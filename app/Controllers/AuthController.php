<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\CIAuth;
use App\Libraries\Hash;
use App\Models\User;

class AuthController extends BaseController
{
    protected $helpers = ['form', 'url'];
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
}
