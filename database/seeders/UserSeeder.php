<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@lms.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'student@lms.com'],
            [
                'name' => 'John Student',
                'password' => Hash::make('password'),
                'role' => 'student',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Users seeded: admin@lms.com / student@lms.com (password: password)');
    }
}