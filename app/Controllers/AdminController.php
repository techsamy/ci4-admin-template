<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\CIAuth;
use App\Models\User;
use App\Libraries\Hash;
use App\Models\Setting;

class AdminController extends BaseController
{
    protected $helpers = ['url', 'form', 'CIMail','CIFunctions'];

    public function index(){
        $data = [
            'pageTitle' => 'Admin Dashboard'
        ];
        
        return view('backend/pages/home', $data);
    }

    public function logoutHandler(){
        CIAuth::forget();
        return redirect()->route('admin.login.form')->with('fail', 'You are logged out!');
    }

    public function profile(){
        $data = [
            'pageTitle' => 'Profile'
        ];
        return view('backend/pages/profile', $data);
    }

    /**
     * @throws \ReflectionException
     */
    public function updatePersonalDetails(){
        $request = \Config\Services::request();
        $validation = \Config\Services::validation();
        $userID = CIAuth::id();

        if ( $request->isAJAX() ){
            $this->validate([
                'name' => [
                    'label' => 'Full Name',
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Please enter your {field}.'
                    ]
                ],
                'username' => [
                    'label' => 'Username',
                    'rules' => 'required|min_length[4]|is_unique[users.username,id,'.$userID.']',
                    'errors' => [
                        'required' => '{field} is required', 
                        'min_length' => 'Username must be at least 4 characters long',
                        'is_unique' => '{field} already exists.'
                    ]   
                ],
                'bio' => [
                    'label' => 'Bio',
                    'rules' => 'permit_empty|max_length[255]',
                    'errors' => [
                        'max_length' => '{field} must not exceed 255 characters.'
                    ]
                ]
            ]);

            if ( $validation->run() == FALSE ){
                $errors = $validation->getErrors();
                return json_encode([
                    'status' => 0,
                    'error' => $errors
                ]);
            } else {
                $user = new User();
                $update = $user->where('id', $userID)->set([
                    'name' => $request->getVar('name'),
                    'username' => $request->getVar('username'),
                    'bio' => $request->getVar('bio')
                ])->update();

                if ( $update ){
                    $userInfo = $user->find($userID);
                    return json_encode([
                        'status' => 1,
                        'user_info' => $userInfo,
                        'msg' => 'Profile updated successfully !!'
                    ]);
                } else {
                    return json_encode([
                        'status' => 0,
                        'msg' => 'Database error occurred while updating profile.'
                    ]);
                }
            }
        }
    }

    public function updateProfilePicture(){
        $request = \Config\Services::request();
        $userID = CIAuth::id();

        $user = new User();
        $userInfo = $user->asObject()->where('id', $userID)->first();

        $path = 'images/users/';
        $file = $request->getFile('user_profile_file');
        $oldPicture = $userInfo->picture;
        $newPicture = 'UIMG_' . $userID . $file->getRandomName();

        /* if ( $file->move($path, $newPicture) ){
            if ( $oldPicture != null && file_exists($path . $oldPicture) ){
                unlink($path . $oldPicture);
            }
            $user->where('id', $userID)
                ->set(['picture' => $newPicture])
                ->update();
            echo json_encode([
                'status' => 1,
                'msg' => 'Profile picture updated successfully.'
            ]);
        } else {
            return json_encode([
                'status' => 0,
                'msg' => 'Database error occurred while updating profile picture.'
            ]);
        } */

        // Image Manipulation
        $uploadedImage = \Config\Services::image()
            ->withFile($file)
            ->fit(450, 450, true, 'height')
            ->save($path . $newPicture);

        if ( !$uploadedImage ){
            return json_encode([
                'status' => 0,
                'msg' => 'Error occurred while uploading the image.'
            ]);
        } else {
            // If the image is uploaded successfully, delete the old picture if it exists
            // and update the database with the new picture name.   
            if ( $oldPicture != null && file_exists($path . $oldPicture) ){
                unlink($path . $oldPicture);
            }
            $user->where('id', $userID)
                ->set(['picture' => $newPicture])
                ->update();
            echo json_encode([
                'status' => 1,
                'msg' => 'Profile picture updated successfully.'
            ]);
        }  
    }

    /**
     * @throws \ReflectionException
     */
    public function changePassword(){
        $request = \Config\Services::request();

        if ( $request->isAJAX() ){ 
            $validation = \Config\Services::validation();
            $userID = CIAuth::id();
            $user = new User();
            $userInfo = $user->asObject()->where('id', $userID)->first();

            $this->validate([
                'current_password' => [
                    'label' => 'Current Password',
                    'rules' => 'required|min_length[5]|check_current_password[current_password]',
                    'errors' => [
                        'required' => '{field} is required',
                        'min_length' => '{field} must be at least 5 characters long',
                        'check_current_password' => 'Current password is incorrect.'
                    ]
                ],
                'new_password' => [
                    'label' => 'New Password',
                    'rules' => 'required|min_length[5]|max_length[20]|is_password_strong[new_password]',
                    'errors' => [
                        'required' => '{field} is required',
                        'min_length' => '{field} must be at least 5 characters long',
                        'max_length' => '{field} must not exceed 20 characters',
                        'is_password_strong' => 'Password must be contains at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.'
                    ]
                ],
                'confirm_new_password' => [
                    'label' => 'Confirm Password',
                    'rules' => 'required|matches[new_password]',
                    'errors' => [
                        'required' => '{field} is required',
                        'matches' => '{field} must match the new password.'
                    ]
                ]
            ]);

             if ( $validation->run() === FALSE ){
                $errors = $validation->getErrors();
                return $this->response->setJSON([
                    'status' => 0,
                    'token' => csrf_hash(),
                    'error' => $errors
                ]);
            } else {
                // Update the password in the database
                $user->where('id', $userID)->set([
                    'password' => Hash::make($request->getVar('new_password'))
                ])->update();

                // send notification email to user (Admin) Email Address
                $mailData = [
                    'user' => $userInfo,
                    'new_password' => $request->getVar('new_password')
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

                sendEmail($mailConfig);
                return $this->response->setJSON([
                    'status' => 1,
                    'token' => csrf_hash(),
                    'msg' => 'Password changed successfully !!'
                ]);
            }

        }
    }

    public function settings(){
        $data = [
            'pageTitle' => 'Settings'
        ];

        return view('backend/pages/settings', $data);
    }

    public function updateGeneralSettings(){
        $request = \Config\Services::request();
        
        if ( $request->isAJAX() ){
            $validation = \Config\Services::validation();

            $this->validate([
                'website_title' => [
                    'label' => 'Website Name',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} is required'
                    ]
                ],
                'website_email' => [
                    'label' => 'Website Email',
                    'rules' => 'required|valid_email',
                    'errors' => [
                        'required' => '{field} is required',
                        'valid_email' => '{field} must be a valid email address.'
                    ]
                ]
            ]);

            if ( $validation->run() == FALSE ){
                $errors = $validation->getErrors();
                return json_encode([
                    'status' => 0,
                    'token' => csrf_hash(),
                    'error' => $errors
                ]);
            } else {
                $settings = new Setting();
                $setting_id = $settings->asObject()->first()->id;
                $update = $settings->where('id', $setting_id)
                    ->set([
                    'website_title'            => $request->getVar('website_title'),
                    'website_email'            => $request->getVar('website_email'),
                    'website_phone'            => $request->getVar('website_phone'),
                    'website_meta_keywords'    => $request->getVar('website_meta_keywords'),
                    'website_meta_description' => $request->getVar('website_meta_description')
                ])->update();

                if ( $update ){
                    return json_encode([
                        'status' => 1,
                        'token' => csrf_hash(),
                        'msg' => 'General settings updated successfully !!'
                    ]);
                } else {
                    return json_encode([
                        'status' => 0,
                        'token' => csrf_hash(),
                        'msg' => 'Database error occurred while updating general settings.'
                    ]);
                }
            }
        }
    }
}
