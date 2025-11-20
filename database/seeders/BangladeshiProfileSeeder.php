<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserPassport;
use App\Models\UserVisaHistory;
use App\Models\UserTravelHistory;
use App\Models\UserFamilyMember;
use App\Models\UserEducation;
use App\Models\UserWorkExperience;
use App\Models\UserLanguage;
use App\Models\UserFinancialInformation;
use App\Models\UserSecurityInformation;
use Illuminate\Support\Facades\Hash;

class BangladeshiProfileSeeder extends Seeder
{
    /**
     * Run the database seeders.
     * Creates realistic Bangladeshi user profiles for testing
     */
    public function run(): void
    {
        echo "\n";
        echo "============================================================\n";
        echo "🇧🇩 BANGLADESHI PROFILE SEEDER\n";
        echo "============================================================\n\n";

        // Create 5 different Bangladeshi user profiles
        $this->createGulfWorkerProfile();
        $this->createStudentProfile();
        $this->createBusinessPersonProfile();
        $this->createITWorkerProfile();
        $this->createFamilyMigrationProfile();

        echo "\n";
        echo "============================================================\n";
        echo "✅ BANGLADESHI PROFILE SEEDING COMPLETE!\n";
        echo "============================================================\n";
        echo "\n📊 Created 5 realistic Bangladeshi profiles:\n";
        echo "  1. Gulf Worker (Dubai-bound)\n";
        echo "  2. University Student (UK-bound)\n";
        echo "  3. Business Person (Multi-country)\n";
        echo "  4. IT Professional (Canada-bound)\n";
        echo "  5. Family Migration (Australia-bound)\n";
        echo "\n🔑 All passwords: password\n";
        echo "🚀 Ready for testing!\n\n";
    }

    /**
     * Profile 1: Gulf Worker - Going to Dubai for work
     */
    private function createGulfWorkerProfile()
    {
        echo "👷 Creating Gulf Worker Profile (Dubai-bound)...\n";

        $user = User::updateOrCreate(
            ['email' => 'rahim.khan@test.com'],
            [
                'name' => 'Rahim Khan',
                'password' => Hash::make('password'),
            ]
        );

        // Profile
        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'dob' => '1990-05-15',
                'gender' => 'male',
                'phone' => '+8801712345678',
                'nid' => '1234567890123',
                'present_address_line' => 'Mirpur-10',
                'present_city' => 'Dhaka',
                'present_division' => 'Dhaka',
                'permanent_address_line' => 'Gazipur',
                'permanent_city' => 'Gazipur',
                'permanent_division' => 'Dhaka',
                'nationality' => 'Bangladeshi',
                'marital_status' => 'married',
            ]
        );

        // Passport
        $passport = UserPassport::updateOrCreate(
            ['user_id' => $user->id, 'passport_number' => 'BN0567890'],
            [
                'surname' => 'KHAN',
                'given_names' => 'RAHIM',
                'passport_number' => 'BN0567890',
                'issuing_country' => 'BD',
                'issue_date' => '2023-01-15',
                'expiry_date' => '2028-01-14',
                'place_of_birth' => 'Gazipur',
                'sex' => 'M',
                'is_current_passport' => true,
            ]
        );

        // Visa - UAE Work Visa (Approved)
        UserVisaHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country' => 'AE',
                'visa_type' => 'work',
                'application_date' => '2024-08-01',
            ],
            [
                'user_passport_id' => $passport->id,
                'country' => 'AE',
                'visa_type' => 'work',
                'visa_category' => 'Employment Visa',
                'visa_number' => 'AE20240567890',
                'application_date' => '2024-08-01',
                'issue_date' => '2024-09-15',
                'expiry_date' => '2026-09-14',
                'status' => 'approved',
                'was_visa_rejected' => false,
                'purpose_of_visit' => 'Employment as Construction Worker at Al Futtaim Group, Dubai',
                'embassy_location' => 'UAE Embassy, Dhaka',
                'visa_fee' => 25000,
                'currency' => 'BDT',
                'notes' => '2-year work visa. Employer: Al Futtaim Group. Position: Construction Worker.',
            ]
        );

        // Previous Saudi Arabia work history
        UserTravelHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country_visited' => 'SA',
                'entry_date' => '2018-03-10',
            ],
            [
                'user_passport_id' => $passport->id,
                'country_visited' => 'SA',
                'city_visited' => 'Riyadh',
                'entry_date' => '2018-03-10',
                'exit_date' => '2021-12-20',
                'duration_days' => 1381,
                'purpose' => 'work',
                'compliance_issues' => false,
                'notes' => 'Worked as Construction Worker for Saudi Binladin Group. Contract completed successfully.',
            ]
        );

        // Family
        UserFamilyMember::updateOrCreate(
            [
                'user_id' => $user->id,
                'full_name' => 'Fatema Begum',
                'relationship' => 'spouse',
            ],
            [
                'full_name' => 'Fatema Begum',
                'relationship' => 'spouse',
                'date_of_birth' => '1993-08-20',
                'gender' => 'female',
                'nationality' => 'Bangladeshi',
                'country_of_residence' => 'BD',

            ]
        );

        UserFamilyMember::updateOrCreate(
            [
                'user_id' => $user->id,
                'full_name' => 'Ayesha Khan',
                'relationship' => 'son',
            ],
            [
                'full_name' => 'Ayesha Khan',
                'relationship' => 'son',
                'date_of_birth' => '2015-06-10',
                'gender' => 'female',
                'nationality' => 'Bangladeshi',
                'country_of_residence' => 'BD',

            ]
        );

        // Education - SSC & HSC only
        UserEducation::updateOrCreate(
            [
                'user_id' => $user->id,
                'degree' => 'Higher Secondary (HSC)',
                'institution_name' => 'Gazipur Govt. College',
            ],
            [
                'degree' => 'Higher Secondary (HSC)',
                'field_of_study' => 'Science',
                'institution_name' => 'Gazipur Govt. College',
                'country' => 'BD',
                'city' => 'Gazipur',
                'start_date' => '2006-01-01',
                'end_date' => '2008-05-31',
                'is_completed' => true,
                'gpa_or_grade' => '3.50/5.00',
                'language_of_instruction' => 'Bengali',
            ]
        );

        // Work Experience
        UserWorkExperience::updateOrCreate(
            [
                'user_id' => $user->id,
                'company_name' => 'Al Futtaim Group',
                'position' => 'Construction Worker',
            ],
            [
                'company_name' => 'Al Futtaim Group',
                'position' => 'Construction Worker',
                'country' => 'AE',
                'city' => 'Dubai',
                'start_date' => '2024-10-01',
                'end_date' => null,
                'is_current_employment' => true,
                'salary' => 1500,
                'salary_period' => 'monthly',
                'currency' => 'AED',
                'job_description' => 'Construction and building maintenance work',
            ]
        );

        UserWorkExperience::updateOrCreate(
            [
                'user_id' => $user->id,
                'company_name' => 'Saudi Binladin Group',
                'position' => 'Construction Worker',
            ],
            [
                'company_name' => 'Saudi Binladin Group',
                'position' => 'Construction Worker',
                'country' => 'SA',
                'city' => 'Riyadh',
                'start_date' => '2018-03-10',
                'end_date' => '2021-12-20',
                'is_current_employment' => false,
                'salary' => 1200,
                'salary_period' => 'monthly',
                'currency' => 'SAR',
                'job_description' => 'General construction work on residential and commercial projects',
            ]
        );

        // Languages
        UserLanguage::updateOrCreate(
            ['user_id' => $user->id, 'language' => 'Bengali'],
            [
                'language' => 'Bengali',
                'proficiency' => 'native',
                'test_taken' => 'none',
            ]
        );

        UserLanguage::updateOrCreate(
            ['user_id' => $user->id, 'language' => 'English'],
            [
                'language' => 'English',
                'proficiency' => 'basic',
                'test_taken' => 'none',
            ]
        );

        UserLanguage::updateOrCreate(
            ['user_id' => $user->id, 'language' => 'Arabic'],
            [
                'language' => 'Arabic',
                'proficiency' => 'intermediate',
                'test_taken' => 'none',
            ]
        );

        // Financial Info
        UserFinancialInformation::updateOrCreate(
            ['user_id' => $user->id],
            [
                'annual_income' => 540000,
                'income_currency' => 'BDT',
                'source_of_income' => 'employment_salary',
                'employer_name' => 'Al Futtaim Group',
                'property_ownership' => 'none',
                'bank_balance' => 150000,
                'bank_name' => 'Dutch-Bangla Bank',
            ]
        );

        // Security Info
        UserSecurityInformation::updateOrCreate(
            ['user_id' => $user->id],
            [
                'has_criminal_record' => false,
                'has_visa_refusal' => false,
                'has_overstay_history' => false,
                'has_deportation_history' => false,
            ]
        );

        echo "  ✅ Created: {$user->email} - Gulf Worker going to Dubai\n\n";
    }

    /**
     * Profile 2: University Student - Going to UK
     */
    private function createStudentProfile()
    {
        echo "🎓 Creating Student Profile (UK-bound)...\n";

        $user = User::updateOrCreate(
            ['email' => 'nafisa.ahmed@test.com'],
            [
                'name' => 'Nafisa Ahmed',
                'password' => Hash::make('password'),
            ]
        );

        // Profile
        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'dob' => '2002-03-22',
                'gender' => 'female',
                'phone' => '+8801823456789',
                'nid' => '2345678901234',
                'present_address_line' => 'Dhanmondi',
                'present_city' => 'Dhaka',
                'present_division' => 'Dhaka',
                'permanent_address_line' => 'Dhanmondi',
                'permanent_city' => 'Dhaka',
                'permanent_division' => 'Dhaka',
                'nationality' => 'Bangladeshi',
                'marital_status' => 'single',
            ]
        );

        // Passport
        $passport = UserPassport::updateOrCreate(
            ['user_id' => $user->id, 'passport_number' => 'BT1234567'],
            [
                'surname' => 'AHMED',
                'given_names' => 'NAFISA',
                'passport_number' => 'BT1234567',
                'issuing_country' => 'BD',
                'issue_date' => '2024-02-10',
                'expiry_date' => '2029-02-09',
                'place_of_birth' => 'Dhaka',
                'sex' => 'F',
                'is_current_passport' => true,
            ]
        );

        // Visa - UK Student Visa (Approved)
        UserVisaHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country' => 'GB',
                'visa_type' => 'student',
                'application_date' => '2024-05-15',
            ],
            [
                'user_passport_id' => $passport->id,
                'country' => 'GB',
                'visa_type' => 'student',
                'visa_category' => 'Tier 4 Student Visa',
                'visa_number' => 'GB20241234567',
                'application_date' => '2024-05-15',
                'issue_date' => '2024-07-20',
                'expiry_date' => '2027-09-30',
                'status' => 'approved',
                'was_visa_rejected' => false,
                'entry_date' => '2024-09-01',
                'purpose_of_visit' => 'Masters in Computer Science at University of Manchester',
                'embassy_location' => 'British High Commission, Dhaka',
                'visa_fee' => 45000,
                'currency' => 'BDT',
                'notes' => 'Student visa for 2-year Masters program. CAS from University of Manchester.',
            ]
        );

        // Travel History - India (Tourism)
        UserTravelHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country_visited' => 'IN',
                'entry_date' => '2023-12-20',
            ],
            [
                'user_passport_id' => $passport->id,
                'country_visited' => 'IN',
                'city_visited' => 'Kolkata',
                'entry_date' => '2023-12-20',
                'exit_date' => '2023-12-27',
                'duration_days' => 7,
                'purpose' => 'tourism',
                'compliance_issues' => false,
                'notes' => 'Short vacation trip to Kolkata',
            ]
        );

        // Family
        UserFamilyMember::updateOrCreate(
            [
                'user_id' => $user->id,
                'full_name' => 'Dr. Kamal Ahmed',
                'relationship' => 'father',
            ],
            [
                'full_name' => 'Dr. Kamal Ahmed',
                'relationship' => 'father',
                'date_of_birth' => '1968-05-10',
                'gender' => 'male',
                'nationality' => 'Bangladeshi',
                'occupation' => 'Physician',
                'employer_name' => 'Square Hospital, Dhaka',
                'country_of_residence' => 'BD',

            ]
        );

        UserFamilyMember::updateOrCreate(
            [
                'user_id' => $user->id,
                'full_name' => 'Shahana Ahmed',
                'relationship' => 'mother',
            ],
            [
                'full_name' => 'Shahana Ahmed',
                'relationship' => 'mother',
                'date_of_birth' => '1972-08-15',
                'gender' => 'female',
                'nationality' => 'Bangladeshi',
                'occupation' => 'University Professor',
                'employer_name' => 'Dhaka University',
                'country_of_residence' => 'BD',

            ]
        );

        // Education
        UserEducation::updateOrCreate(
            [
                'user_id' => $user->id,
                'degree' => 'Bachelor of Science (B.Sc.)',
                'institution_name' => 'North South University',
            ],
            [
                'degree' => 'Bachelor of Science (B.Sc.)',
                'field_of_study' => 'Computer Science',
                'institution_name' => 'North South University',
                'country' => 'BD',
                'city' => 'Dhaka',
                'start_date' => '2019-01-10',
                'end_date' => '2023-05-20',
                'is_completed' => true,
                'gpa_or_grade' => '3.85/4.00',
                'language_of_instruction' => 'English',
                'honors_awards' => "Dean's List (2021, 2022, 2023), Best Final Year Project Award",
            ]
        );

        UserEducation::updateOrCreate(
            [
                'user_id' => $user->id,
                'degree' => 'Higher Secondary (HSC)',
                'institution_name' => 'Viqarunnisa Noon School & College',
            ],
            [
                'degree' => 'Higher Secondary (HSC)',
                'field_of_study' => 'Science',
                'institution_name' => 'Viqarunnisa Noon School & College',
                'country' => 'BD',
                'city' => 'Dhaka',
                'start_date' => '2016-06-01',
                'end_date' => '2018-05-31',
                'is_completed' => true,
                'gpa_or_grade' => '5.00/5.00',
                'language_of_instruction' => 'Bengali & English',
            ]
        );

        // Languages
        UserLanguage::updateOrCreate(
            ['user_id' => $user->id, 'language' => 'Bengali'],
            [
                'language' => 'Bengali',
                'proficiency' => 'native',
                'test_taken' => 'none',
            ]
        );

        UserLanguage::updateOrCreate(
            ['user_id' => $user->id, 'language' => 'English'],
            [
                'language' => 'English',
                'proficiency' => 'advanced',
                'test_taken' => 'IELTS',
                'test_score' => '7.5',
                'test_date' => '2024-03-15',
            ]
        );

        UserLanguage::updateOrCreate(
            ['user_id' => $user->id, 'language' => 'Hindi'],
            [
                'language' => 'Hindi',
                'proficiency' => 'intermediate',
                'test_taken' => 'none',
            ]
        );

        // Financial Info - Strong family support
        UserFinancialInformation::updateOrCreate(
            ['user_id' => $user->id],
            [
                'annual_income' => 0,
                'income_currency' => 'BDT',
                'source_of_income' => 'family_support',
                'has_sponsor' => true,
                'sponsor_name' => 'Dr. Kamal Ahmed',
                'sponsor_relationship' => 'Father',
                'sponsor_occupation' => 'Physician',
                'sponsor_annual_income' => 3500000,
                'property_ownership' => 'family_owned',
                'bank_balance' => 2500000,
                'bank_name' => 'Standard Chartered Bank',
            ]
        );

        // Security Info
        UserSecurityInformation::updateOrCreate(
            ['user_id' => $user->id],
            [
                'has_criminal_record' => false,
                'has_visa_refusal' => false,
                'has_overstay_history' => false,
                'has_deportation_history' => false,
            ]
        );

        echo "  ✅ Created: {$user->email} - Student going to UK for Masters\n\n";
    }

    /**
     * Profile 3: Business Person - Multiple countries
     */
    private function createBusinessPersonProfile()
    {
        echo "💼 Creating Business Person Profile...\n";

        $user = User::updateOrCreate(
            ['email' => 'shahid.islam@test.com'],
            [
                'name' => 'Shahid Islam',
                'password' => Hash::make('password'),
            ]
        );

        // Profile
        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'dob' => '1978-11-08',
                'gender' => 'male',
                'phone' => '+8801934567890',
                'nid' => '3456789012345',
                'present_address_line' => 'Gulshan-2',
                'present_city' => 'Dhaka',
                'present_division' => 'Dhaka',
                'permanent_address_line' => 'Chittagong',
                'permanent_city' => 'Chittagong',
                'permanent_division' => 'Chittagong',
                'nationality' => 'Bangladeshi',
                'marital_status' => 'married',
            ]
        );

        // Passport
        $passport = UserPassport::updateOrCreate(
            ['user_id' => $user->id, 'passport_number' => 'BU9876543'],
            [
                'surname' => 'ISLAM',
                'given_names' => 'SHAHID',
                'passport_number' => 'BU9876543',
                'issuing_country' => 'BD',
                'issue_date' => '2022-06-20',
                'expiry_date' => '2027-06-19',
                'place_of_birth' => 'Chittagong',
                'sex' => 'M',
                'is_current_passport' => true,
            ]
        );

        // Multiple Business Visas
        UserVisaHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country' => 'AE',
                'visa_type' => 'business',
                'application_date' => '2024-01-10',
            ],
            [
                'user_passport_id' => $passport->id,
                'country' => 'AE',
                'visa_type' => 'business',
                'visa_category' => 'Multiple Entry Business',
                'visa_number' => 'AE20240987654',
                'application_date' => '2024-01-10',
                'issue_date' => '2024-02-15',
                'expiry_date' => '2026-02-14',
                'status' => 'approved',
                'was_visa_rejected' => false,
                'purpose_of_visit' => 'Business meetings and trade negotiations for garment export',
                'embassy_location' => 'UAE Embassy, Dhaka',
                'visa_fee' => 35000,
                'currency' => 'BDT',
                'notes' => '2-year multiple entry business visa',
            ]
        );

        UserVisaHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country' => 'CN',
                'visa_type' => 'business',
                'application_date' => '2023-08-20',
            ],
            [
                'user_passport_id' => $passport->id,
                'country' => 'CN',
                'visa_type' => 'business',
                'visa_category' => 'M Visa (Business)',
                'visa_number' => 'CN20230456789',
                'application_date' => '2023-08-20',
                'issue_date' => '2023-09-10',
                'expiry_date' => '2024-09-09',
                'status' => 'expired',
                'was_visa_rejected' => false,
                'entry_date' => '2023-10-05',
                'exit_date' => '2023-10-15',
                'duration_days' => 10,
                'purpose_of_visit' => 'Textile machinery purchase from Guangzhou',
                'embassy_location' => 'Chinese Embassy, Dhaka',
                'visa_fee' => 28000,
                'currency' => 'BDT',
            ]
        );

        UserVisaHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country' => 'IN',
                'visa_type' => 'business',
                'application_date' => '2024-06-01',
            ],
            [
                'user_passport_id' => $passport->id,
                'country' => 'IN',
                'visa_type' => 'business',
                'visa_category' => 'Business e-Visa',
                'visa_number' => 'IN20240123456',
                'application_date' => '2024-06-01',
                'issue_date' => '2024-06-05',
                'expiry_date' => '2025-06-04',
                'status' => 'approved',
                'was_visa_rejected' => false,
                'purpose_of_visit' => 'Trade fair attendance in Mumbai',
                'embassy_location' => 'Indian High Commission, Dhaka',
                'visa_fee' => 8000,
                'currency' => 'BDT',
            ]
        );

        // Multiple Travel History
        UserTravelHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country_visited' => 'AE',
                'entry_date' => '2024-03-10',
            ],
            [
                'user_passport_id' => $passport->id,
                'country_visited' => 'AE',
                'city_visited' => 'Dubai',
                'entry_date' => '2024-03-10',
                'exit_date' => '2024-03-17',
                'duration_days' => 7,
                'purpose' => 'business',
                'compliance_issues' => false,
                'notes' => 'Business meetings with garment buyers at Dubai Textile City',
            ]
        );

        UserTravelHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country_visited' => 'CN',
                'entry_date' => '2023-10-05',
            ],
            [
                'user_passport_id' => $passport->id,
                'country_visited' => 'CN',
                'city_visited' => 'Guangzhou',
                'entry_date' => '2023-10-05',
                'exit_date' => '2023-10-15',
                'duration_days' => 10,
                'purpose' => 'business',
                'compliance_issues' => false,
                'notes' => 'Canton Fair attendance and machinery purchase',
            ]
        );

        UserTravelHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country_visited' => 'IN',
                'entry_date' => '2024-07-22',
            ],
            [
                'user_passport_id' => $passport->id,
                'country_visited' => 'IN',
                'city_visited' => 'Mumbai',
                'entry_date' => '2024-07-22',
                'exit_date' => '2024-07-28',
                'duration_days' => 6,
                'purpose' => 'business',
                'compliance_issues' => false,
                'notes' => 'India International Textile Machinery Exhibition',
            ]
        );

        // Family
        UserFamilyMember::updateOrCreate(
            [
                'user_id' => $user->id,
                'full_name' => 'Taslima Islam',
                'relationship' => 'spouse',
            ],
            [
                'full_name' => 'Taslima Islam',
                'relationship' => 'spouse',
                'date_of_birth' => '1982-04-12',
                'gender' => 'female',
                'nationality' => 'Bangladeshi',
                'occupation' => 'Fashion Designer',
                'country_of_residence' => 'BD',

            ]
        );

        UserFamilyMember::updateOrCreate(
            [
                'user_id' => $user->id,
                'full_name' => 'Rafi Islam',
                'relationship' => 'son',
            ],
            [
                'full_name' => 'Rafi Islam',
                'relationship' => 'son',
                'date_of_birth' => '2008-09-05',
                'gender' => 'male',
                'nationality' => 'Bangladeshi',
                'country_of_residence' => 'BD',

            ]
        );

        UserFamilyMember::updateOrCreate(
            [
                'user_id' => $user->id,
                'full_name' => 'Sadia Islam',
                'relationship' => 'son',
            ],
            [
                'full_name' => 'Sadia Islam',
                'relationship' => 'son',
                'date_of_birth' => '2012-03-18',
                'gender' => 'female',
                'nationality' => 'Bangladeshi',
                'country_of_residence' => 'BD',

            ]
        );

        // Education
        UserEducation::updateOrCreate(
            [
                'user_id' => $user->id,
                'degree' => 'Bachelor of Business Administration (BBA)',
                'institution_name' => 'University of Dhaka',
            ],
            [
                'degree' => 'Bachelor of Business Administration (BBA)',
                'field_of_study' => 'Business Administration',
                'institution_name' => 'University of Dhaka',
                'country' => 'BD',
                'city' => 'Dhaka',
                'start_date' => '1996-07-01',
                'end_date' => '2000-05-31',
                'is_completed' => true,
                'gpa_or_grade' => '3.45/4.00',
                'language_of_instruction' => 'English',
            ]
        );

        // Work Experience - Business Owner
        UserWorkExperience::updateOrCreate(
            [
                'user_id' => $user->id,
                'company_name' => 'Islam Garments Ltd.',
                'position' => 'Managing Director & Owner',
            ],
            [
                'company_name' => 'Islam Garments Ltd.',
                'position' => 'Managing Director & Owner',
                'country' => 'BD',
                'city' => 'Dhaka',
                'start_date' => '2005-01-01',
                'end_date' => null,
                'is_current_employment' => true,
                'salary' => 500000,
                'salary_period' => 'monthly',
                'currency' => 'BDT',
                'job_description' => 'Own and operate RMG export business. 500+ employees. Export to USA, Europe, Middle East.',
            ]
        );

        UserWorkExperience::updateOrCreate(
            [
                'user_id' => $user->id,
                'company_name' => 'Beximco Textiles',
                'position' => 'Export Manager',
            ],
            [
                'company_name' => 'Beximco Textiles',
                'position' => 'Export Manager',
                'country' => 'BD',
                'city' => 'Dhaka',
                'start_date' => '2000-06-01',
                'end_date' => '2004-12-31',
                'is_current_employment' => false,
                'salary' => 60000,
                'salary_period' => 'monthly',
                'currency' => 'BDT',
                'job_description' => 'Managed international export operations and client relationships',
            ]
        );

        // Languages
        UserLanguage::updateOrCreate(
            ['user_id' => $user->id, 'language' => 'Bengali'],
            [
                'language' => 'Bengali',
                'proficiency' => 'native',
                'test_taken' => 'none',
            ]
        );

        UserLanguage::updateOrCreate(
            ['user_id' => $user->id, 'language' => 'English'],
            [
                'language' => 'English',
                'proficiency' => 'advanced',
                'test_taken' => 'none',
            ]
        );

        UserLanguage::updateOrCreate(
            ['user_id' => $user->id, 'language' => 'Hindi'],
            [
                'language' => 'Hindi',
                'proficiency' => 'intermediate',
                'test_taken' => 'none',
            ]
        );

        // Financial Info - Business owner
        UserFinancialInformation::updateOrCreate(
            ['user_id' => $user->id],
            [
                'annual_income' => 6000000,
                'income_currency' => 'BDT',
                'source_of_income' => 'business_owner',
                'business_name' => 'Islam Garments Ltd.',
                'property_ownership' => 'owned',
                'property_value' => 15000000,
                'bank_balance' => 8000000,
                'bank_name' => 'HSBC Bangladesh',
                'other_assets' => 'Factory building in Ashulia, 2 commercial properties in Gulshan',
            ]
        );

        // Security Info
        UserSecurityInformation::updateOrCreate(
            ['user_id' => $user->id],
            [
                'has_criminal_record' => false,
                'has_visa_refusal' => false,
                'has_overstay_history' => false,
                'has_deportation_history' => false,
            ]
        );

        echo "  ✅ Created: {$user->email} - Business person with multiple country travel\n\n";
    }

    /**
     * Profile 4: IT Professional - Canada PR applicant
     */
    private function createITWorkerProfile()
    {
        echo "💻 Creating IT Professional Profile (Canada-bound)...\n";

        $user = User::updateOrCreate(
            ['email' => 'tanvir.rahman@test.com'],
            [
                'name' => 'Tanvir Rahman',
                'password' => Hash::make('password'),
            ]
        );

        // Profile
        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'dob' => '1988-07-14',
                'gender' => 'male',
                'phone' => '+8801645678901',
                'nid' => '4567890123456',
                'present_address_line' => 'Banani',
                'present_city' => 'Dhaka',
                'present_division' => 'Dhaka',
                'permanent_address_line' => 'Sylhet',
                'permanent_city' => 'Sylhet',
                'permanent_division' => 'Sylhet',
                'nationality' => 'Bangladeshi',
                'marital_status' => 'married',
            ]
        );

        // Passport
        $passport = UserPassport::updateOrCreate(
            ['user_id' => $user->id, 'passport_number' => 'BR2345678'],
            [
                'surname' => 'RAHMAN',
                'given_names' => 'TANVIR',
                'passport_number' => 'BR2345678',
                'issuing_country' => 'BD',
                'issue_date' => '2023-09-05',
                'expiry_date' => '2028-09-04',
                'place_of_birth' => 'Sylhet',
                'sex' => 'M',
                'is_current_passport' => true,
            ]
        );

        // Canada PR application in progress
        UserVisaHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country' => 'CA',
                'visa_type' => 'work',
                'application_date' => '2024-03-01',
            ],
            [
                'user_passport_id' => $passport->id,
                'country' => 'CA',
                'visa_type' => 'work',
                'visa_category' => 'Express Entry - Federal Skilled Worker',
                'application_reference' => 'CA2024567890',
                'application_date' => '2024-03-01',
                'status' => 'pending',
                'was_visa_rejected' => false,
                'purpose_of_visit' => 'Permanent Residence application under Express Entry FSW program',
                'embassy_location' => 'Canadian High Commission, Singapore (processing)',
                'visa_fee' => 150000,
                'currency' => 'BDT',
                'notes' => 'Express Entry PR application. CRS Score: 478. NOC: 21232 Software Developer',
            ]
        );

        // Previous US tourist visa (approved)
        UserVisaHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country' => 'US',
                'visa_type' => 'tourist',
                'application_date' => '2022-05-10',
            ],
            [
                'user_passport_id' => $passport->id,
                'country' => 'US',
                'visa_type' => 'tourist',
                'visa_category' => 'B1/B2',
                'visa_number' => 'US20220987654',
                'application_date' => '2022-05-10',
                'issue_date' => '2022-06-20',
                'expiry_date' => '2032-06-19',
                'status' => 'approved',
                'was_visa_rejected' => false,
                'entry_date' => '2022-10-01',
                'exit_date' => '2022-10-15',
                'duration_days' => 14,
                'purpose_of_visit' => 'Tourism - visiting New York and Washington DC',
                'embassy_location' => 'US Embassy, Dhaka',
                'visa_fee' => 18000,
                'currency' => 'BDT',
            ]
        );

        // Travel History
        UserTravelHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country_visited' => 'US',
                'entry_date' => '2022-10-01',
            ],
            [
                'user_passport_id' => $passport->id,
                'country_visited' => 'US',
                'city_visited' => 'New York',
                'entry_date' => '2022-10-01',
                'exit_date' => '2022-10-15',
                'duration_days' => 14,
                'purpose' => 'tourism',
                'compliance_issues' => false,
                'notes' => 'Tourism trip. Visited New York, Washington DC, Boston',
            ]
        );

        UserTravelHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country_visited' => 'TH',
                'entry_date' => '2024-02-10',
            ],
            [
                'user_passport_id' => $passport->id,
                'country_visited' => 'TH',
                'city_visited' => 'Bangkok',
                'entry_date' => '2024-02-10',
                'exit_date' => '2024-02-17',
                'duration_days' => 7,
                'purpose' => 'tourism',
                'compliance_issues' => false,
                'notes' => 'Family vacation in Bangkok and Phuket',
            ]
        );

        // Family
        UserFamilyMember::updateOrCreate(
            [
                'user_id' => $user->id,
                'full_name' => 'Nusrat Jahan',
                'relationship' => 'spouse',
            ],
            [
                'full_name' => 'Nusrat Jahan',
                'relationship' => 'spouse',
                'date_of_birth' => '1990-12-22',
                'gender' => 'female',
                'nationality' => 'Bangladeshi',
                'occupation' => 'Teacher',
                'employer_name' => 'Sunbeam School, Dhaka',
                'country_of_residence' => 'BD',

            ]
        );

        UserFamilyMember::updateOrCreate(
            [
                'user_id' => $user->id,
                'full_name' => 'Aayan Rahman',
                'relationship' => 'son',
            ],
            [
                'full_name' => 'Aayan Rahman',
                'relationship' => 'son',
                'date_of_birth' => '2018-04-08',
                'gender' => 'male',
                'nationality' => 'Bangladeshi',
                'country_of_residence' => 'BD',

            ]
        );

        // Education
        UserEducation::updateOrCreate(
            [
                'user_id' => $user->id,
                'degree' => 'Master of Science (M.Sc.)',
                'institution_name' => 'Bangladesh University of Engineering and Technology',
            ],
            [
                'degree' => 'Master of Science (M.Sc.)',
                'field_of_study' => 'Computer Science',
                'institution_name' => 'Bangladesh University of Engineering and Technology',
                'country' => 'BD',
                'city' => 'Dhaka',
                'start_date' => '2011-01-01',
                'end_date' => '2013-12-31',
                'is_completed' => true,
                'gpa_or_grade' => '3.78/4.00',
                'language_of_instruction' => 'English',
                'honors_awards' => 'University Merit Scholarship',
            ]
        );

        UserEducation::updateOrCreate(
            [
                'user_id' => $user->id,
                'degree' => 'Bachelor of Science (B.Sc.)',
                'institution_name' => 'Bangladesh University of Engineering and Technology',
            ],
            [
                'degree' => 'Bachelor of Science (B.Sc.)',
                'field_of_study' => 'Computer Science & Engineering',
                'institution_name' => 'Bangladesh University of Engineering and Technology',
                'country' => 'BD',
                'city' => 'Dhaka',
                'start_date' => '2006-03-01',
                'end_date' => '2010-12-31',
                'is_completed' => true,
                'gpa_or_grade' => '3.65/4.00',
                'language_of_instruction' => 'English',
            ]
        );

        // Work Experience
        UserWorkExperience::updateOrCreate(
            [
                'user_id' => $user->id,
                'company_name' => 'Brain Station 23',
                'position' => 'Senior Software Engineer',
            ],
            [
                'company_name' => 'Brain Station 23',
                'position' => 'Senior Software Engineer',
                'country' => 'BD',
                'city' => 'Dhaka',
                'start_date' => '2018-03-01',
                'end_date' => null,
                'is_current_employment' => true,
                'salary' => 180000,
                'salary_period' => 'monthly',
                'currency' => 'BDT',
                'job_description' => 'Full-stack development using React, Node.js, Python. Leading team of 5 developers.',
            ]
        );

        UserWorkExperience::updateOrCreate(
            [
                'user_id' => $user->id,
                'company_name' => 'Samsung R&D Institute Bangladesh',
                'position' => 'Software Engineer',
            ],
            [
                'company_name' => 'Samsung R&D Institute Bangladesh',
                'position' => 'Software Engineer',
                'country' => 'BD',
                'city' => 'Dhaka',
                'start_date' => '2014-01-15',
                'end_date' => '2018-02-28',
                'is_current_employment' => false,
                'salary' => 100000,
                'salary_period' => 'monthly',
                'currency' => 'BDT',
                'job_description' => 'Android application development for Samsung mobile devices',
            ]
        );

        // Languages
        UserLanguage::updateOrCreate(
            ['user_id' => $user->id, 'language' => 'Bengali'],
            [
                'language' => 'Bengali',
                'proficiency' => 'native',
                'test_taken' => 'none',
            ]
        );

        UserLanguage::updateOrCreate(
            ['user_id' => $user->id, 'language' => 'English'],
            [
                'language' => 'English',
                'proficiency' => 'advanced',
                'test_taken' => 'IELTS',
                'test_score' => '8.0',
                'test_date' => '2024-01-20',
            ]
        );

        // Financial Info
        UserFinancialInformation::updateOrCreate(
            ['user_id' => $user->id],
            [
                'annual_income' => 2160000,
                'income_currency' => 'BDT',
                'source_of_income' => 'employment_salary',
                'employer_name' => 'Brain Station 23',
                'property_ownership' => 'owned',
                'property_value' => 12000000,
                'bank_balance' => 3500000,
                'bank_name' => 'City Bank',
                'other_assets' => 'Apartment in Banani (owned), Mutual fund investments',
            ]
        );

        // Security Info
        UserSecurityInformation::updateOrCreate(
            ['user_id' => $user->id],
            [
                'has_criminal_record' => false,
                'has_visa_refusal' => false,
                'has_overstay_history' => false,
                'has_deportation_history' => false,
            ]
        );

        echo "  ✅ Created: {$user->email} - IT Professional applying for Canada PR\n\n";
    }

    /**
     * Profile 5: Family Migration - Australia family reunion
     */
    private function createFamilyMigrationProfile()
    {
        echo "👨‍👩‍👧 Creating Family Migration Profile (Australia-bound)...\n";

        $user = User::updateOrCreate(
            ['email' => 'rashida.begum@test.com'],
            [
                'name' => 'Rashida Begum',
                'password' => Hash::make('password'),
            ]
        );

        // Profile
        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'dob' => '1975-09-30',
                'gender' => 'female',
                'phone' => '+8801756789012',
                'nid' => '5678901234567',
                'present_address_line' => 'Uttara',
                'present_city' => 'Dhaka',
                'present_division' => 'Dhaka',
                'permanent_address_line' => 'Mymensingh',
                'permanent_city' => 'Mymensingh',
                'permanent_division' => 'Mymensingh',
                'nationality' => 'Bangladeshi',
                'marital_status' => 'widowed',
            ]
        );

        // Passport
        $passport = UserPassport::updateOrCreate(
            ['user_id' => $user->id, 'passport_number' => 'BE3456789'],
            [
                'surname' => 'BEGUM',
                'given_names' => 'RASHIDA',
                'passport_number' => 'BE3456789',
                'issuing_country' => 'BD',
                'issue_date' => '2023-11-20',
                'expiry_date' => '2028-11-19',
                'place_of_birth' => 'Mymensingh',
                'sex' => 'F',
                'is_current_passport' => true,
            ]
        );

        // Australia Parent visa application
        UserVisaHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country' => 'AU',
                'visa_type' => 'family_visit',
                'application_date' => '2024-04-15',
            ],
            [
                'user_passport_id' => $passport->id,
                'country' => 'AU',
                'visa_type' => 'family_visit',
                'visa_category' => 'Subclass 103 Parent Visa',
                'application_reference' => 'AU2024345678',
                'application_date' => '2024-04-15',
                'status' => 'pending',
                'was_visa_rejected' => false,
                'purpose_of_visit' => 'Family reunion with son (Australian citizen). Parent visa application.',
                'embassy_location' => 'Australian High Commission, Dhaka',
                'visa_fee' => 280000,
                'currency' => 'BDT',
                'notes' => 'Son is Australian citizen (sponsoring). Subclass 103 Parent visa. Processing time: 12-30 years.',
            ]
        );

        // Previous visitor visa (approved)
        UserVisaHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country' => 'AU',
                'visa_type' => 'tourist',
                'application_date' => '2023-03-10',
            ],
            [
                'user_passport_id' => $passport->id,
                'country' => 'AU',
                'visa_type' => 'tourist',
                'visa_category' => 'Visitor Visa (Subclass 600)',
                'visa_number' => 'AU20230456789',
                'application_date' => '2023-03-10',
                'issue_date' => '2023-04-25',
                'expiry_date' => '2024-04-24',
                'status' => 'expired',
                'was_visa_rejected' => false,
                'entry_date' => '2023-06-01',
                'exit_date' => '2023-08-30',
                'duration_days' => 90,
                'purpose_of_visit' => 'Visit son and grandchildren in Sydney',
                'embassy_location' => 'Australian High Commission, Dhaka',
                'visa_fee' => 18000,
                'currency' => 'BDT',
            ]
        );

        // Travel History
        UserTravelHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country_visited' => 'AU',
                'entry_date' => '2023-06-01',
            ],
            [
                'user_passport_id' => $passport->id,
                'country_visited' => 'AU',
                'city_visited' => 'Sydney',
                'entry_date' => '2023-06-01',
                'exit_date' => '2023-08-30',
                'duration_days' => 90,
                'purpose' => 'family_visit',
                'compliance_issues' => false,
                'notes' => 'Visited son (Australian citizen) and grandchildren for 3 months. Returned on time.',
            ]
        );

        // Family - Son in Australia
        UserFamilyMember::updateOrCreate(
            [
                'user_id' => $user->id,
                'full_name' => 'Jahangir Alam',
                'relationship' => 'son',
            ],
            [
                'full_name' => 'Jahangir Alam',
                'relationship' => 'son',
                'date_of_birth' => '1995-02-14',
                'gender' => 'male',
                'nationality' => 'Australian',
                'occupation' => 'Civil Engineer',
                'country_of_residence' => 'AU',

                'notes' => 'Australian citizen. Sponsor for parent visa. Living in Sydney since 2016.',
            ]
        );

        UserFamilyMember::updateOrCreate(
            [
                'user_id' => $user->id,
                'full_name' => 'Zara Alam',
                'relationship' => 'son',
            ],
            [
                'full_name' => 'Zara Alam',
                'relationship' => 'son',
                'date_of_birth' => '1998-11-20',
                'gender' => 'female',
                'nationality' => 'Bangladeshi',
                'occupation' => 'Pharmacist',
                'country_of_residence' => 'BD',

            ]
        );

        // Education
        UserEducation::updateOrCreate(
            [
                'user_id' => $user->id,
                'degree' => 'Bachelor of Arts (B.A.)',
                'institution_name' => 'Eden College',
            ],
            [
                'degree' => 'Bachelor of Arts (B.A.)',
                'field_of_study' => 'English Literature',
                'institution_name' => 'Eden College',
                'country' => 'BD',
                'city' => 'Dhaka',
                'start_date' => '1993-07-01',
                'end_date' => '1997-06-30',
                'is_completed' => true,
                'gpa_or_grade' => 'Second Class',
                'language_of_instruction' => 'English & Bengali',
            ]
        );

        // Previous work (now retired)
        UserWorkExperience::updateOrCreate(
            [
                'user_id' => $user->id,
                'company_name' => 'Mymensingh Girls School',
                'position' => 'School Teacher (English)',
            ],
            [
                'company_name' => 'Mymensingh Girls School',
                'position' => 'School Teacher (English)',
                'country' => 'BD',
                'city' => 'Mymensingh',
                'start_date' => '1997-07-01',
                'end_date' => '2020-06-30',
                'is_current_employment' => false,
                'salary' => 35000,
                'salary_period' => 'monthly',
                'currency' => 'BDT',
                'job_description' => 'English teacher for secondary school. Retired after 23 years of service.',
            ]
        );

        // Languages
        UserLanguage::updateOrCreate(
            ['user_id' => $user->id, 'language' => 'Bengali'],
            [
                'language' => 'Bengali',
                'proficiency' => 'native',
                'test_taken' => 'none',
            ]
        );

        UserLanguage::updateOrCreate(
            ['user_id' => $user->id, 'language' => 'English'],
            [
                'language' => 'English',
                'proficiency' => 'advanced',
                'test_taken' => 'none',
            ]
        );

        // Financial Info - Supported by son
        UserFinancialInformation::updateOrCreate(
            ['user_id' => $user->id],
            [
                'annual_income' => 240000,
                'income_currency' => 'BDT',
                'source_of_income' => 'pension',
                'has_sponsor' => true,
                'sponsor_name' => 'Jahangir Alam',
                'sponsor_relationship' => 'Son',
                'sponsor_occupation' => 'Civil Engineer',
                'sponsor_annual_income' => 120000,
                'sponsor_income_currency' => 'AUD',
                'property_ownership' => 'owned',
                'property_value' => 4500000,
                'bank_balance' => 800000,
                'bank_name' => 'Sonali Bank',
                'other_assets' => 'Apartment in Mymensingh (ancestral property)',
            ]
        );

        // Security Info
        UserSecurityInformation::updateOrCreate(
            ['user_id' => $user->id],
            [
                'has_criminal_record' => false,
                'has_visa_refusal' => false,
                'has_overstay_history' => false,
                'has_deportation_history' => false,
                'has_medical_conditions' => true,
                'medical_conditions' => 'Diabetes Type 2 (controlled with medication)',
            ]
        );

        echo "  ✅ Created: {$user->email} - Family migration to Australia (Parent visa)\n\n";
    }
}
