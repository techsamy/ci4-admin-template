<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\CIAuth;
use App\Libraries\Hash;
use App\Models\User;
use App\Models\PasswordResetToken;
use Carbon\Carbon;

class AuthController extends BaseController
{
    protected $helpers = ['url', 'form', 'CIMail','CIFunctions'];
    public function loginForm(){
        $data = [
            'pageTitle' => 'Login',
            'validation' => NULL
        ];
        return view('backend/pages/auth/login', $data);
    }

    public function loginHandler(){
        $fieldType = filter_Var($this->request->getVar('login_id'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if ($fieldType == 'email') {
            $isValid = $this->validate([
                'login_id' => [
                    'label' => 'Email',
                    'rules' => 'required|valid_email|is_not_unique[users.email]',
                    'errors' => [
                        'required' => 'Email is required.',
                        'valid_email' => 'Please, check the email field. It is not appears to be valid.',
                        'is_not_unique' => 'Email does not exist in our records.'
                    ]
                ],
                'password' => [
                    'label' => 'Password',
                    'rules' => 'required|min_length[6]|max_length[50]',
                    'errors' => [
                        'required' => 'Password is required.',
                        'min_length' => 'Password must be at least 6 characters long.',
                        'max_length' => 'Password must not exceed 50 characters.'
                    ]
                ]
            ]);
        } else {
            $isValid = $this->validate([
                'login_id' => [
                    'label' => 'Username',
                    'rules' => 'required|is_not_unique[users.username]',
                    'errors' => [
                        'required' => 'Username is required.',
                        'is_not_unique' => 'Username does not exist in our records.'
                    ]
                ],
                'password' => [
                    'label' => 'Password',
                    'rules' => 'required|min_length[6]|max_length[50]',
                    'errors' => [
                        'required' => 'Password is required.',
                        'min_length' => 'Password must be at least 6 characters long.',
                        'max_length' => 'Password must not exceed 50 characters.'
                    ]
                ]
            ]);
        }

        if (!$isValid) {
            // return redirect()->back()->withInput()->with('validation', $this->validator);
            return view('backend/pages/auth/login', [
                'pageTitle' => 'Login',
                'validation' => $this->validator
            ]);
        } else {
            $user = new User();
            $userInfo = $user->where($fieldType, $this->request->getVar('login_id'))->first();
            $check_password = Hash::check($this->request->getVar('password'), $userInfo['password']);
            
            if (!$check_password) {
                return redirect()->route('admin.login.form')->withInput()->with('fail', 'Password is incorrect.');
            } else {
                CIAuth::setCIAuth($userInfo);
                return redirect()->route('admin.home')->with('success', 'You are successfully logged in.');
            }
        }
    }

    public function forgotPasswordForm(){
        $data = [
            'pageTitle' => 'Forgot Password',
            'validation' => NULL
        ];
        return view('backend/pages/auth/forgot-password', $data);
    }

    public function sendPasswordResetLink(){
       $isValid = $this->validate([
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email|is_not_unique[users.email]',
                'errors' => [
                    'required' => 'Email is required.',
                    'valid_email' => 'Please, check the email field. It is not appears to be valid.',
                    'is_not_unique' => 'Email does not exist in our records.'
                ]
            ]
        ]);

        if (!$isValid) {
            return view('backend/pages/auth/forgot-password', [
                'pageTitle' => 'Forgot Password',
                'validation' => $this->validator
            ]);
        } else {
            // get user (Admin) details
            $user = new User();
            $userInfo = $user->asObject()->where('email', $this->request->getVar('email'))->first();

            // generate token
            $token = bin2hex(openssl_random_pseudo_bytes(65));

            // get reset password token
            $passwordResetToken = new PasswordResetToken();
            $isOldTokenExists = $passwordResetToken->asObject()->where('email', $userInfo->email)->first();

            if ($isOldTokenExists) {
                // update Existing token
                $passwordResetToken->where('email', $userInfo->email)
                                    ->set([
                                        'token' => $token,
                                        'created_at' => Carbon::now()
                                    ])
                                    ->update();
                
            } else {
                // create new token
                $passwordResetToken->insert([
                    'email' => $userInfo->email,
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]);
            }  

            // Create action link
            $actionLink = base_url(route_to('admin.reset-password', $token));

            $mailData = [
                'actionLink' => $actionLink,
                'user' => $userInfo,
            ];

            $view = \Config\Services::renderer();
            $mailBody = $view->setVar('mailData', $mailData)->render('email-templates/forgot-email-template');

            $mailConfig = [
                'mail_from_email' => getenv('EMAIL_FROM_ADDRESS'),
                'mail_from_name' => getenv('EMAIL_FROM_NAME'),
                'mail_recipient_email' => $userInfo->email,
                'mail_recipient_name' => $userInfo->name,
                'mail_subject' => 'Reset Password',
                'mail_body' => $mailBody
            ];

            // Send Email
            if (sendEmail($mailConfig)) {
                return redirect()->route('admin.forgot.password.form')->with('success', 'Password reset link has been sent to your email.');
            } else {
                return redirect()->route('admin.forgot.password.form')->with('fail', 'Failed to send password reset link.');
            }  
        }       
    }
    
    public function resetPassword($token){
        $passwordResetToken = new PasswordResetToken();
        $checkToken = $passwordResetToken->asObject()->where('token', $token)->first();

        if (!$checkToken) {
            return redirect()->route('admin.forgot.password.form')->with('fail', 'Invalid or expired token. Request a new password reset link.');
        } else {
            // Token is valid, proceed with showing the reset password form
            $diffMins = Carbon::createFromFormat('Y-m-d H:i:s', $checkToken->created_at)->diffInMinutes(Carbon::now());

            if ($diffMins > 6000) {
                // Token has expired (Older than 60 minutes)
                return redirect()->route('admin.forgot.password.form')->with('fail', 'Token has expired. Request a new password reset link.');
            } else {
                // Token is valid and not expired
                return view('backend/pages/auth/reset-password', [
                    'pageTitle' => 'Reset Password',
                    'validation' => NULL,
                    'token' => $token
                ]);
            }

        }
    }

    public function resetPasswordHandler($token){
        $isValid = $this->validate([
            'new_password' => [
                'label' => 'New Password',
                'rules' => 'required|min_length[5]|max_length[20]|is_password_strong[new_password]',
                'errors' => [
                    'required' => 'New Password is required.',
                    'min_length' => 'Password must be at least 5 characters long.',
                    'max_length' => 'Password must not exceed 20 characters.',
                    'is_password_strong' => 'Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.'
                ]
            ],
            'confirm_password' => [
                'label' => 'Confirm Password',
                'rules' => 'required|matches[new_password]',
                'errors' => [
                    'required' => 'Confirm Password is required.',
                    'matches' => 'Confirm Password must match New Password.'
                ]
            ]
        ]);

        if (!$isValid) {
            return view('backend/pages/auth/reset-password', [
                'pageTitle' => 'Reset Password',
                'validation' => null,
                'token' => $token
            ]);
        } else {
            // Validation passes, proceed with password reset
            $passwordResetToken = new PasswordResetToken();
            $getToken = $passwordResetToken->asObject()->where('token', $token)->first();

            // Get user (admin) details
            $user = new User();
            $userInfo = $user->asObject()->where('email', $getToken->email)->first();

            if (!$userInfo) {
                return redirect()->back()->with('fail', 'Invalid token. User not found.')->withInput();
            } else {
                // Update admin password in the database
                $user->where('email', $getToken->email)
                     ->set(['password' => Hash::make($this->request->getVar('new_password'))])
                     ->update();

                // send notification email to user (Admin) Email Address
                $mailData = [
                    'user' => $userInfo,
                    'new_password' => $this->request->getVar('new_password')
                ];

                $view = \Config\Services::renderer();
                $mailBody = $view->setVar('mailData', $mailData)->render('email-templates/password-changed-email-template');

                $mailConfig = [
                    'mail_from_email' => getenv('EMAIL_FROM_ADDRESS'),
                    'mail_from_name' => getenv('EMAIL_FROM_NAME'),
                    'mail_recipient_email' => $userInfo->email,
                    'mail_recipient_name' => $userInfo->name,
                    'mail_subject' => 'Password Changed Successfully',
                    'mail_body' => $mailBody
                ];

                // Send Email
                if (sendEmail($mailConfig)) {
                    // Delete the token after successful password reset
                    $passwordResetToken->where('email', $userInfo->email)->delete();

                    // Redirect to login page with success message
                    return redirect()->route('admin.login.form')->with('success', 'Password has been reset successfully. You can now log in with your new password.');
                } else {
                    return redirect()->route('admin.forgot.password.form')->with('fail', 'Failed to send password reset link.');
                    return redirect()->back()->with('fail', 'Failed to send password reset link.')->withInput();
                }  
            }
        }

        // If validation passes, proceed with password reset
    }
}
