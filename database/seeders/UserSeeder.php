<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find roles by slug (ensure RolesSeeder ran first)
        $adminRole = Role::where('slug', 'admin')->first();
        $agencyRole = Role::where('slug', 'agency')->first();
        $consultantRole = Role::where('slug', 'consultant')->first();
        $userRole = Role::where('slug', 'user')->first();

        // Create Admin User
        if ($adminRole) {
            $admin = User::firstOrCreate(
                ['email' => 'admin@bgplatform.com'],
                [
                    'name' => 'Admin User',
                    'password' => Hash::make('password'),
                    'role_id' => $adminRole->id,
                    'email_verified_at' => now(),
                ]
            );
            
            // Create profile and wallet if they don't exist
            if (!$admin->profile) {
                UserProfile::create(['user_id' => $admin->id]);
            }
            if (!$admin->wallet) {
                Wallet::create([
                    'user_id' => $admin->id,
                    'balance' => 0,
                    'currency' => 'BDT',
                ]);
            }
        }

        // Create Agency User
        if ($agencyRole) {
            $agency = User::firstOrCreate(
                ['email' => 'agency@bgplatform.com'],
                [
                    'name' => 'Agency User',
                    'password' => Hash::make('password'),
                    'role_id' => $agencyRole->id,
                    'email_verified_at' => now(),
                ]
            );
            
            if (!$agency->profile) {
                UserProfile::create(['user_id' => $agency->id]);
            }
            if (!$agency->wallet) {
                Wallet::create([
                    'user_id' => $agency->id,
                    'balance' => 0,
                    'currency' => 'BDT',
                ]);
            }
        }

        // Create Consultant User
        if ($consultantRole) {
            $consultant = User::firstOrCreate(
                ['email' => 'consultant@bgplatform.com'],
                [
                    'name' => 'Consultant User',
                    'password' => Hash::make('password'),
                    'role_id' => $consultantRole->id,
                    'email_verified_at' => now(),
                ]
            );
            
            if (!$consultant->profile) {
                UserProfile::create(['user_id' => $consultant->id]);
            }
            if (!$consultant->wallet) {
                Wallet::create([
                    'user_id' => $consultant->id,
                    'balance' => 0,
                    'currency' => 'BDT',
                ]);
            }
        }

        // Create Regular User
        if ($userRole) {
            $testUser = User::firstOrCreate(
                ['email' => 'user@bgplatform.com'],
                [
                    'name' => 'Test User',
                    'password' => Hash::make('password'),
                    'role_id' => $userRole->id,
                    'email_verified_at' => now(),
                ]
            );
            
            if (!$testUser->profile) {
                UserProfile::create(['user_id' => $testUser->id]);
            }
            if (!$testUser->wallet) {
                Wallet::create([
                    'user_id' => $testUser->id,
                    'balance' => 5000.00,
                    'currency' => 'BDT',
                ]);
            }
        }

        $this->command->info('✓ Created 4 test users with profiles and wallets');
    }
}
