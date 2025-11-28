<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VisaRequirement;
use App\Models\Country;

class MalaysiaVisaFeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Malaysia country
        $malaysia = Country::where('name', 'Malaysia')->first();
        
        if (!$malaysia) {
            $this->command->error('Malaysia not found in countries table');
            return;
        }

        $this->command->info('🇲🇾 Setting up Malaysia visa fees...');

        // Tourist Visa Fees
        $touristVisa = VisaRequirement::updateOrCreate(
            [
                'country_id' => $malaysia->id,
                'country' => 'Malaysia',
                'country_code' => 'MY',
                'visa_type' => 'tourist',
            ],
            [
                'visa_category' => 'Single Entry',
                
                // Fees (in BDT)
                'government_fee' => 16000,  // Embassy fee
                'service_fee' => 15000,     // Our platform fee
                'processing_fee_standard' => 5000,  // Standard processing
                'processing_fee_express' => 8000,   // Express processing
                'processing_fee_urgent' => 12000,   // Urgent processing
                
                // Processing Time
                'processing_days_standard' => 15,
                'processing_days_express' => 10,
                'processing_days_urgent' => 5,
                'processing_time_info' => '15-20 working days (Standard), 10-12 days (Express), 5-7 days (Urgent)',
                
                // Financial Requirements
                'min_bank_balance' => 500000,  // ৳5,00,000
                'bank_statement_months' => 6,
                'financial_requirements' => 'Minimum bank balance of ৳5,00,000 for last 6 months. Bank statement must show regular transactions. Bank solvency certificate required.',
                
                // Eligibility Criteria
                'eligibility_criteria' => 'Valid passport with minimum 6 months validity. Clean travel history. Sufficient financial means. Confirmed return tickets. Hotel booking or invitation letter.',
                
                // Interview & Biometrics
                'interview_required' => false,
                'biometrics_required' => false,
                
                // Important Notes
                'important_notes' => '⚠️ IMPORTANT NOTES:
1. Passport must have at least 6 months validity from date of entry
2. Old passport required if available
3. All documents must be recent (not older than 3 months)
4. Bank statement should show consistent balance
5. Cover letter must be addressed to "Visa Officer, High Commission of Malaysia"
6. Processing time may vary during peak season
7. Visa approval is at the discretion of Malaysian authorities
8. Fees are non-refundable in case of visa rejection',
                
                // Application Method
                'application_method' => 'online',
                'embassy_website' => 'https://www.kln.gov.my/web/bgd_dhaka',
                
                // Status
                'is_active' => true,
            ]
        );

        $this->command->info('✅ Tourist visa fees added');

        // Business Visa Fees
        $businessVisa = VisaRequirement::updateOrCreate(
            [
                'country_id' => $malaysia->id,
                'country' => 'Malaysia',
                'country_code' => 'MY',
                'visa_type' => 'business',
            ],
            [
                'visa_category' => 'Single/Multiple Entry',
                
                // Fees (in BDT)
                'government_fee' => 20000,
                'service_fee' => 18000,
                'processing_fee_standard' => 6000,
                'processing_fee_express' => 10000,
                'processing_fee_urgent' => 15000,
                
                // Processing Time
                'processing_days_standard' => 20,
                'processing_days_express' => 12,
                'processing_days_urgent' => 7,
                'processing_time_info' => '20-25 working days (Standard), 12-15 days (Express), 7-10 days (Urgent)',
                
                // Financial Requirements
                'min_bank_balance' => 800000,
                'bank_statement_months' => 6,
                'financial_requirements' => 'Minimum bank balance of ৳8,00,000 for last 6 months. Company bank statements required. Tax certificates mandatory.',
                
                // Eligibility Criteria
                'eligibility_criteria' => 'Valid business purpose. Invitation letter from Malaysian company. Valid trade license. Company registration certificate. Tax clearance.',
                
                // Interview & Biometrics
                'interview_required' => false,
                'biometrics_required' => false,
                
                // Important Notes
                'important_notes' => '⚠️ BUSINESS VISA REQUIREMENTS:
1. Invitation letter from Malaysian company is mandatory
2. Company must be registered with Malaysian authorities
3. Purpose of visit must be clearly stated
4. Trade license and TIN certificate required
5. Company profile and brochure recommended
6. Previous business relationship documents helpful
7. Multiple entry visa requires strong justification',
                
                'application_method' => 'online',
                'embassy_website' => 'https://www.kln.gov.my/web/bgd_dhaka',
                'is_active' => true,
            ]
        );

        $this->command->info('✅ Business visa fees added');

        // Student Visa Fees
        $studentVisa = VisaRequirement::updateOrCreate(
            [
                'country_id' => $malaysia->id,
                'country' => 'Malaysia',
                'country_code' => 'MY',
                'visa_type' => 'student',
            ],
            [
                'visa_category' => 'Student Pass',
                
                // Fees (in BDT)
                'government_fee' => 25000,
                'service_fee' => 20000,
                'processing_fee_standard' => 7000,
                'processing_fee_express' => 12000,
                
                // Processing Time
                'processing_days_standard' => 30,
                'processing_days_express' => 20,
                'processing_time_info' => '30-45 working days (Standard), 20-25 days (Express)',
                
                // Financial Requirements
                'min_bank_balance' => 1000000,
                'bank_statement_months' => 6,
                'financial_requirements' => 'Minimum bank balance of ৳10,00,000 for last 6 months. Sponsor\'s financial documents required. Tuition fee payment proof.',
                
                // Eligibility Criteria
                'eligibility_criteria' => 'Admission letter from Malaysian university. EMGS approval. Sufficient financial proof. Parent\'s sponsorship documents. Academic transcripts.',
                
                // Interview & Biometrics
                'interview_required' => false,
                'biometrics_required' => true,
                'biometrics_details' => 'Biometric enrollment required at Malaysian High Commission',
                
                // Important Notes
                'important_notes' => '⚠️ STUDENT VISA REQUIREMENTS:
1. EMGS (Education Malaysia Global Services) approval is mandatory
2. Admission letter from recognized Malaysian university
3. SEV (Single Entry Visa) will be issued first
4. Student Pass to be collected upon arrival in Malaysia
5. Medical examination required
6. Parent\'s financial sponsorship documents needed
7. Academic transcripts with English translation
8. Processing time longer than other visa types',
                
                'application_method' => 'online',
                'embassy_website' => 'https://visa.educationmalaysia.gov.my',
                'is_active' => true,
            ]
        );

        $this->command->info('✅ Student visa fees added');

        // Work Visa Fees
        $workVisa = VisaRequirement::updateOrCreate(
            [
                'country_id' => $malaysia->id,
                'country' => 'Malaysia',
                'country_code' => 'MY',
                'visa_type' => 'work',
            ],
            [
                'visa_category' => 'Employment Pass',
                
                // Fees (in BDT)
                'government_fee' => 30000,
                'service_fee' => 25000,
                'processing_fee_standard' => 10000,
                'processing_fee_express' => 15000,
                
                // Processing Time
                'processing_days_standard' => 45,
                'processing_days_express' => 30,
                'processing_time_info' => '45-60 working days (Standard), 30-40 days (Express)',
                
                // Financial Requirements
                'min_bank_balance' => 1500000,
                'bank_statement_months' => 6,
                'financial_requirements' => 'Minimum bank balance of ৳15,00,000. Employment contract required. Salary details must be clear.',
                
                // Eligibility Criteria
                'eligibility_criteria' => 'Job offer letter from Malaysian employer. Employment Pass approval from Immigration Department. Professional qualifications. Work experience certificates.',
                
                // Interview & Biometrics
                'interview_required' => true,
                'interview_details' => 'Interview may be required at High Commission',
                'biometrics_required' => true,
                'biometrics_details' => 'Biometric enrollment mandatory',
                
                // Important Notes
                'important_notes' => '⚠️ WORK VISA REQUIREMENTS:
1. Employment Pass approval from Malaysian Immigration required FIRST
2. Employer must apply for EP approval in Malaysia
3. Professional qualification certificates mandatory
4. Work experience certificates with employment letters
5. Medical fitness certificate required
6. Police clearance certificate needed
7. Educational certificates with transcripts
8. CV/Resume highlighting relevant experience
9. EP approval takes 4-8 weeks before visa application',
                
                'application_method' => 'online',
                'embassy_website' => 'https://www.kln.gov.my/web/bgd_dhaka',
                'is_active' => true,
            ]
        );

        $this->command->info('✅ Work visa fees added');

        $this->command->newLine();
        $this->command->info('🎉 Malaysia visa fees setup completed!');
        $this->command->info('📊 Summary:');
        $this->command->table(
            ['Visa Type', 'Embassy Fee', 'Service Fee', 'Processing (Std)', 'Total (Standard)'],
            [
                ['Tourist', '৳16,000', '৳15,000', '৳5,000', '৳36,000'],
                ['Business', '৳20,000', '৳18,000', '৳6,000', '৳44,000'],
                ['Student', '৳25,000', '৳20,000', '৳7,000', '৳52,000'],
                ['Work', '৳30,000', '৳25,000', '৳10,000', '৳65,000'],
            ]
        );
    }
}
