<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Staff account used to log in to the management pages.
        // The 'hashed' cast on the User model hashes the password automatically.
        User::updateOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'Staff Admin',
                'password' => 'password',
                'email_verified_at' => now(),
            ]
        );
    }
}
