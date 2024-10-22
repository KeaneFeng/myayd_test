<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $admin = User::create([
            'nickname' => 'Super Admin',
            'account' => 'admin',
            'email' => 'SuperAdmin@example.com',
            'role' => 'admin',
            'password' => bcrypt('admin'),
            'status' => 1,
        ]);

        $user = User::create([
            'nickname' => 'Test User',
            'account' => 'test_user',
            'email' => 'TestUser@example.com',
            'role' => 'user',
            'password' => bcrypt('123456'),
            'status' => 1,
        ]);
    }

}
