<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Seed the superadmin user.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'info@noteds.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Wahyu123456789@'),
                'role' => 'superadmin',
                'email_verified_at' => now(),
            ]
        );
    }
}
