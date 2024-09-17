<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Create an admin user with predefined credentials
        Admin::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password123'), // Replace with a secure password
            'role' => 'admin',
        ]);

        // Optionally, you can create more admin users or use factories for more diverse data
    }
}