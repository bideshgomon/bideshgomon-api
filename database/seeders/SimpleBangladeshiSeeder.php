<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
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

class SimpleBangladeshiSeeder extends Seeder
{
    /**
     * Bangladesh-specific profile seeder
     * Uses actual schema from ProfileManagementSeeder
     */
    public function run(): void
    {
        echo "\n============================================================\n";
        echo "🇧🇩 BANGLADESHI PROFILE SEEDER - 3 Real Scenarios\n";
        echo "============================================================\n\n";

        $this->createGulfWorker();
        $this->createStudentApplicant();
        $this->createITprofessional();

        echo "\n============================================================\n";
        echo "✅ BANGLADESHI SEEDING COMPLETE!\n";
        echo "============================================================\n\n";
        echo "📧 Test Logins:\n";
        echo "  1. rahim.gulf@test.com / password (Gulf Worker)\n";
        echo "  2. nafisa.student@test.com / password (UK Student)\n";
        echo "  3. tanvir.it@test.com / password (Canada IT Professional)\n\n";
    }

    private function createGulfWorker()
    {
        echo "👷 Gulf Worker Profile (Saudi Arabia/UAE)...\n";

        // Create User
        $user = User::updateOrCreate(
            ['email' => 'rahim.gulf@test.com'],
            [
                'name' => 'Rahim Khan',
                'password' => Hash::make('password'),
            ]
        );

        // Passport
        $passport = UserPassport::updateOrCreate(
            ['user_id' => $user->id, 'passport_number' => 'BH0567890'],
            [
                'surname' => 'KHAN',
                'given_names' => 'RAHIM',
                'passport_number' => 'BH0567890',
                'issuing_country' => 'BD',
                'issue_date' => '2023-03-15',
                'expiry_date' => '2028-03-14',
                'place_of_birth' => 'Gazipur',
                'sex' => 'M',
                'is_current_passport' => true,
            ]
        );

        // Saudi Arabia Work Visa (Expired)
        UserVisaHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country' => 'SA',
                'visa_type' => 'work',
                'application_date' => '2018-01-10',
            ],
            [
                'user_passport_id' => $passport->id,
                'country' => 'SA',
                'visa_type' => 'work',
                'visa_category' => 'Work Permit',
                'visa_number' => 'SA20180123456',
                'application_date' => '2018-01-10',
                'issue_date' => '2018-02-20',
                'expiry_date' => '2021-02-19',
                'status' => 'expired',
                'was_visa_rejected' => false,
                'entry_date' => '2018-03-01',
                'exit_date' => '2021-01-15',
                'duration_of_stay' => 1051,
                'purpose_of_visit' => 'Construction worker at Binladin Group',
                'embassy_location' => 'Saudi Embassy, Dhaka',
                'visa_fee' => 28000,
                'currency' => 'BDT',
            ]
        );

        // UAE Work Visa (Current)
        UserVisaHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country' => 'AE',
                'visa_type' => 'work',
                'application_date' => '2024-07-01',
            ],
            [
                'user_passport_id' => $passport->id,
                'country' => 'AE',
                'visa_type' => 'work',
                'visa_category' => 'Employment Visa',
                'visa_number' => 'AE20240987654',
                'application_date' => '2024-07-01',
                'issue_date' => '2024-08-15',
                'expiry_date' => '2026-08-14',
                'status' => 'approved',
                'was_visa_rejected' => false,
                'entry_date' => '2024-09-01',
                'purpose_of_visit' => 'Construction worker at Al Futtaim Group, Dubai',
                'embassy_location' => 'UAE Embassy, Dhaka',
                'visa_fee' => 32000,
                'currency' => 'BDT',
            ]
        );

        // Travel History - Saudi Arabia
        UserTravelHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country_visited' => 'SA',
                'entry_date' => '2018-03-01',
            ],
            [
                'user_passport_id' => $passport->id,
                'country_visited' => 'SA',
                'city_visited' => 'Riyadh',
                'entry_date' => '2018-03-01',
                'exit_date' => '2021-01-15',
                'purpose' => 'work',
                'compliance_issues' => false,
                'notes' => 'Worked 3 years as construction worker. Contract completed successfully.',
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
                'date_of_birth' => '1992-08-15',
                'gender' => 'female',
                'nationality' => 'BD',
                'country_of_residence' => 'BD',
            ]
        );

        UserFamilyMember::updateOrCreate(
            [
                'user_id' => $user->id,
                'full_name' => 'Ayesha Khan',
                'relationship' => 'daughter',
            ],
            [
                'full_name' => 'Ayesha Khan',
                'relationship' => 'daughter',
                'date_of_birth' => '2015-05-10',
                'gender' => 'female',
                'nationality' => 'BD',
                'country_of_residence' => 'BD',
            ]
        );

        // Education - HSC
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
                'start_date' => '2024-09-01',
                'end_date' => null,
                'is_current_employment' => true,
                'salary' => 54000,
                'salary_period' => 'monthly',
                'currency' => 'BDT',
                'job_description' => 'Construction and maintenance work',
            ]
        );

        UserWorkExperience::updateOrCreate(
            [
                'user_id' => $user->id,
                'company_name' => 'Binladin Group',
                'position' => 'Construction Worker',
            ],
            [
                'company_name' => 'Binladin Group',
                'position' => 'Construction Worker',
                'country' => 'SA',
                'city' => 'Riyadh',
                'start_date' => '2018-03-01',
                'end_date' => '2021-01-15',
                'is_current_employment' => false,
                'salary' => 45000,
                'salary_period' => 'monthly',
                'currency' => 'BDT',
                'job_description' => 'General construction work',
            ]
        );

        // Languages
        UserLanguage::updateOrCreate(
            ['user_id' => $user->id, 'language' => 'Bengali'],
            ['language' => 'Bengali', 'proficiency' => 'native', 'test_taken' => 'none']
        );

        UserLanguage::updateOrCreate(
            ['user_id' => $user->id, 'language' => 'Arabic'],
            ['language' => 'Arabic', 'proficiency' => 'intermediate', 'test_taken' => 'none']
        );

        UserLanguage::updateOrCreate(
            ['user_id' => $user->id, 'language' => 'English'],
            ['language' => 'English', 'proficiency' => 'basic', 'test_taken' => 'none']
        );

        // Financial Info
        UserFinancialInformation::updateOrCreate(
            ['user_id' => $user->id],
            [
                'annual_income' => 648000,
                'currency' => 'BDT',
                'source_of_income' => 'salary',
                'employer_name' => 'Al Futtaim Group',
                'property_ownership' => 'renting',
                'current_balance' => 180000,
                'balance_currency' => 'BDT',
            ]
        );

        // Security
        UserSecurityInformation::updateOrCreate(
            ['user_id' => $user->id],
            [
                'has_criminal_record' => false,
                'has_been_deported' => false,
                'has_overstayed_visa' => false,
                'has_worked_illegally' => false,
            ]
        );

        echo "  ✅ Created: {$user->email} - Gulf worker profile\n\n";
    }

    private function createStudentApplicant()
    {
        echo "🎓 University Student Profile (UK-bound)...\n";

        $user = User::updateOrCreate(
            ['email' => 'nafisa.student@test.com'],
            [
                'name' => 'Nafisa Ahmed',
                'password' => Hash::make('password'),
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
                'issue_date' => '2024-01-10',
                'expiry_date' => '2029-01-09',
                'place_of_birth' => 'Dhaka',
                'sex' => 'F',
                'is_current_passport' => true,
            ]
        );

        // UK Student Visa
        UserVisaHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country' => 'GB',
                'visa_type' => 'student',
                'application_date' => '2024-05-01',
            ],
            [
                'user_passport_id' => $passport->id,
                'country' => 'GB',
                'visa_type' => 'student',
                'visa_category' => 'Tier 4 Student Visa',
                'visa_number' => 'GB20241234567',
                'application_date' => '2024-05-01',
                'issue_date' => '2024-07-15',
                'expiry_date' => '2027-09-30',
                'status' => 'approved',
                'was_visa_rejected' => false,
                'entry_date' => '2024-09-01',
                'purpose_of_visit' => 'Masters in Computer Science at University of Manchester',
                'embassy_location' => 'British High Commission, Dhaka',
                'visa_fee' => 48000,
                'currency' => 'BDT',
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
                'nationality' => 'BD',
                'occupation' => 'Physician',
                'employer_name' => 'Square Hospital',
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
                'date_of_birth' => '1972-08-20',
                'gender' => 'female',
                'nationality' => 'BD',
                'occupation' => 'Professor',
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
                'honors_awards' => "Dean's List 2021-2023",
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
            ['language' => 'Bengali', 'proficiency' => 'native', 'test_taken' => 'none']
        );

        UserLanguage::updateOrCreate(
            ['user_id' => $user->id, 'language' => 'English'],
            ['language' => 'English', 'proficiency' => 'advanced', 'test_taken' => 'ielts', 'test_score' => '7.5', 'test_date' => '2024-03-15']
        );

        UserLanguage::updateOrCreate(
            ['user_id' => $user->id, 'language' => 'Hindi'],
            ['language' => 'Hindi', 'proficiency' => 'intermediate', 'test_taken' => 'none']
        );

        // Financial Info - Family support
        UserFinancialInformation::updateOrCreate(
            ['user_id' => $user->id],
            [
                'annual_income' => 0,
                'currency' => 'BDT',
                'source_of_income' => 'family_support',
                'has_sponsor' => true,
                'sponsor_name' => 'Dr. Kamal Ahmed',
                'sponsor_relationship' => 'parent',
                'sponsor_annual_income' => 3500000,
                'sponsor_income_currency' => 'BDT',
                'property_ownership' => 'family_home',
                'current_balance' => 2500000,
                'balance_currency' => 'BDT',
            ]
        );

        // Security
        UserSecurityInformation::updateOrCreate(
            ['user_id' => $user->id],
            [
                'has_criminal_record' => false,
                'has_been_deported' => false,
                'has_overstayed_visa' => false,
                'has_worked_illegally' => false,
            ]
        );

        echo "  ✅ Created: {$user->email} - UK student profile\n\n";
    }

    private function createITprofessional()
    {
        echo "💻 IT Professional Profile (Canada PR applicant)...\n";

        $user = User::updateOrCreate(
            ['email' => 'tanvir.it@test.com'],
            [
                'name' => 'Tanvir Rahman',
                'password' => Hash::make('password'),
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
                'issue_date' => '2023-08-05',
                'expiry_date' => '2028-08-04',
                'place_of_birth' => 'Sylhet',
                'sex' => 'M',
                'is_current_passport' => true,
            ]
        );

        // Canada PR Application (Pending)
        UserVisaHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country' => 'CA',
                'visa_type' => 'work',
                'application_date' => '2024-02-01',
            ],
            [
                'user_passport_id' => $passport->id,
                'country' => 'CA',
                'visa_type' => 'work',
                'visa_category' => 'Express Entry FSW',
                'application_reference' => 'CA2024567890',
                'application_date' => '2024-02-01',
                'status' => 'pending',
                'was_visa_rejected' => false,
                'purpose_of_visit' => 'Permanent Residence - Express Entry Federal Skilled Worker',
                'embassy_location' => 'Canadian High Commission (Processing Singapore)',
                'visa_fee' => 165000,
                'currency' => 'BDT',
                'notes' => 'CRS Score: 475, NOC: 21232 Software Developer',
            ]
        );

        // USA Tourist Visa (Approved - strong travel history)
        UserVisaHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country' => 'US',
                'visa_type' => 'tourist',
                'application_date' => '2022-04-10',
            ],
            [
                'user_passport_id' => $passport->id,
                'country' => 'US',
                'visa_type' => 'tourist',
                'visa_category' => 'B1/B2',
                'visa_number' => 'US20220987654',
                'application_date' => '2022-04-10',
                'issue_date' => '2022-05-20',
                'expiry_date' => '2032-05-19',
                'status' => 'approved',
                'was_visa_rejected' => false,
                'entry_date' => '2022-09-01',
                'exit_date' => '2022-09-15',
                'duration_of_stay' => 14,
                'purpose_of_visit' => 'Tourism - visiting New York and Washington DC',
                'embassy_location' => 'US Embassy, Dhaka',
                'visa_fee' => 19000,
                'currency' => 'BDT',
            ]
        );

        // Travel History
        UserTravelHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country_visited' => 'US',
                'entry_date' => '2022-09-01',
            ],
            [
                'user_passport_id' => $passport->id,
                'country_visited' => 'US',
                'city_visited' => 'New York',
                'entry_date' => '2022-09-01',
                'exit_date' => '2022-09-15',
                'purpose' => 'tourism',
                'compliance_issues' => false,
                'notes' => 'Clean travel record. Returned on time.',
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
                'nationality' => 'BD',
                'occupation' => 'Teacher',
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
                'nationality' => 'BD',
                'country_of_residence' => 'BD',
            ]
        );

        // Education
        UserEducation::updateOrCreate(
            [
                'user_id' => $user->id,
                'degree' => 'Master of Science (M.Sc.)',
                'institution_name' => 'BUET',
            ],
            [
                'degree' => 'Master of Science (M.Sc.)',
                'field_of_study' => 'Computer Science',
                'institution_name' => 'BUET',
                'country' => 'BD',
                'city' => 'Dhaka',
                'start_date' => '2011-01-01',
                'end_date' => '2013-12-31',
                'is_completed' => true,
                'gpa_or_grade' => '3.78/4.00',
                'language_of_instruction' => 'English',
            ]
        );

        UserEducation::updateOrCreate(
            [
                'user_id' => $user->id,
                'degree' => 'Bachelor of Science (B.Sc.)',
                'institution_name' => 'BUET',
            ],
            [
                'degree' => 'Bachelor of Science (B.Sc.)',
                'field_of_study' => 'Computer Science & Engineering',
                'institution_name' => 'BUET',
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
                'job_description' => 'Full-stack development - React, Node.js, Python. Leading team of 5.',
            ]
        );

        UserWorkExperience::updateOrCreate(
            [
                'user_id' => $user->id,
                'company_name' => 'Samsung R&D Bangladesh',
                'position' => 'Software Engineer',
            ],
            [
                'company_name' => 'Samsung R&D Bangladesh',
                'position' => 'Software Engineer',
                'country' => 'BD',
                'city' => 'Dhaka',
                'start_date' => '2014-01-15',
                'end_date' => '2018-02-28',
                'is_current_employment' => false,
                'salary' => 100000,
                'salary_period' => 'monthly',
                'currency' => 'BDT',
                'job_description' => 'Android application development',
            ]
        );

        // Languages
        UserLanguage::updateOrCreate(
            ['user_id' => $user->id, 'language' => 'Bengali'],
            ['language' => 'Bengali', 'proficiency' => 'native', 'test_taken' => 'none']
        );

        UserLanguage::updateOrCreate(
            ['user_id' => $user->id, 'language' => 'English'],
            ['language' => 'English', 'proficiency' => 'advanced', 'test_taken' => 'ielts', 'test_score' => '8.0', 'test_date' => '2024-01-20']
        );

        // Financial Info
        UserFinancialInformation::updateOrCreate(
            ['user_id' => $user->id],
            [
                'annual_income' => 2160000,
                'currency' => 'BDT',
                'source_of_income' => 'salary',
                'employer_name' => 'Brain Station 23',
                'property_ownership' => 'own_home',
                'property_value' => 12000000,
                'property_value_currency' => 'BDT',
                'current_balance' => 3500000,
                'balance_currency' => 'BDT',
            ]
        );

        // Security
        UserSecurityInformation::updateOrCreate(
            ['user_id' => $user->id],
            [
                'has_criminal_record' => false,
                'has_been_deported' => false,
                'has_overstayed_visa' => false,
                'has_worked_illegally' => false,
            ]
        );

        echo "  ✅ Created: {$user->email} - IT professional profile\n\n";
    }
}
