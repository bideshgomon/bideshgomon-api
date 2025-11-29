<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ServiceApplication;
use App\Models\ServiceModule;
use App\Models\Country;
use App\Models\TouristVisa;
use Illuminate\Database\Seeder;

class TestServiceApplicationsSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create test users
        $user1 = User::firstOrCreate(
            ['email' => 'testuser1@example.com'],
            [
                'name' => 'John Doe',
                'password' => bcrypt('password'),
                'phone' => '01712345678',
            ]
        );

        $user2 = User::firstOrCreate(
            ['email' => 'testuser2@example.com'],
            [
                'name' => 'Jane Smith',
                'password' => bcrypt('password'),
                'phone' => '01812345679',
            ]
        );

        // Get service modules
        $touristVisaModule = ServiceModule::where('category', 'Visa Services')
            ->where('name', 'like', '%Tourist%')
            ->first();

        if (!$touristVisaModule) {
            $touristVisaModule = ServiceModule::first();
        }

        // Get countries
        $usa = Country::where('name', 'United States')->first();
        $canada = Country::where('name', 'Canada')->first();
        $uk = Country::where('name', 'United Kingdom')->first();

        if (!$usa || !$canada || !$uk) {
            $this->command->error('Countries not found. Please run CountrySeeder first.');
            return;
        }

        // Create test applications
        $applications = [
            [
                'user' => $user1,
                'country' => $usa,
                'travel_purpose' => 'Tourism',
                'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            ],
            [
                'user' => $user2,
                'country' => $canada,
                'travel_purpose' => 'Visit Family',
                'departure_date' => now()->addMonths(3)->format('Y-m-d'),
            ],
            [
                'user' => $user1,
                'country' => $uk,
                'travel_purpose' => 'Business',
                'departure_date' => now()->addMonths(1)->format('Y-m-d'),
            ],
        ];

        foreach ($applications as $appData) {
            // Create tourist visa first
            $touristVisa = TouristVisa::create([
                'user_id' => $appData['user']->id,
                'destination_country_id' => $appData['country']->id,
                'intended_travel_date' => $appData['departure_date'],
                'duration_days' => 14,
                'status' => 'pending',
                'user_notes' => 'Test application for ' . $appData['travel_purpose'],
            ]);

            // Create service application
            $application = ServiceApplication::create([
                'user_id' => $appData['user']->id,
                'service_module_id' => $touristVisaModule->id,
                'application_number' => 'APP-' . date('Y') . '-' . str_pad(ServiceApplication::count() + 1, 4, '0', STR_PAD_LEFT),
                'status' => 'pending',
                'application_data' => [
                    'destination_country' => $appData['country']->name,
                    'destination_country_id' => $appData['country']->id,
                    'travel_purpose' => $appData['travel_purpose'],
                    'departure_date' => $appData['departure_date'],
                    'duration' => '14 days',
                ],
                'tourist_visa_id' => $touristVisa->id,
            ]);

            $this->command->info("Created application: {$application->application_number} for {$appData['user']->name}");
        }

        $this->command->info('Test service applications created successfully!');
    }
}
