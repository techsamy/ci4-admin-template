<?php

namespace App\Validation;
use App\Libraries\CIAuth;
use App\Libraries\Hash;
use App\Models\User;


class IsCurrentPasswordCorrect
{
    public function check_current_password($password): bool
    {
        $password  = trim($password);
        $userID    = CIAuth::id();
        $user = new User();

        $userID = $user->asObject()->where('id', $userID)->first();

        if ( !Hash::check($password, $userID->password) ) {
            return false; // Password does not match
        }

        return true;
    }
}
