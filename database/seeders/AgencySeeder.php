<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Agency;
use App\Models\Role;

class AgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find user with agency role
        $agencyRole = Role::where('slug', 'agency')->first();
        $agencyUser = User::where('role_id', $agencyRole->id)->first();

        if ($agencyUser) {
            // Create agency profile for existing agency user
            Agency::create([
                'user_id' => $agencyUser->id,
                'name' => 'BideshGomon Travel Agency',
                'company_name' => 'BideshGomon Travel Services Ltd.',
                'registration_number' => 'REG-2025-001',
                'phone' => '+880 1700-000000',
                'email' => $agencyUser->email,
                'address' => 'House 123, Road 45, Gulshan-1',
                'city' => 'Dhaka',
                'country' => 'Bangladesh',
                'postal_code' => '1212',
                'description' => 'Leading visa and travel services provider in Bangladesh',
                'website' => 'https://bideshgomon.com',
                'commission_rate' => 15.00,
                'is_verified' => true,
                'is_active' => true,
                'verified_at' => now(),
            ]);

            $this->command->info('Agency profile created for user: ' . $agencyUser->email);
        } else {
            $this->command->warn('No agency user found. Please create a user with agency role first.');
        }
    }
}
