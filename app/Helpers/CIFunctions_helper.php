<?php 

use App\Libraries\CIAuth;
use App\Models\User;
use App\Models\Setting;

if ( !function_exists('getUser') ) {
    function getUser() {
        if (CIAuth::isLoggedIn()) {
            $user = new User();
            return $user->asObject()->where('id', CIAuth::id())->first();
        } else {
            return null;
        }
    }
}

if ( !function_exists('getSettings') ) {
    function getSettings() {
        $settings = new Setting();
        $settingData =  $settings->asObject()->first();

        if ( !$settingData ) {
            // If no settings found, return an empty array
            $data = [
                'website_title' => 'Website Title',
                'website_email' => 'website_email@mail.com',
                'website_phone' => null,
                'website_meta_keywords' => null,
                'website_meta_description' => null,
                'website_logo' => null,
                'website_favicon' => null
            ];
            $settings->save($data);
            $newSettingData = $settings->asObject()->first();
            return $newSettingData;
        } else {
            // Convert the object to an associative array
            // return (array) $settingData;
            return $settingData;
        }  
    }   
}