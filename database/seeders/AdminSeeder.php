<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Create a default admin
        Admin::create([
            'username' => 'admin',
            'password' => Hash::make('password123'), // Default password
        ]);
    }
}