<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\CIAuth;
use App\Models\User;

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
}
