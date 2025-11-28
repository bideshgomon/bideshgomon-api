<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Agency;
use App\Models\Role;
use App\Models\ServiceModule;
use App\Models\Country;
use App\Models\VisaType;
use App\Models\AgencyCountryAssignment;
use Illuminate\Support\Facades\Hash;

class AgencyTestSeeder extends Seeder
{
    /**
     * Seed test agencies with diverse assignments.
     */
    public function run(): void
    {
        $agencyRole = Role::where('slug', 'agency')->first();
        
        if (!$agencyRole) {
            $this->command->error('Agency role not found. Please run RoleSeeder first.');
            return;
        }

        // Get service modules
        $touristVisa = ServiceModule::where('slug', 'tourist-visa')->first();
        $studentVisa = ServiceModule::where('slug', 'student-visa')->first();
        $workVisa = ServiceModule::where('slug', 'work-visa')->first();
        $flightBooking = ServiceModule::where('slug', 'flight-booking')->first();
        $hotelBooking = ServiceModule::where('slug', 'hotel-booking')->first();

        // Get countries
        $malaysia = Country::where('iso_code_2', 'MY')->first();
        $thailand = Country::where('iso_code_2', 'TH')->first();
        $singapore = Country::where('iso_code_2', 'SG')->first();
        $india = Country::where('iso_code_2', 'IN')->first();
        $uk = Country::where('iso_code_2', 'GB')->first();
        $usa = Country::where('iso_code_2', 'US')->first();

        // Get visa types
        $touristVisaType = VisaType::where('slug', 'tourist')->first();

        // Agency 1: BideshGomon Travel (existing - update if exists)
        $agency1User = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'BideshGomon Travel Agency',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'role_id' => $agencyRole->id,
                'email_verified_at' => now(),
            ]
        );

        $agency1 = Agency::updateOrCreate(
            ['user_id' => $agency1User->id],
            [
                'name' => 'BideshGomon Travel Agency',
                'company_name' => 'BideshGomon Travel Services Ltd.',
                'registration_number' => 'REG-2025-001',
                'phone' => '+880 1700-000000',
                'email' => 'test@example.com',
                'address' => '123 Gulshan Avenue',
                'city' => 'Dhaka',
                'country' => 'Bangladesh',
                'postal_code' => '1212',
                'description' => 'Leading travel agency specializing in tourist visa services for Southeast Asia',
                'website' => 'https://bideshgomon.com',
                'commission_rate' => 15.00,
                'is_verified' => true,
                'is_active' => true,
                'verified_at' => now(),
            ]
        );

        // Assign Agency 1 to Tourist Visa - Multiple Countries
        if ($touristVisa && $malaysia && $touristVisaType) {
            AgencyCountryAssignment::updateOrCreate(
                [
                    'agency_id' => $agency1->id,
                    'service_module_id' => $touristVisa->id,
                    'country_id' => $malaysia->id,
                ],
                [
                    'country' => $malaysia->name,
                    'country_code' => $malaysia->iso_code_2,
                    'visa_type_id' => $touristVisaType->id,
                    'visa_type' => $touristVisaType->name,
                    'assignment_scope' => 'visa_specific',
                    'platform_commission_rate' => 15.00,
                    'commission_type' => 'percentage',
                    'can_edit_requirements' => true,
                    'can_set_fees' => true,
                    'can_process_applications' => true,
                    'is_active' => true,
                    'assigned_at' => now(),
                    'assigned_by' => 1, // Admin
                    'assignment_notes' => 'Primary agency for Malaysia tourist visas',
                ]
            );
        }

        if ($touristVisa && $thailand && $touristVisaType) {
            AgencyCountryAssignment::updateOrCreate(
                [
                    'agency_id' => $agency1->id,
                    'service_module_id' => $touristVisa->id,
                    'country_id' => $thailand->id,
                ],
                [
                    'country' => $thailand->name,
                    'country_code' => $thailand->iso_code_2,
                    'visa_type_id' => $touristVisaType->id,
                    'visa_type' => $touristVisaType->name,
                    'assignment_scope' => 'visa_specific',
                    'platform_commission_rate' => 15.00,
                    'commission_type' => 'percentage',
                    'can_edit_requirements' => true,
                    'can_set_fees' => true,
                    'can_process_applications' => true,
                    'is_active' => true,
                    'assigned_at' => now(),
                    'assigned_by' => 1,
                    'assignment_notes' => 'Handles Thailand tourist visa applications',
                ]
            );
        }

        if ($touristVisa && $singapore && $touristVisaType) {
            AgencyCountryAssignment::updateOrCreate(
                [
                    'agency_id' => $agency1->id,
                    'service_module_id' => $touristVisa->id,
                    'country_id' => $singapore->id,
                ],
                [
                    'country' => $singapore->name,
                    'country_code' => $singapore->iso_code_2,
                    'visa_type_id' => $touristVisaType->id,
                    'visa_type' => $touristVisaType->name,
                    'assignment_scope' => 'visa_specific',
                    'platform_commission_rate' => 12.00,
                    'commission_type' => 'percentage',
                    'can_edit_requirements' => false,
                    'can_set_fees' => true,
                    'can_process_applications' => true,
                    'is_active' => true,
                    'assigned_at' => now(),
                    'assigned_by' => 1,
                    'assignment_notes' => 'Singapore tourist visa - limited permissions',
                ]
            );
        }

        // Agency 2: Global Education Consultancy
        $agency2User = User::updateOrCreate(
            ['email' => 'global.edu@example.com'],
            [
                'name' => 'Global Education Consultancy',
                'email' => 'global.edu@example.com',
                'password' => Hash::make('password'),
                'role_id' => $agencyRole->id,
                'email_verified_at' => now(),
            ]
        );

        $agency2 = Agency::updateOrCreate(
            ['user_id' => $agency2User->id],
            [
                'name' => 'Global Education Consultancy',
                'company_name' => 'Global Education Services Pvt. Ltd.',
                'registration_number' => 'REG-2025-002',
                'phone' => '+880 1800-111111',
                'email' => 'global.edu@example.com',
                'address' => '456 Banani Road',
                'city' => 'Dhaka',
                'country' => 'Bangladesh',
                'postal_code' => '1213',
                'description' => 'Specialized in student visa processing for UK, USA, and Australia',
                'website' => 'https://globaledu.com',
                'commission_rate' => 18.00,
                'is_verified' => true,
                'is_active' => true,
                'verified_at' => now(),
            ]
        );

        // Assign Agency 2 to Student Visa - UK
        if ($studentVisa && $uk) {
            AgencyCountryAssignment::updateOrCreate(
                [
                    'agency_id' => $agency2->id,
                    'service_module_id' => $studentVisa->id,
                    'country_id' => $uk->id,
                ],
                [
                    'country' => $uk->name,
                    'country_code' => $uk->iso_code_2,
                    'visa_type_id' => null,
                    'visa_type' => 'Student',
                    'assignment_scope' => 'country_specific',
                    'platform_commission_rate' => 18.00,
                    'commission_type' => 'percentage',
                    'can_edit_requirements' => true,
                    'can_set_fees' => true,
                    'can_process_applications' => true,
                    'is_active' => true,
                    'assigned_at' => now(),
                    'assigned_by' => 1,
                    'assignment_notes' => 'Expert in UK student visa applications',
                ]
            );
        }

        // Assign Agency 2 to Student Visa - USA
        if ($studentVisa && $usa) {
            AgencyCountryAssignment::updateOrCreate(
                [
                    'agency_id' => $agency2->id,
                    'service_module_id' => $studentVisa->id,
                    'country_id' => $usa->id,
                ],
                [
                    'country' => $usa->name,
                    'country_code' => $usa->iso_code_2,
                    'visa_type_id' => null,
                    'visa_type' => 'Student',
                    'assignment_scope' => 'country_specific',
                    'platform_commission_rate' => 20.00,
                    'commission_type' => 'percentage',
                    'can_edit_requirements' => true,
                    'can_set_fees' => true,
                    'can_process_applications' => true,
                    'is_active' => true,
                    'assigned_at' => now(),
                    'assigned_by' => 1,
                    'assignment_notes' => 'USA student visa specialist',
                ]
            );
        }

        // Agency 3: Work Permit Experts
        $agency3User = User::updateOrCreate(
            ['email' => 'workpermit@example.com'],
            [
                'name' => 'Work Permit Experts',
                'email' => 'workpermit@example.com',
                'password' => Hash::make('password'),
                'role_id' => $agencyRole->id,
                'email_verified_at' => now(),
            ]
        );

        $agency3 = Agency::updateOrCreate(
            ['user_id' => $agency3User->id],
            [
                'name' => 'Work Permit Experts',
                'company_name' => 'Work Permit Solutions Ltd.',
                'registration_number' => 'REG-2025-003',
                'phone' => '+880 1900-222222',
                'email' => 'workpermit@example.com',
                'address' => '789 Uttara Sector 7',
                'city' => 'Dhaka',
                'country' => 'Bangladesh',
                'postal_code' => '1230',
                'description' => 'Professional work visa and permit processing services',
                'website' => 'https://workpermitexperts.com',
                'commission_rate' => 20.00,
                'is_verified' => true,
                'is_active' => true,
                'verified_at' => now(),
            ]
        );

        // Assign Agency 3 to Work Visa - Multiple Countries
        if ($workVisa && $singapore) {
            AgencyCountryAssignment::updateOrCreate(
                [
                    'agency_id' => $agency3->id,
                    'service_module_id' => $workVisa->id,
                    'country_id' => $singapore->id,
                ],
                [
                    'country' => $singapore->name,
                    'country_code' => $singapore->iso_code_2,
                    'visa_type_id' => null,
                    'visa_type' => 'Work',
                    'assignment_scope' => 'country_specific',
                    'platform_commission_rate' => 20.00,
                    'commission_type' => 'percentage',
                    'can_edit_requirements' => true,
                    'can_set_fees' => true,
                    'can_process_applications' => true,
                    'is_active' => true,
                    'assigned_at' => now(),
                    'assigned_by' => 1,
                    'assignment_notes' => 'Singapore employment pass specialist',
                ]
            );
        }

        if ($workVisa && $malaysia) {
            AgencyCountryAssignment::updateOrCreate(
                [
                    'agency_id' => $agency3->id,
                    'service_module_id' => $workVisa->id,
                    'country_id' => $malaysia->id,
                ],
                [
                    'country' => $malaysia->name,
                    'country_code' => $malaysia->iso_code_2,
                    'visa_type_id' => null,
                    'visa_type' => 'Work',
                    'assignment_scope' => 'country_specific',
                    'platform_commission_rate' => 18.00,
                    'commission_type' => 'percentage',
                    'can_edit_requirements' => true,
                    'can_set_fees' => true,
                    'can_process_applications' => true,
                    'is_active' => true,
                    'assigned_at' => now(),
                    'assigned_by' => 1,
                    'assignment_notes' => 'Malaysia work permit processing',
                ]
            );
        }

        // Agency 4: Sky Travel Services (Flight & Hotel Booking)
        $agency4User = User::updateOrCreate(
            ['email' => 'skytravel@example.com'],
            [
                'name' => 'Sky Travel Services',
                'email' => 'skytravel@example.com',
                'password' => Hash::make('password'),
                'role_id' => $agencyRole->id,
                'email_verified_at' => now(),
            ]
        );

        $agency4 = Agency::updateOrCreate(
            ['user_id' => $agency4User->id],
            [
                'name' => 'Sky Travel Services',
                'company_name' => 'Sky Travel & Tours Ltd.',
                'registration_number' => 'REG-2025-004',
                'phone' => '+880 1600-333333',
                'email' => 'skytravel@example.com',
                'address' => '321 Dhanmondi',
                'city' => 'Dhaka',
                'country' => 'Bangladesh',
                'postal_code' => '1205',
                'description' => 'Flight ticketing and hotel booking services worldwide',
                'website' => 'https://skytravel.com',
                'commission_rate' => 10.00,
                'is_verified' => true,
                'is_active' => true,
                'verified_at' => now(),
            ]
        );

        // Assign Agency 4 to Flight Booking - Global
        if ($flightBooking) {
            AgencyCountryAssignment::updateOrCreate(
                [
                    'agency_id' => $agency4->id,
                    'country' => 'Global - Flight',
                    'visa_type' => 'N/A',
                ],
                [
                    'service_module_id' => $flightBooking->id,
                    'country_id' => null,
                    'country_code' => 'GLB',
                    'visa_type_id' => null,
                    'assignment_scope' => 'global',
                    'platform_commission_rate' => 10.00,
                    'commission_type' => 'percentage',
                    'can_edit_requirements' => false,
                    'can_set_fees' => true,
                    'can_process_applications' => true,
                    'is_active' => true,
                    'assigned_at' => now(),
                    'assigned_by' => 1,
                    'assignment_notes' => 'Global flight booking service',
                ]
            );
        }

        // Assign Agency 4 to Hotel Booking - Global
        if ($hotelBooking) {
            AgencyCountryAssignment::updateOrCreate(
                [
                    'agency_id' => $agency4->id,
                    'country' => 'Global - Hotel',
                    'visa_type' => 'N/A',
                ],
                [
                    'service_module_id' => $hotelBooking->id,
                    'country_id' => null,
                    'country_code' => 'GLB',
                    'visa_type_id' => null,
                    'assignment_scope' => 'global',
                    'platform_commission_rate' => 8.00,
                    'commission_type' => 'percentage',
                    'can_edit_requirements' => false,
                    'can_set_fees' => true,
                    'can_process_applications' => true,
                    'is_active' => true,
                    'assigned_at' => now(),
                    'assigned_by' => 1,
                    'assignment_notes' => 'Global hotel booking service',
                ]
            );
        }

        $this->command->info('✅ Created/Updated 4 test agencies with diverse assignments');
        $this->command->info('   1. BideshGomon Travel - Tourist Visa (MY, TH, SG)');
        $this->command->info('   2. Global Education - Student Visa (UK, USA)');
        $this->command->info('   3. Work Permit Experts - Work Visa (SG, MY)');
        $this->command->info('   4. Sky Travel - Flight & Hotel (Global)');
        $this->command->newLine();
        $this->command->info('📧 Login credentials:');
        $this->command->info('   test@example.com / password');
        $this->command->info('   global.edu@example.com / password');
        $this->command->info('   workpermit@example.com / password');
        $this->command->info('   skytravel@example.com / password');
    }
}
