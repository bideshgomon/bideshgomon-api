<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\UserProfile;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find roles by slug (ensure RoleSeeder ran first)
        $adminRole = Role::where('slug', 'admin')->first();
        $agencyRole = Role::where('slug', 'agency')->first();
        $consultantRole = Role::where('slug', 'consultant')->first();
        $userRole = Role::where('slug', 'user')->first();

        // Create Admin User
        if ($adminRole) {
            User::firstOrCreate(
                ['email' => 'admin@bideshgomon.com'],
                [
                    'name' => 'Admin User',
                    'password' => Hash::make('password'),
                    'role_id' => $adminRole->id,
                    'email_verified_at' => now(),
                    // 'is_active' => true, // Assuming default is true or handled elsewhere
                ]
            );
        }

        // Create Agency User
        if ($agencyRole) {
           $agencyUser = User::firstOrCreate(
                ['email' => 'agency@bideshgomon.com'],
                [
                    'name' => 'Agency User',
                    'password' => Hash::make('password'),
                    'role_id' => $agencyRole->id,
                    'email_verified_at' => now(),
                ]
            );
            // Create profile if Agency needs one
            UserProfile::firstOrCreate(['user_id' => $agencyUser->id]);
        }

        // Create Consultant User
        if ($consultantRole) {
            $consultantUser = User::firstOrCreate(
                ['email' => 'consultant@bideshgomon.com'],
                [
                    'name' => 'Consultant User',
                    'password' => Hash::make('password'),
                    'role_id' => $consultantRole->id,
                    'email_verified_at' => now(),
                ]
            );
             // Create profile if Consultant needs one
            UserProfile::firstOrCreate(['user_id' => $consultantUser->id]);
        }

        // Create Regular User
        if ($userRole) {
            $testUser = User::firstOrCreate(
                ['email' => 'user@bideshgomon.com'],
                [
                    'name' => 'Test User',
                    'password' => Hash::make('password'),
                    'role_id' => $userRole->id,
                    'email_verified_at' => now(),
                ]
            );
            // Create profile for the user
            UserProfile::firstOrCreate(['user_id' => $testUser->id]);
        }
    }
}