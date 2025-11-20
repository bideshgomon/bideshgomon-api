<?php

namespace Database\Seeders;

use App\Models\VisaApplication;
use Illuminate\Database\Seeder;

class VisaServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample visa types with countries and pricing
        $visaData = [
            ['country' => 'United States', 'code' => 'USA', 'type' => 'tourist', 'service' => 15000, 'govt' => 16000, 'processing' => 5000],
            ['country' => 'United Kingdom', 'code' => 'GBR', 'type' => 'tourist', 'service' => 12000, 'govt' => 12000, 'processing' => 4000],
            ['country' => 'Canada', 'code' => 'CAN', 'type' => 'tourist', 'service' => 10000, 'govt' => 10000, 'processing' => 3500],
            ['country' => 'Australia', 'code' => 'AUS', 'type' => 'tourist', 'service' => 13000, 'govt' => 14000, 'processing' => 4500],
            ['country' => 'Schengen', 'code' => 'SCH', 'type' => 'tourist', 'service' => 8000, 'govt' => 8000, 'processing' => 3000],
            ['country' => 'United Arab Emirates', 'code' => 'ARE', 'type' => 'tourist', 'service' => 6000, 'govt' => 5000, 'processing' => 2000],
            ['country' => 'Singapore', 'code' => 'SGP', 'type' => 'tourist', 'service' => 7000, 'govt' => 4000, 'processing' => 2500],
            ['country' => 'Malaysia', 'code' => 'MYS', 'type' => 'tourist', 'service' => 3000, 'govt' => 2000, 'processing' => 1000],
            ['country' => 'Thailand', 'code' => 'THA', 'type' => 'tourist', 'service' => 2500, 'govt' => 2000, 'processing' => 800],
            ['country' => 'India', 'code' => 'IND', 'type' => 'tourist', 'service' => 3500, 'govt' => 3000, 'processing' => 1200],
        ];

        foreach ($visaData as $data) {
            VisaApplication::create([
                'user_id' => 2,
                'visa_type' => $data['type'],
                'destination_country' => $data['country'],
                'destination_country_code' => $data['code'],
                'visa_category' => 'single_entry',
                'duration_days' => 30,
                'applicant_name' => 'Sample Applicant',
                'applicant_email' => 'applicant@example.com',
                'applicant_phone' => '+8801700000000',
                'applicant_dob' => '1990-01-15',
                'passport_number' => 'BD' . rand(1000000, 9999999),
                'passport_issue_date' => '2020-01-01',
                'passport_expiry_date' => '2030-01-01',
                'passport_issuing_country' => 'Bangladesh',
                'nationality' => 'Bangladeshi',
                'intended_travel_date' => now()->addMonths(2),
                'travel_purpose' => 'Tourism and sightseeing',
                'occupation' => 'Software Engineer',
                'processing_type' => 'standard',
                'processing_days' => 15,
                'status' => ['submitted', 'under_review', 'approved'][rand(0, 2)],
                'submitted_at' => now()->subDays(rand(1, 30)),
                'service_fee' => $data['service'],
                'government_fee' => $data['govt'],
                'processing_fee' => $data['processing'],
                'total_amount' => $data['service'] + $data['govt'] + $data['processing'],
                'payment_status' => 'paid',
                'paid_at' => now()->subDays(rand(1, 30)),
                'ip_address' => '127.0.0.1',
            ]);
        }

        echo "✓ Created 10 sample visa applications\n";
    }
}
