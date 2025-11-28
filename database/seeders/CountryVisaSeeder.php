<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountryVisaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data (without triggering foreign key constraints)
        DB::statement('PRAGMA foreign_keys = OFF');
        DB::table('visa_fees')->truncate();
        DB::table('visa_requirements')->whereNotNull('country_id')->delete();
        
        // Insert or update countries
        $countries = [
            ['name' => 'United States', 'iso_code_2' => 'US', 'iso_code_3' => 'USA', 'flag_emoji' => '🇺🇸', 'phone_code' => '+1', 'currency_code' => 'USD', 'region' => 'Americas', 'is_active' => true],
            ['name' => 'United Kingdom', 'iso_code_2' => 'GB', 'iso_code_3' => 'GBR', 'flag_emoji' => '🇬🇧', 'phone_code' => '+44', 'currency_code' => 'GBP', 'region' => 'Europe', 'is_active' => true],
            ['name' => 'Canada', 'iso_code_2' => 'CA', 'iso_code_3' => 'CAN', 'flag_emoji' => '🇨🇦', 'phone_code' => '+1', 'currency_code' => 'CAD', 'region' => 'Americas', 'is_active' => true],
            ['name' => 'Australia', 'iso_code_2' => 'AU', 'iso_code_3' => 'AUS', 'flag_emoji' => '🇦🇺', 'phone_code' => '+61', 'currency_code' => 'AUD', 'region' => 'Oceania', 'is_active' => true],
            ['name' => 'Germany', 'iso_code_2' => 'DE', 'iso_code_3' => 'DEU', 'flag_emoji' => '🇩🇪', 'phone_code' => '+49', 'currency_code' => 'EUR', 'region' => 'Europe', 'is_active' => true],
            ['name' => 'France', 'iso_code_2' => 'FR', 'iso_code_3' => 'FRA', 'flag_emoji' => '🇫🇷', 'phone_code' => '+33', 'currency_code' => 'EUR', 'region' => 'Europe', 'is_active' => true],
            ['name' => 'Italy', 'iso_code_2' => 'IT', 'iso_code_3' => 'ITA', 'flag_emoji' => '🇮🇹', 'phone_code' => '+39', 'currency_code' => 'EUR', 'region' => 'Europe', 'is_active' => true],
            ['name' => 'Spain', 'iso_code_2' => 'ES', 'iso_code_3' => 'ESP', 'flag_emoji' => '🇪🇸', 'phone_code' => '+34', 'currency_code' => 'EUR', 'region' => 'Europe', 'is_active' => true],
            ['name' => 'Japan', 'iso_code_2' => 'JP', 'iso_code_3' => 'JPN', 'flag_emoji' => '🇯🇵', 'phone_code' => '+81', 'currency_code' => 'JPY', 'region' => 'Asia', 'is_active' => true],
            ['name' => 'Singapore', 'iso_code_2' => 'SG', 'iso_code_3' => 'SGP', 'flag_emoji' => '🇸🇬', 'phone_code' => '+65', 'currency_code' => 'SGD', 'region' => 'Asia', 'is_active' => true],
            ['name' => 'United Arab Emirates', 'iso_code_2' => 'AE', 'iso_code_3' => 'ARE', 'flag_emoji' => '🇦🇪', 'phone_code' => '+971', 'currency_code' => 'AED', 'region' => 'Asia', 'is_active' => true],
            ['name' => 'Saudi Arabia', 'iso_code_2' => 'SA', 'iso_code_3' => 'SAU', 'flag_emoji' => '🇸🇦', 'phone_code' => '+966', 'currency_code' => 'SAR', 'region' => 'Asia', 'is_active' => true],
            ['name' => 'Malaysia', 'iso_code_2' => 'MY', 'iso_code_3' => 'MYS', 'flag_emoji' => '🇲🇾', 'phone_code' => '+60', 'currency_code' => 'MYR', 'region' => 'Asia', 'is_active' => true],
            ['name' => 'Thailand', 'iso_code_2' => 'TH', 'iso_code_3' => 'THA', 'flag_emoji' => '🇹🇭', 'phone_code' => '+66', 'currency_code' => 'THB', 'region' => 'Asia', 'is_active' => true],
            ['name' => 'Turkey', 'iso_code_2' => 'TR', 'iso_code_3' => 'TUR', 'flag_emoji' => '🇹🇷', 'phone_code' => '+90', 'currency_code' => 'TRY', 'region' => 'Asia', 'is_active' => true],
            ['name' => 'India', 'iso_code_2' => 'IN', 'iso_code_3' => 'IND', 'flag_emoji' => '🇮🇳', 'phone_code' => '+91', 'currency_code' => 'INR', 'region' => 'Asia', 'is_active' => true],
            ['name' => 'China', 'iso_code_2' => 'CN', 'iso_code_3' => 'CHN', 'flag_emoji' => '🇨🇳', 'phone_code' => '+86', 'currency_code' => 'CNY', 'region' => 'Asia', 'is_active' => true],
            ['name' => 'South Korea', 'iso_code_2' => 'KR', 'iso_code_3' => 'KOR', 'flag_emoji' => '🇰🇷', 'phone_code' => '+82', 'currency_code' => 'KRW', 'region' => 'Asia', 'is_active' => true],
            ['name' => 'Switzerland', 'iso_code_2' => 'CH', 'iso_code_3' => 'CHE', 'flag_emoji' => '🇨🇭', 'phone_code' => '+41', 'currency_code' => 'CHF', 'region' => 'Europe', 'is_active' => true],
            ['name' => 'Netherlands', 'iso_code_2' => 'NL', 'iso_code_3' => 'NLD', 'flag_emoji' => '🇳🇱', 'phone_code' => '+31', 'currency_code' => 'EUR', 'region' => 'Europe', 'is_active' => true],
        ];

        foreach ($countries as $country) {
            DB::table('countries')->updateOrInsert(
                ['iso_code_2' => $country['iso_code_2']],
                array_merge($country, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
        
        DB::statement('PRAGMA foreign_keys = ON');

        // Get country IDs
        $usaId = DB::table('countries')->where('iso_code_2', 'US')->value('id');
        $ukId = DB::table('countries')->where('iso_code_2', 'GB')->value('id');
        $canadaId = DB::table('countries')->where('iso_code_2', 'CA')->value('id');
        $australiaId = DB::table('countries')->where('iso_code_2', 'AU')->value('id');
        $germanyId = DB::table('countries')->where('iso_code_2', 'DE')->value('id');
        $uaeId = DB::table('countries')->where('iso_code_2', 'AE')->value('id');
        $japanId = DB::table('countries')->where('iso_code_2', 'JP')->value('id');
        $singaporeId = DB::table('countries')->where('iso_code_2', 'SG')->value('id');

        // Visa Requirements for USA - Student
        DB::table('visa_requirements')->insert([
            'country_id' => $usaId,
            'country' => 'United States',
            'country_code' => 'USA',
            'profession' => 'Student',
            'visa_type' => 'tourist',
            'general_requirements' => 'Tourist visa for USA requires valid passport, visa application form, interview appointment, and supporting documents.',
            'required_documents' => json_encode([
                'Valid Passport (6+ months validity)',
                'Passport Size Photos (2 copies)',
                'DS-160 Confirmation Page',
                'Visa Application Fee Receipt',
                'Bank Statements (Last 6 months)',
                'Travel Itinerary',
                'Hotel Booking Confirmation',
                'Return Flight Tickets',
            ]),
            'profession_specific_docs' => json_encode([
                'University Enrollment Letter',
                'Student ID Card',
                'Academic Transcripts',
                'Proof of Financial Support',
            ]),
            'processing_time' => '15-30 days',
            'validity_period' => '10 years (Multiple Entry)',
            'interview_required' => true,
            'biometrics_required' => true,
            'is_template' => false,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Visa Fees for USA - Student
        DB::table('visa_fees')->insert([
            'country_id' => $usaId,
            'agency_id' => null,
            'profession' => 'Student',
            'visa_type' => 'tourist',
            'embassy_fee' => 16000,
            'service_fee' => 5000,
            'processing_fee' => 2000,
            'total_fee' => 23000,
            'currency' => 'BDT',
            'urgent_processing_fee' => 8000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Visa Requirements for UK - Engineer
        DB::table('visa_requirements')->insert([
            'country_id' => $ukId,
            'country' => 'United Kingdom',
            'country_code' => 'GBR',
            'profession' => 'Engineer',
            'visa_type' => 'tourist',
            'general_requirements' => 'UK tourist visa requires valid passport, visa application, bank statements, and travel itinerary.',
            'required_documents' => json_encode([
                'Valid Passport (6+ months validity)',
                'Passport Size Photos (2 copies)',
                'Online Application Form',
                'Bank Statements (Last 6 months)',
                'Travel Itinerary',
                'Accommodation Proof',
                'Return Flight Booking',
                'Travel Insurance',
            ]),
            'profession_specific_docs' => json_encode([
                'Employment Letter',
                'Professional Engineer Certificate',
                'Leave Approval Letter',
                'Salary Slips (Last 3 months)',
            ]),
            'processing_time' => '10-15 days',
            'validity_period' => '6 months',
            'interview_required' => false,
            'biometrics_required' => true,
            'is_template' => false,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Visa Fees for UK - Engineer
        DB::table('visa_fees')->insert([
            'country_id' => $ukId,
            'agency_id' => null,
            'profession' => 'Engineer',
            'visa_type' => 'tourist',
            'embassy_fee' => 12000,
            'service_fee' => 4000,
            'processing_fee' => 1500,
            'total_fee' => 17500,
            'currency' => 'BDT',
            'urgent_processing_fee' => 6000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Visa Requirements for Canada - Business Owner
        DB::table('visa_requirements')->insert([
            'country_id' => $canadaId,
            'country' => 'Canada',
            'country_code' => 'CAN',
            'profession' => 'Business Owner',
            'visa_type' => 'tourist',
            'general_requirements' => 'Canada tourist visa requires valid passport, application form, proof of funds, and travel plans.',
            'required_documents' => json_encode([
                'Valid Passport (6+ months validity)',
                'Passport Size Photos (2 copies)',
                'Completed Application Form',
                'Bank Statements (Last 6 months)',
                'Travel Itinerary',
                'Hotel Reservation',
                'Return Flight Tickets',
                'Purpose of Visit Letter',
            ]),
            'profession_specific_docs' => json_encode([
                'Business Registration Certificate',
                'Business Bank Statements',
                'Tax Returns (Last 2 years)',
                'Company Letterhead Document',
            ]),
            'processing_time' => '20-30 days',
            'validity_period' => '10 years (Multiple Entry)',
            'interview_required' => false,
            'biometrics_required' => true,
            'is_template' => false,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Visa Fees for Canada - Business Owner
        DB::table('visa_fees')->insert([
            'country_id' => $canadaId,
            'agency_id' => null,
            'profession' => 'Business Owner',
            'visa_type' => 'tourist',
            'embassy_fee' => 10000,
            'service_fee' => 4500,
            'processing_fee' => 1800,
            'total_fee' => 16300,
            'currency' => 'BDT',
            'urgent_processing_fee' => 7000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Visa Requirements for Australia - Doctor
        DB::table('visa_requirements')->insert([
            'country_id' => $australiaId,
            'country' => 'Australia',
            'country_code' => 'AUS',
            'profession' => 'Doctor',
            'visa_type' => 'tourist',
            'general_requirements' => 'Australia tourist visa requires valid passport, online application, travel insurance, and financial proof.',
            'required_documents' => json_encode([
                'Valid Passport (6+ months validity)',
                'Passport Size Photos (2 copies)',
                'Online Application (ImmiAccount)',
                'Bank Statements (Last 6 months)',
                'Travel Itinerary',
                'Accommodation Booking',
                'Return Flight Tickets',
                'Travel Insurance (Mandatory)',
            ]),
            'profession_specific_docs' => json_encode([
                'Medical Council Registration',
                'Employment Certificate from Hospital',
                'Leave Approval Letter',
                'Professional Indemnity Insurance',
            ]),
            'processing_time' => '15-25 days',
            'validity_period' => '1 year (Multiple Entry)',
            'interview_required' => false,
            'biometrics_required' => true,
            'is_template' => false,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Visa Fees for Australia - Doctor
        DB::table('visa_fees')->insert([
            'country_id' => $australiaId,
            'agency_id' => null,
            'profession' => 'Doctor',
            'visa_type' => 'tourist',
            'embassy_fee' => 14500,
            'service_fee' => 5500,
            'processing_fee' => 2000,
            'total_fee' => 22000,
            'currency' => 'BDT',
            'urgent_processing_fee' => 7500,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Visa Requirements for Germany (Schengen) - IT Professional
        DB::table('visa_requirements')->insert([
            'country_id' => $germanyId,
            'country' => 'Germany',
            'country_code' => 'DEU',
            'profession' => 'IT Professional',
            'visa_type' => 'tourist',
            'general_requirements' => 'Germany Schengen visa requires valid passport, travel insurance, hotel bookings, and financial means.',
            'required_documents' => json_encode([
                'Valid Passport (6+ months validity)',
                'Passport Size Photos (2 copies, Schengen format)',
                'Completed Schengen Visa Form',
                'Travel Insurance (€30,000 coverage)',
                'Bank Statements (Last 3 months)',
                'Flight Reservation',
                'Hotel Booking',
                'Cover Letter',
            ]),
            'profession_specific_docs' => json_encode([
                'Employment Letter',
                'Company ID Card',
                'Leave Approval Certificate',
                'Last 3 Months Salary Slips',
            ]),
            'processing_time' => '10-15 days',
            'validity_period' => '90 days',
            'interview_required' => true,
            'biometrics_required' => true,
            'is_template' => false,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Visa Fees for Germany - IT Professional
        DB::table('visa_fees')->insert([
            'country_id' => $germanyId,
            'agency_id' => null,
            'profession' => 'IT Professional',
            'visa_type' => 'tourist',
            'embassy_fee' => 8000,
            'service_fee' => 3500,
            'processing_fee' => 1500,
            'total_fee' => 13000,
            'currency' => 'BDT',
            'urgent_processing_fee' => 5000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Visa Requirements for UAE - all professions (template)
        DB::table('visa_requirements')->insert([
            'country_id' => $uaeId,
            'country' => 'United Arab Emirates',
            'country_code' => 'ARE',
            'profession' => 'all',
            'visa_type' => 'tourist',
            'general_requirements' => 'UAE tourist visa requires valid passport, confirmed flight tickets, and hotel bookings.',
            'required_documents' => json_encode([
                'Valid Passport (6+ months validity)',
                'Passport Size Photos (2 copies)',
                'Passport Copy',
                'Confirmed Return Flight Tickets',
                'Hotel Booking Confirmation',
                'Bank Statement (Last 3 months)',
            ]),
            'profession_specific_docs' => json_encode([
                'Employment Letter (if employed)',
                'Business Documents (if business owner)',
            ]),
            'processing_time' => '3-5 days',
            'validity_period' => '60 days',
            'interview_required' => false,
            'biometrics_required' => false,
            'is_template' => false,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Visa Fees for UAE - all professions
        DB::table('visa_fees')->insert([
            'country_id' => $uaeId,
            'agency_id' => null,
            'profession' => 'all',
            'visa_type' => 'tourist',
            'embassy_fee' => 6000,
            'service_fee' => 2500,
            'processing_fee' => 1000,
            'total_fee' => 9500,
            'currency' => 'BDT',
            'urgent_processing_fee' => 3000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Visa Requirements for Japan - Teacher
        DB::table('visa_requirements')->insert([
            'country_id' => $japanId,
            'country' => 'Japan',
            'country_code' => 'JPN',
            'profession' => 'Teacher',
            'visa_type' => 'tourist',
            'general_requirements' => 'Japan tourist visa requires valid passport, application form, itinerary, and proof of financial means.',
            'required_documents' => json_encode([
                'Valid Passport (6+ months validity)',
                'Passport Size Photos (2 copies)',
                'Visa Application Form',
                'Flight Itinerary',
                'Hotel Reservations',
                'Bank Statements (Last 6 months)',
                'Travel Schedule',
            ]),
            'profession_specific_docs' => json_encode([
                'Teaching Certificate',
                'Employment Letter from School',
                'Leave Approval Document',
                'Salary Slips (Last 3 months)',
            ]),
            'processing_time' => '5-7 days',
            'validity_period' => '90 days',
            'interview_required' => false,
            'biometrics_required' => false,
            'is_template' => false,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Visa Fees for Japan - Teacher
        DB::table('visa_fees')->insert([
            'country_id' => $japanId,
            'agency_id' => null,
            'profession' => 'Teacher',
            'visa_type' => 'tourist',
            'embassy_fee' => 3000,
            'service_fee' => 2000,
            'processing_fee' => 1000,
            'total_fee' => 6000,
            'currency' => 'BDT',
            'urgent_processing_fee' => 2500,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Visa Requirements for Singapore - all professions
        DB::table('visa_requirements')->insert([
            'country_id' => $singaporeId,
            'country' => 'Singapore',
            'country_code' => 'SGP',
            'profession' => 'all',
            'visa_type' => 'tourist',
            'general_requirements' => 'Singapore tourist visa requires valid passport, completed application form, and financial documents.',
            'required_documents' => json_encode([
                'Valid Passport (6+ months validity)',
                'Passport Size Photos (2 copies)',
                'Completed Application Form (Form 14A)',
                'Flight Itinerary',
                'Hotel Booking',
                'Bank Statements (Last 3 months)',
                'Income Tax Returns',
            ]),
            'profession_specific_docs' => json_encode([
                'Employment Letter',
                'Leave Letter',
            ]),
            'processing_time' => '3-5 days',
            'validity_period' => '30 days',
            'interview_required' => false,
            'biometrics_required' => false,
            'is_template' => false,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Visa Fees for Singapore - all professions
        DB::table('visa_fees')->insert([
            'country_id' => $singaporeId,
            'agency_id' => null,
            'profession' => 'all',
            'visa_type' => 'tourist',
            'embassy_fee' => 3000,
            'service_fee' => 1500,
            'processing_fee' => 800,
            'total_fee' => 5300,
            'currency' => 'BDT',
            'urgent_processing_fee' => 2000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Countries and visa requirements seeded successfully!');
    }
}
