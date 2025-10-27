<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\UserProfile; // <-- [PATCH] IMPORT UserProfile MODEL

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find roles by slug for consistency with new RoleSeeder
        $adminRole = Role::where('slug', 'admin')->first();
        $userRole = Role::where('slug', 'user')->first();

        // 4. Create Admin User (only if the role was found)
        if ($adminRole) {
            User::firstOrCreate(
                ['email' => 'admin@bideshgomon.com'],
                [
                    'name' => 'Admin User',
                    'password' => Hash::make('password'),
                    'role_id' => $adminRole->id,
                    'email_verified_at' => now(),
                    'is_active' => true, // <-- [RECOMMENDATION] Set default active status
                ]
            );
            // Note: Admin user might also need a profile, but bug was specific to test user.
        }

        // 5. Create a Test User (only if the role was found)
        if ($userRole) {
            $testUser = User::firstOrCreate( // <-- [PATCH] Assign to $testUser variable
                ['email' => 'user@bideshgomon.com'],
                [
                    'name' => 'Test User',
                    'password' => Hash::make('password'),
                    'role_id' => $userRole->id,
                    'email_verified_at' => now(),
                    'is_active' => true, // <-- [RECOMMENDATION] Set default active status
                ]
            );

            // --- [PATCH START] ---
            // Create a corresponding profile for the test user
            UserProfile::firstOrCreate(['user_id' => $testUser->id]);
            // --- [PATCH END] ---
        }
    }
}