<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\FamilyMember;
use App\Models\UserEducation;
use App\Models\UserWorkExperience;
use App\Models\UserLanguage;
use App\Models\UserPhoneNumber;
use App\Models\UserTravelHistory;
use App\Models\UserSecurityInformation;
use App\Models\Language;
use App\Models\LanguageTest;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find or create user role
        $userRole = Role::firstOrCreate(
            ['name' => 'user'],
            ['description' => 'Regular User']
        );

        // Delete existing demo user if exists
        User::where('email', 'demo@bideshgomon.com')->delete();

        // Create demo user
        $user = User::create([
            'name' => 'Ahmed Hassan',
            'email' => 'demo@bideshgomon.com',
            'password' => Hash::make('password123'),
            'phone' => '+8801712345678',
            'role_id' => $userRole->id,
            'email_verified_at' => now(),
        ]);

        // Create complete user profile
        UserProfile::create([
            'user_id' => $user->id,
            'first_name' => 'Ahmed',
            'middle_name' => 'Ibn',
            'last_name' => 'Hassan',
            'name_as_per_passport' => 'AHMED IBN HASSAN',
            'bio' => 'Experienced software engineer with a passion for international opportunities. Seeking to expand my career horizons abroad.',
            'phone' => '+8801712345678',
            'dob' => '1990-05-15',
            'gender' => 'male',
            'nationality' => 'Bangladeshi',
            'marital_status' => 'married',
            
            // Present Address
            'present_address_line' => 'House 45, Road 12, Dhanmondi',
            'present_city' => 'Dhaka',
            'present_division' => 'Dhaka',
            'present_district' => 'Dhaka',
            'present_postal_code' => '1209',
            
            // Permanent Address
            'permanent_address_line' => 'Village: Rampur, Post: Mirzapur',
            'permanent_city' => 'Tangail',
            'permanent_division' => 'Dhaka',
            'permanent_district' => 'Tangail',
            'permanent_postal_code' => '1900',
            
            // Documents
            'nid' => '1234567890123',
            'passport_number' => 'BG1234567',
            'passport_issue_date' => '2020-01-15',
            'passport_expiry_date' => '2030-01-14',
            
            // Employment & Financial Information
            'employer_name' => 'Tech Solutions Bangladesh Ltd',
            'employer_address' => 'Gulshan-2, Dhaka',
            'employment_start_date' => '2018-03-01',
            'monthly_income_bdt' => 85000,
            'annual_income_bdt' => 1020000,
            
            // Bank Details
            'bank_name' => 'Dutch-Bangla Bank',
            'bank_branch' => 'Dhanmondi Branch',
            'bank_account_number' => '1234567890123456',
            'bank_account_type' => 'savings',
            'bank_balance_bdt' => 450000,
            'bank_statement_path' => 'documents/bank-statements/demo-statement.pdf',
            
            // Property
            'owns_property' => true,
            'property_type' => 'Apartment',
            'property_address' => 'Dhanmondi, Dhaka',
            'property_value_bdt' => 8500000,
            'property_documents_path' => 'documents/property/deed.pdf',
            
            // Vehicle
            'owns_vehicle' => true,
            'vehicle_type' => 'Car',
            'vehicle_make_model' => 'Toyota Corolla',
            'vehicle_year' => 2019,
            'vehicle_value_bdt' => 1800000,
            
            // Investments
            'has_investments' => true,
            'investment_types' => 'Fixed Deposits, Mutual Funds, Stock Market',
            'investment_value_bdt' => 1200000,
            
            // Liabilities
            'has_liabilities' => true,
            'liability_types' => 'Home Loan',
            'liabilities_amount_bdt' => 2500000,
            
            // Net Worth
            'total_assets_bdt' => 11950000,
            'net_worth_bdt' => 9450000,
            
            // Documents
            'tax_return_path' => 'documents/tax/tax-return-2024.pdf',
            'salary_certificate_path' => 'documents/employment/salary-certificate.pdf',
            'financial_sponsor_info' => 'Self-sponsored with strong financial background',
        ]);

        // Create Family Members
        FamilyMember::create([
            'user_id' => $user->id,
            'relationship' => 'spouse',
            'full_name' => 'Fatima Rahman',
            'native_name' => 'ফাতিমা রহমান',
            'date_of_birth' => '1992-08-20',
            'place_of_birth' => 'Dhaka, Bangladesh',
            'gender' => 'female',
            'nationality' => 'BD',
            'country_of_residence' => 'BD',
            'city' => 'Dhaka',
            'address' => 'House 45, Road 12, Dhanmondi, Dhaka',
            'occupation' => 'Teacher',
            'employer_name' => 'Dhaka International School',
            'annual_income' => 480000,
            'income_currency' => 'BDT',
            'education_level' => 'masters',
            'marital_status' => 'married',
            'is_dependent' => true,
            'lives_with_user' => true,
            'will_accompany' => true,
            'will_accompany_travel' => true,
            'is_traveling_with' => true,
            'passport_number' => 'BG9876543',
            'passport_expiry' => '2029-05-10',
            'immigration_status' => 'not_applicable',
            'is_deceased' => false,
            'phone_number' => '+8801812345678',
            'email' => 'fatima.rahman@example.com',
            'emergency_contact' => true,
            'birth_certificate_path' => 'documents/family/spouse-birth-cert.pdf',
            'marriage_certificate_path' => 'documents/family/marriage-cert.pdf',
        ]);

        FamilyMember::create([
            'user_id' => $user->id,
            'relationship' => 'daughter',
            'full_name' => 'Aisha Hassan',
            'native_name' => 'আয়েশা হাসান',
            'date_of_birth' => '2015-03-10',
            'place_of_birth' => 'Dhaka, Bangladesh',
            'gender' => 'female',
            'nationality' => 'BD',
            'country_of_residence' => 'BD',
            'city' => 'Dhaka',
            'education_level' => 'primary',
            'is_dependent' => true,
            'lives_with_user' => true,
            'will_accompany' => true,
            'will_accompany_travel' => true,
            'is_traveling_with' => true,
            'is_deceased' => false,
            'birth_certificate_path' => 'documents/family/child-birth-cert.pdf',
        ]);

        FamilyMember::create([
            'user_id' => $user->id,
            'relationship' => 'father',
            'full_name' => 'Mohammad Hassan',
            'native_name' => 'মোহাম্মদ হাসান',
            'date_of_birth' => '1960-12-05',
            'place_of_birth' => 'Tangail, Bangladesh',
            'gender' => 'male',
            'nationality' => 'BD',
            'country_of_residence' => 'BD',
            'city' => 'Tangail',
            'occupation' => 'Retired Government Officer',
            'education_level' => 'bachelors',
            'marital_status' => 'married',
            'is_dependent' => false,
            'lives_with_user' => false,
            'will_accompany' => false,
            'is_deceased' => false,
            'phone_number' => '+8801912345678',
            'emergency_contact' => true,
        ]);

        // Create Education Records
        UserEducation::create([
            'user_id' => $user->id,
            'institution_name' => 'Bangladesh University of Engineering and Technology (BUET)',
            'degree' => 'Bachelor of Science',
            'field_of_study' => 'Computer Science and Engineering',
            'start_date' => '2008-01-01',
            'end_date' => '2012-12-31',
            'country' => 'BD',
            'city' => 'Dhaka',
            'is_completed' => true,
            'gpa_or_grade' => '3.85/4.00',
            'language_of_instruction' => 'English',
            'courses_completed' => 'Data Structures, Algorithms, Database Systems, Software Engineering, Web Development, Machine Learning',
            'honors_awards' => 'Dean\'s List (4 semesters), Best Final Year Project Award',
            'degree_certificate_path' => 'documents/education/bsc-certificate.pdf',
            'transcript_path' => 'documents/education/bsc-transcript.pdf',
        ]);

        UserEducation::create([
            'user_id' => $user->id,
            'institution_name' => 'National University of Singapore',
            'degree' => 'Master of Science',
            'field_of_study' => 'Computer Science',
            'start_date' => '2013-08-01',
            'end_date' => '2015-05-31',
            'country' => 'SG',
            'city' => 'Singapore',
            'is_completed' => true,
            'gpa_or_grade' => '4.5/5.0',
            'language_of_instruction' => 'English',
            'courses_completed' => 'Advanced Algorithms, Distributed Systems, Cloud Computing, Big Data Analytics',
            'honors_awards' => 'Graduate Research Scholarship',
            'degree_certificate_path' => 'documents/education/msc-certificate.pdf',
            'transcript_path' => 'documents/education/msc-transcript.pdf',
        ]);

        // Create Work Experience Records
        UserWorkExperience::create([
            'user_id' => $user->id,
            'company_name' => 'Tech Solutions Bangladesh Ltd',
            'position' => 'Senior Software Engineer',
            'employment_type' => 'full_time',
            'start_date' => '2018-03-01',
            'end_date' => null,
            'is_current_employment' => true,
            'city' => 'Dhaka',
            'country' => 'BD',
            'job_description' => 'Leading development of enterprise web applications using modern tech stack. Responsibilities include: Lead a team of 5 developers, Design and implement scalable microservices, Code review and mentoring junior developers, Collaborate with product managers on feature planning',
            'salary' => 85000,
            'currency' => 'BDT',
            'salary_period' => 'monthly',
            'supervisor_name' => 'Dr. Karim Ahmed',
            'supervisor_phone' => '+8801711111111',
            'supervisor_email' => 'karim.ahmed@techsolutions.com',
            'employment_letter_path' => 'documents/work/current-employment-letter.pdf',
        ]);

        UserWorkExperience::create([
            'user_id' => $user->id,
            'company_name' => 'Global IT Services Pte Ltd',
            'position' => 'Software Engineer',
            'employment_type' => 'full_time',
            'start_date' => '2015-07-01',
            'end_date' => '2018-02-28',
            'is_current_employment' => false,
            'city' => 'Singapore',
            'country' => 'SG',
            'job_description' => 'Developed and maintained web applications for international clients. Responsibilities: Full-stack web development, API design and implementation, Database optimization, Client communication and requirement gathering',
            'salary' => 5500,
            'currency' => 'SGD',
            'salary_period' => 'monthly',
            'reason_for_leaving' => 'Relocation to Bangladesh for family reasons',
            'supervisor_name' => 'James Tan',
            'supervisor_phone' => '+6591234567',
            'supervisor_email' => 'james.tan@globalit.sg',
            'employment_letter_path' => 'documents/work/singapore-experience-letter.pdf',
        ]);

        // Create Language Records
        $englishLang = Language::firstOrCreate(['name' => 'English', 'code' => 'en'], ['is_active' => true]);
        $ieltsTest = LanguageTest::firstOrCreate(
            ['name' => 'IELTS', 'language_id' => $englishLang->id],
            ['is_active' => true, 'description' => 'International English Language Testing System']
        );

        \App\Models\UserLanguage::create([
            'user_id' => $user->id,
            'language_id' => $englishLang->id,
            'language_test_id' => $ieltsTest->id,
            'proficiency_level' => 'advanced',
            'can_read' => true,
            'can_write' => true,
            'can_speak' => true,
            'can_understand' => true,
            'overall_score' => 7.5,
            'listening_score' => 8.0,
            'reading_score' => 8.5,
            'writing_score' => 7.0,
            'speaking_score' => 7.0,
            'test_date' => '2024-06-15',
            'expiry_date' => '2026-06-15',
            'test_reference_number' => 'IELTS2024BD123456',
            'is_native' => false,
            'file_path' => 'documents/languages/ielts-certificate.pdf',
        ]);

        $bengaliLang = Language::firstOrCreate(['name' => 'Bengali', 'code' => 'bn'], ['is_active' => true]);
        \App\Models\UserLanguage::create([
            'user_id' => $user->id,
            'language_id' => $bengaliLang->id,
            'proficiency_level' => 'native',
            'can_read' => true,
            'can_write' => true,
            'can_speak' => true,
            'can_understand' => true,
            'is_native' => true,
        ]);

        // Create Skills
        $skills = [
            ['name' => 'PHP', 'proficiency' => 'expert', 'years' => 8],
            ['name' => 'Laravel', 'proficiency' => 'expert', 'years' => 6],
            ['name' => 'JavaScript', 'proficiency' => 'expert', 'years' => 7],
            ['name' => 'Vue.js', 'proficiency' => 'advanced', 'years' => 4],
            ['name' => 'React', 'proficiency' => 'advanced', 'years' => 3],
            ['name' => 'MySQL', 'proficiency' => 'advanced', 'years' => 7],
            ['name' => 'Git', 'proficiency' => 'expert', 'years' => 8],
            ['name' => 'REST API', 'proficiency' => 'expert', 'years' => 6],
            ['name' => 'Docker', 'proficiency' => 'intermediate', 'years' => 3],
            ['name' => 'AWS', 'proficiency' => 'intermediate', 'years' => 2],
        ];

        foreach ($skills as $skillData) {
            $skill = \App\Models\Skill::where('name', $skillData['name'])->first();
            if ($skill) {
                $user->skills()->attach($skill->id, [
                    'proficiency_level' => $skillData['proficiency'],
                    'years_of_experience' => $skillData['years'],
                ]);
            }
        }

        // Create Phone Numbers
        UserPhoneNumber::create([
            'user_id' => $user->id,
            'phone_number' => '01712345678',
            'country_code' => '+880',
            'phone_type' => 'mobile',
            'is_primary' => true,
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        UserPhoneNumber::create([
            'user_id' => $user->id,
            'phone_number' => '01812345679',
            'country_code' => '+880',
            'phone_type' => 'whatsapp',
            'is_primary' => false,
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        // Create Travel History
        UserTravelHistory::create([
            'user_id' => $user->id,
            'country_visited' => 'SG',
            'city_visited' => 'Singapore',
            'entry_date' => '2015-07-01',
            'exit_date' => '2018-02-28',
            'duration_days' => 972,
            'purpose' => 'work',
            'purpose_details' => 'Work as Software Engineer at Global IT Services',
            'accommodation_type' => 'rental',
            'accommodation_address' => '123 Orchard Road, Singapore 238858',
            'entry_port' => 'Changi Airport',
            'exit_port' => 'Changi Airport',
            'compliance_issues' => false,
        ]);

        UserTravelHistory::create([
            'user_id' => $user->id,
            'country_visited' => 'MY',
            'city_visited' => 'Kuala Lumpur',
            'entry_date' => '2023-12-20',
            'exit_date' => '2023-12-27',
            'duration_days' => 7,
            'purpose' => 'tourism',
            'purpose_details' => 'Family vacation',
            'accommodation_type' => 'hotel',
            'accommodation_name' => 'Grand Hyatt Kuala Lumpur',
            'accommodation_address' => 'Jalan Pinang, Kuala Lumpur',
            'entry_port' => 'Kuala Lumpur International Airport',
            'exit_port' => 'Kuala Lumpur International Airport',
            'compliance_issues' => false,
        ]);

        // Create Security Information
        UserSecurityInformation::create([
            'user_id' => $user->id,
            
            // All clean records
            'has_criminal_record' => false,
            'has_been_deported' => false,
            'has_overstayed_visa' => false,
            'has_worked_illegally' => false,
            'has_violated_visa_conditions' => false,
            'has_immigration_ban' => false,
            'has_military_service' => false,
            
            // Police Clearance
            'has_police_clearance' => true,
            'police_clearance_country' => 'BD',
            'police_clearance_issue_date' => '2024-09-01',
            'police_clearance_expiry_date' => '2025-09-01',
            'police_clearance_reference' => 'BPC2024-BD-123456',
            
            // Background Check
            'background_check_completed' => true,
            'background_check_agency' => 'National Background Check Services',
            'background_check_date' => '2024-08-15',
            'background_check_result' => 'clear',
            
            // Security - Clean
            'has_terrorist_affiliation' => false,
            'member_of_banned_organization' => false,
        ]);

        $this->command->info('✅ Demo user created successfully!');
        $this->command->info('📧 Email: demo@bideshgomon.com');
        $this->command->info('🔑 Password: password123');
        $this->command->info('');
        $this->command->info('Profile completion: ' . $user->calculateProfileCompletion() . '%');
    }
}
