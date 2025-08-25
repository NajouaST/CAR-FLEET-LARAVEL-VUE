<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('12345678'),
                'is_active' => true,
            ]
        );

        // Create mod user
        $moderatorUser = User::firstOrCreate(
            ['email' => 'moderator@example.com'],
            [
                'name' => 'Moderator User',
                'password' => Hash::make('12345678'),
                'is_active' => true,
            ]
        );

        // Assign admin role
        if (!$adminUser->hasRole('Admin')) {
            $adminUser->assignRole('Admin');
        }

        // Assign moderator role
        if (!$moderatorUser->hasRole('Moderator')) {
            $moderatorUser->assignRole('Moderator');
        }
    }
}
