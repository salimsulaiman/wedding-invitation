<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            [
                'username' => 'admin',
            ],
            [
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'phone' => '081234567890',
                'role' => 'admin',
                'avatar' => null,
                'is_active' => true,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            [
                'username' => 'user',
            ],
            [
                'name' => 'User',
                'email' => 'user@example.com',
                'phone' => '081234567891',
                'role' => 'user',
                'avatar' => null,
                'is_active' => true,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}