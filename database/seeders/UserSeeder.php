<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Agency; // <-- ADDED
use App\Models\UserProfile; // <-- ADDED

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find roles
        $adminRole = Role::where('name', 'admin')->first();
        $agencyRole = Role::where('name', 'agency')->first(); // <-- ADDED
        $consultantRole = Role::where('name', 'consultant')->first(); // <-- ADDED
        $userRole = Role::where('name', 'user')->first();

        // 1. Create Admin User
        if ($adminRole) {
            $adminUser = User::firstOrCreate(
                ['email' => 'admin@bideshgomon.com'],
                [
                    'name' => 'Admin User',
                    'password' => Hash::make('password'),
                    'role_id' => $adminRole->id,
                    'email_verified_at' => now(),
                ]
            );
            // Create profile for admin
            UserProfile::firstOrCreate(['user_id' => $adminUser->id]);
        }

        // 2. Create Agency Owner User
        if ($agencyRole) {
            $agencyUser = User::firstOrCreate(
                ['email' => 'agency@bideshgomon.com'],
                [
                    'name' => 'Agency Owner',
                    'password' => Hash::make('password'),
                    'role_id' => $agencyRole->id,
                    'email_verified_at' => now(),
                ]
            );
            // Create profile for agency owner
            UserProfile::firstOrCreate(['user_id' => $agencyUser->id]);
            
            // Create the Agency Profile itself, linked to this user
            Agency::firstOrCreate(
                ['owner_id' => $agencyUser->id],
                [
                    'name' => 'BideshGomon Agency',
                    'status' => 'approved',
                    'country' => 'Bangladesh',
                    'city' => 'Dhaka',
                ]
            );
        }

        // 3. Create Consultant User
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
            // Create profile for consultant
            UserProfile::firstOrCreate(['user_id' => $consultantUser->id]);
            
            // Optional: Link this consultant to the agency we just created
            $agency = Agency::first();
            if ($agency) {
                // This uses the 'consultants' relationship in the Agency model
                $agency->consultants()->syncWithoutDetaching($consultantUser->id);
            }
        }

        // 4. Create a Test User
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
            // Create profile for test user
            UserProfile::firstOrCreate(['user_id' => $testUser->id]);
        }
    }
}