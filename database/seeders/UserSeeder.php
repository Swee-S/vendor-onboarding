<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin User',
                'email'    => 'admin@example.com',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        // User 1
        User::updateOrCreate(
            ['email' => 'user1@example.com'],
            [
                'name'     => 'User One',
                'email'    => 'user1@example.com',
                'password' => Hash::make('password'),
                'role'     => 'user',
            ]
        );

        // User 2
        User::updateOrCreate(
            ['email' => 'user2@example.com'],
            [
                'name'     => 'User Two',
                'email'    => 'user2@example.com',
                'password' => Hash::make('password'),
                'role'     => 'user',
            ]
        );

        // User 3
        User::updateOrCreate(
            ['email' => 'user3@example.com'],
            [
                'name'     => 'User Three',
                'email'    => 'user3@example.com',
                'password' => Hash::make('password'),
                'role'     => 'user',
            ]
        );
    }
}