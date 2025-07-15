<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\CIAuth;

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
}
