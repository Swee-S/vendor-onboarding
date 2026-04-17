<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        User::create([
            'name' => 'User1',
            'email' => 'user1@example.com',
            'password' => Hash::make('password'),
            'role' => 'user'
        ]);

        User::create([
            'name' => 'User2',
            'email' => 'user2@example.com',
            'password' => Hash::make('password'),
            'role' => 'user'
        ]);

        User::create([
            'name' => 'User3',
            'email' => 'user3@example.com',
            'password' => Hash::make('password'),
            'role' => 'user'
        ]);
    }
}