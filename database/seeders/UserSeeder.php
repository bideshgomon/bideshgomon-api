<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role; // <-- 1. IMPORT THE ROLE MODEL

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 2. Find the 'admin' role from the database
        $adminRole = Role::where('name', 'admin')->first();
        
        // 3. Find the 'user' role from the database
        $userRole = Role::where('name', 'user')->first();

        // 4. Create Admin User (only if the role was found)
        if ($adminRole) {
            User::firstOrCreate(
                ['email' => 'admin@bideshgomon.com'],
                [
                    'name' => 'Admin User',
                    'password' => Hash::make('password'),
                    'role_id' => $adminRole->id,
                    'email_verified_at' => now(),
                ]
            );
        }

        // 5. Create a Test User (only if the role was found)
        if ($userRole) {
            User::firstOrCreate(
                ['email' => 'user@bideshgomon.com'],
                [
                    'name' => 'Test User',
                    'password' => Hash::make('password'),
                    'role_id' => $userRole->id,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}