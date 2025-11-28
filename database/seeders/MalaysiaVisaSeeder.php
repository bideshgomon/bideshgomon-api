<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MalaysiaVisaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Malaysia country ID
        $malaysiaId = DB::table('countries')->where('iso_code_2', 'MY')->value('id');

        if (!$malaysiaId) {
            $this->command->error('Malaysia not found in countries table. Please run CountryVisaSeeder first.');
            return;
        }

        // Basic Documents (Common for all professions)
        $basicDocuments = [
            'Valid passport with Old Passport if available (minimum 6 months validity with at least 2 blank pages)',
            'Recent passport-size photograph (white background, without glasses)',
            'Recent bank statement (last 6 months) and bank solvency certificate',
            'TIN Certificate (English) and Income Tax Certificate for consecutive three (3) years',
            'Tour itinerary',
            'Air-ticket booking',
            'Hotel booking confirmation',
            'Cover letter to the visa officer',
            'Marriage Certificate Copy (if traveling with spouse and children)',
        ];

        // Job Holder specific documents
        $jobHolderDocs = [
            'NOC from current employer on letterhead pad',
            'Employee ID card photo (color)',
            'Proper visiting card',
            'Salary certificate/Pay slip',
            'BMDC certificate for Doctor (if applicable)',
            'BAR council Certificate for Advocate (if applicable)',
        ];

        // Business Person specific documents
        $businessPersonDocs = [
            'Notarized and renewed trade license (in English) copy',
            'Company Tax return submission documents',
            'Memorandum for limited company/Form XII',
            'One blank page of business letterhead pad',
            'Proper Visiting card',
        ];

        // Student specific documents
        $studentDocs = [
            'Student ID card Photo (color)',
            'Parent\'s/Spouse Bank statement and Bank Solvency Certificate',
            'Leave Approval and NOC from the school/college/university',
            'Birth Certificate Copy (only for Child & infant in case of unavailability of NID Card)',
            'Marriage Certificate Copy of parents',
        ];

        // Special notes for all applicants
        $specialNotes = "• If the applicant has visited Malaysia previously, need to provide all previous Malaysia E-Visa copy.\n• If the applicant has traveled to any other country rather than Malaysia previously, need to provide the travel history (visa and entry and exit stamps of passport) for each country.";

        // Create visa requirements for different professions
        $professionConfig = [
            'Engineer' => [
                'docs' => $jobHolderDocs,
                'category' => 'Social Visit Visa (eVisa)',
            ],
            'Doctor' => [
                'docs' => $jobHolderDocs,
                'category' => 'Social Visit Visa (eVisa)',
            ],
            'Teacher' => [
                'docs' => $jobHolderDocs,
                'category' => 'Social Visit Visa (eVisa)',
            ],
            'IT Professional' => [
                'docs' => $jobHolderDocs,
                'category' => 'Social Visit Visa (eVisa)',
            ],
            'Accountant' => [
                'docs' => $jobHolderDocs,
                'category' => 'Social Visit Visa (eVisa)',
            ],
            'Manager' => [
                'docs' => $jobHolderDocs,
                'category' => 'Social Visit Visa (eVisa)',
            ],
            'Government Employee' => [
                'docs' => $jobHolderDocs,
                'category' => 'Social Visit Visa (eVisa)',
            ],
            'Private Employee' => [
                'docs' => $jobHolderDocs,
                'category' => 'Social Visit Visa (eVisa)',
            ],
            'Business Owner' => [
                'docs' => $businessPersonDocs,
                'category' => 'Social Visit Visa (eVisa)',
            ],
            'Self Employed' => [
                'docs' => $businessPersonDocs,
                'category' => 'Social Visit Visa (eVisa)',
            ],
            'Student' => [
                'docs' => $studentDocs,
                'category' => 'Social Visit Visa (eVisa)',
            ],
            'Retired' => [
                'docs' => array_merge($jobHolderDocs, ['Retirement Certificate', 'Pension Statement']),
                'category' => 'Social Visit Visa (eVisa)',
            ],
        ];

        foreach ($professionConfig as $profession => $config) {
            DB::table('visa_requirements')->updateOrInsert(
                [
                    'country_id' => $malaysiaId,
                    'profession' => $profession,
                    'visa_type' => 'tourist'
                ],
                [
                    'country' => 'Malaysia',
                    'country_code' => 'MY',
                    'visa_category' => $config['category'],
                    'general_requirements' => 'Malaysia Tourist Visa (eVisa) requires valid passport, recent photographs, bank statements, tax certificates, and complete travel documentation. All documents must be clear scanned copies.',
                    'required_documents' => json_encode($basicDocuments),
                    'profession_specific_docs' => json_encode($config['docs']),
                    'processing_time' => '5-7 working days',
                    'validity_period' => '3 months (Single/Multiple Entry)',
                    'interview_required' => false,
                    'biometrics_required' => false,
                    'important_notes' => $specialNotes,
                    'is_template' => false,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Add visa fees for each profession
            DB::table('visa_fees')->updateOrInsert(
                [
                    'country_id' => $malaysiaId,
                    'profession' => $profession,
                    'visa_type' => 'tourist'
                ],
                [
                    'agency_id' => null,
                    'embassy_fee' => 3500.00,
                    'service_fee' => 4000.00,
                    'processing_fee' => 1500.00,
                    'total_fee' => 9000.00,
                    'currency' => 'BDT',
                    'urgent_processing_fee' => 2000.00,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('Malaysia visa requirements and fees seeded successfully!');
    }
}
