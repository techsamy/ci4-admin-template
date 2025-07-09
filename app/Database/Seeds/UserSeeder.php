<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
                'name'     => 'Admin',
                'username' => 'admin',
                'email'    => 'admin@gmail.com',  // Use a valid email format
                'password' => password_hash('password123', PASSWORD_DEFAULT), // Hash the password  
        ];
        
        // Insert the user data into the 'users' table
        $this->db->table('users')->insert($data);

    }
}
