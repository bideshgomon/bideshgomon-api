<?php

namespace Database\Seeders;

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
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProfileManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates comprehensive test data for ALL profile management components:
     * - Passports (with expired, expiring soon, valid)
     * - Visa History (approved, rejected, with overstays)
     * - Travel History (recent and old travels)
     * - Family Members (spouse, children, parents)
     * - Education (multiple degrees)
     * - Work Experience (multiple jobs)
     * - Languages (with test scores)
     * - Financial Information
     * - Security Information
     */
    public function run(): void
    {
        // Create test user if doesn't exist
        $user = User::firstOrCreate(
            ['email' => 'john@test.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        echo "✅ Test User: {$user->email}\n";

        // Assign user role if not already assigned
        if (!$user->role_id) {
            $userRole = \App\Models\Role::where('name', 'user')->first();
            if ($userRole) {
                $user->update(['role_id' => $userRole->id]);
            }
        }

        // ========================================
        // 1. PASSPORTS (3 passports)
        // ========================================
        echo "\n📘 Creating Passports...\n";

        // Primary valid passport
        $passport1 = UserPassport::updateOrCreate(
            [
                'user_id' => $user->id,
                'passport_number' => 'BD1234567'
            ],
            [
                'passport_type' => 'regular',
                'issuing_country' => 'BD',
                'issuing_authority' => 'Department of Immigration and Passports, Bangladesh',
                'issue_date' => '2020-03-01',
                'expiry_date' => '2030-03-01',
                'place_of_issue' => 'Dhaka',
                'is_current_passport' => true,
                'is_lost_or_stolen' => false,
                'surname' => 'Doe',
                'given_names' => 'John',
                'nationality' => 'BD',
                'sex' => 'M',
                'date_of_birth' => '1990-01-15',
                'place_of_birth' => 'Dhaka, Bangladesh',
                'notes' => 'Primary passport - valid until 2030',
            ]
        );
        echo "  ✓ Primary Passport: {$passport1->passport_number} (Valid until 2030)\n";

        // Expiring soon passport
        $passport2 = UserPassport::updateOrCreate(
            [
                'user_id' => $user->id,
                'passport_number' => 'BD7654321'
            ],
            [
                'passport_type' => 'regular',
                'issuing_country' => 'BD',
                'issuing_authority' => 'Department of Immigration and Passports, Bangladesh',
                'issue_date' => '2015-06-15',
                'expiry_date' => now()->addMonths(4)->format('Y-m-d'),
                'place_of_issue' => 'Dhaka',
                'is_current_passport' => false,
                'is_lost_or_stolen' => false,
                'surname' => 'Doe',
                'given_names' => 'John',
                'nationality' => 'BD',
                'sex' => 'M',
                'date_of_birth' => '1990-01-15',
                'place_of_birth' => 'Dhaka, Bangladesh',
                'notes' => 'Old passport - expiring soon',
            ]
        );
        echo "  ⚠ Expiring Soon: {$passport2->passport_number} (Expires in 4 months)\n";

        // Expired passport
        $passport3 = UserPassport::updateOrCreate(
            [
                'user_id' => $user->id,
                'passport_number' => 'BD9876543'
            ],
            [
                'passport_type' => 'regular',
                'issuing_country' => 'BD',
                'issuing_authority' => 'Department of Immigration and Passports, Bangladesh',
                'issue_date' => '2010-01-10',
                'expiry_date' => '2020-01-10',
                'place_of_issue' => 'Dhaka',
                'is_current_passport' => false,
                'is_lost_or_stolen' => false,
                'surname' => 'Doe',
                'given_names' => 'John',
                'nationality' => 'BD',
                'sex' => 'M',
                'date_of_birth' => '1990-01-15',
                'place_of_birth' => 'Dhaka, Bangladesh',
                'notes' => 'First passport - expired in 2020',
            ]
        );
        echo "  ❌ Expired: {$passport3->passport_number} (Expired in 2020)\n";

        // ========================================
        // 2. VISA HISTORY (5 records - approved, rejected, with overstay)
        // ========================================
        echo "\n🌍 Creating Visa History...\n";

        // USA Tourist Visa - REJECTED (CRITICAL DATA)
        $visa1 = UserVisaHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country' => 'US',
                'application_date' => '2022-05-10',
            ],
            [
                'user_passport_id' => $passport2->id,
                'visa_type' => 'tourist',
                'visa_category' => 'B-2',
                'visa_number' => null,
                'application_date' => '2022-05-10',
                'status' => 'rejected',
                'was_visa_rejected' => true,
                'rejection_reason' => 'Insufficient proof of ties to home country. Could not demonstrate strong economic and social ties to Bangladesh. Advised to reapply with additional documentation including property ownership, employment letter, and family ties evidence.',
                'purpose_of_visit' => 'Tourism and sightseeing in New York and Washington DC',
                'overstay_occurred' => false,
                'overstay_days' => 0,
                'embassy_location' => 'US Embassy Dhaka',
                'visa_fee' => 16000.00,
                'currency' => 'BDT',
                'notes' => 'First USA visa attempt - rejected. Need to strengthen application with property docs and bank statements.',
            ]
        );
        echo "  ❌ USA Tourist Visa: REJECTED (Critical for future applications)\n";

        // Thailand Tourist Visa - APPROVED with OVERSTAY (RED FLAG)
        $visa2 = UserVisaHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country' => 'TH',
                'visa_number' => 'TH2023001234',
            ],
            [
                'user_passport_id' => $passport2->id,
                'visa_type' => 'tourist',
                'visa_category' => 'Tourist Visa (60 days)',
                'visa_number' => 'TH2023001234',
                'application_date' => '2023-01-05',
                'issue_date' => '2023-01-20',
                'expiry_date' => '2023-07-20',
                'entry_date' => '2023-02-15',
                'exit_date' => '2023-04-16',
                'status' => 'approved',
                'was_visa_rejected' => false,
                'duration_of_stay' => 75,
                'purpose_of_visit' => 'Tourism and leisure travel',
                'overstay_occurred' => true,
                'overstay_days' => 15,
                'embassy_location' => 'Royal Thai Embassy Dhaka',
                'visa_fee' => 3500.00,
                'currency' => 'BDT',
                'notes' => 'WARNING: Overstayed by 15 days due to medical emergency (Bumrungrad Hospital). Paid fine 7,500 THB. May impact future visa applications.',
            ]
        );
        echo "  ⚠ Thailand Tourist Visa: APPROVED but OVERSTAYED 15 days (RED FLAG)\n";

        // UK Student Visa - APPROVED (currently valid)
        $visa3 = UserVisaHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country' => 'GB',
                'visa_number' => 'UK202400987',
            ],
            [
                'user_passport_id' => $passport1->id,
                'visa_type' => 'student',
                'visa_category' => 'Tier 4 (General) Student Visa',
                'visa_number' => 'UK202400987',
                'application_date' => '2024-06-01',
                'issue_date' => '2024-07-15',
                'expiry_date' => '2026-09-30',
                'entry_date' => '2024-09-01',
                'status' => 'approved',
                'was_visa_rejected' => false,
                'duration_of_stay' => null,
                'purpose_of_visit' => 'Masters degree in Computer Science at University of London',
                'overstay_occurred' => false,
                'overstay_days' => 0,
                'embassy_location' => 'UK Visa Application Centre Dhaka',
                'visa_fee' => 40000.00,
                'currency' => 'BDT',
                'notes' => 'Student visa for Masters program. Multiple entry allowed. Currently studying.',
            ]
        );
        echo "  ✅ UK Student Visa: APPROVED (Valid until 2026)\n";

        // Dubai Tourist Visa - APPROVED (expired)
        $visa4 = UserVisaHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country' => 'AE',
                'visa_number' => 'UAE2021005678',
            ],
            [
                'user_passport_id' => $passport2->id,
                'visa_type' => 'tourist',
                'visa_category' => '30-Day Tourist Visa',
                'visa_number' => 'UAE2021005678',
                'application_date' => '2021-11-01',
                'issue_date' => '2021-11-10',
                'expiry_date' => '2021-12-10',
                'entry_date' => '2021-11-20',
                'exit_date' => '2021-11-28',
                'status' => 'expired',
                'was_visa_rejected' => false,
                'duration_of_stay' => 8,
                'purpose_of_visit' => 'Family vacation and tourism',
                'overstay_occurred' => false,
                'overstay_days' => 0,
                'embassy_location' => 'Dubai Travel Agency',
                'visa_fee' => 8000.00,
                'currency' => 'BDT',
                'notes' => 'Tourist visa for Dubai vacation. Completed without issues.',
            ]
        );
        echo "  ✓ UAE Tourist Visa: APPROVED (Expired)\n";

        // India Business Visa - APPROVED
        $visa5 = UserVisaHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country' => 'IN',
                'visa_number' => 'IND2023B0012345',
            ],
            [
                'user_passport_id' => $passport1->id,
                'visa_type' => 'business',
                'visa_category' => 'Business Visa (Multiple Entry)',
                'visa_number' => 'IND2023B0012345',
                'application_date' => '2023-03-15',
                'issue_date' => '2023-03-30',
                'expiry_date' => '2024-03-30',
                'entry_date' => '2023-06-10',
                'exit_date' => '2023-06-17',
                'status' => 'expired',
                'was_visa_rejected' => false,
                'duration_of_stay' => 7,
                'purpose_of_visit' => 'Business meetings, conferences, and client consultations',
                'overstay_occurred' => false,
                'overstay_days' => 0,
                'embassy_location' => 'Indian High Commission Dhaka',
                'visa_fee' => 5000.00,
                'currency' => 'BDT',
                'notes' => 'Multiple entry business visa. Used for 2 trips.',
            ]
        );
        echo "  ✓ India Business Visa: APPROVED (Expired)\n";

        // ========================================
        // 3. TRAVEL HISTORY (3 records)
        // ========================================
        echo "\n✈️  Creating Travel History...\n";

        // Thailand trip (with overstay)
        $travel1 = UserTravelHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country_visited' => 'TH',
                'entry_date' => '2023-02-15',
            ],
            [
                'user_passport_id' => $passport2->id,
                'user_visa_history_id' => $visa2->id,
                'country_visited' => 'TH',
                'city_visited' => 'Bangkok',
                'region_visited' => null,
                'purpose' => 'tourism',
                'purpose_details' => 'Vacation and sightseeing',
                'entry_date' => '2023-02-15',
                'exit_date' => '2023-04-16',
                'duration_days' => 60,
                'accommodation_type' => 'hotel',
                'accommodation_address' => '123 Sukhumvit Road, Bangkok',
                'accommodation_name' => 'Sukhumvit Hotel',
                'travel_companions' => json_encode(['spouse']),
                'entry_port' => 'Suvarnabhumi Airport',
                'exit_port' => 'Suvarnabhumi Airport',
                'compliance_issues' => true,
                'compliance_notes' => 'Overstayed by 15 days due to medical emergency. Hospitalized at Bumrungrad International Hospital. Paid overstay fine of 7,500 THB.',
                'notes' => 'Medical emergency caused overstay. Have hospital records and payment receipts.',
            ]
        );
        echo "  ⚠ Thailand (Bangkok): Feb-Apr 2023 - Overstayed 15 days\n";

        // Dubai trip (clean)
        $travel2 = UserTravelHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country_visited' => 'AE',
                'entry_date' => '2021-11-20',
            ],
            [
                'user_passport_id' => $passport2->id,
                'user_visa_history_id' => $visa4->id,
                'country_visited' => 'AE',
                'city_visited' => 'Dubai',
                'region_visited' => null,
                'purpose' => 'tourism',
                'purpose_details' => 'Family vacation and sightseeing',
                'entry_date' => '2021-11-20',
                'exit_date' => '2021-11-28',
                'duration_days' => 8,
                'accommodation_type' => 'hotel',
                'accommodation_address' => 'Jumeirah Beach Road, Dubai',
                'accommodation_name' => 'Burj Al Arab Hotel',
                'travel_companions' => json_encode(['spouse', 'children']),
                'entry_port' => 'Dubai International Airport',
                'exit_port' => 'Dubai International Airport',
                'compliance_issues' => false,
                'compliance_notes' => null,
                'notes' => 'Family vacation. No issues.',
            ]
        );
        echo "  ✓ UAE (Dubai): Nov 2021 - Clean travel record\n";

        // India business trip (clean)
        $travel3 = UserTravelHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'country_visited' => 'IN',
                'entry_date' => '2023-06-10',
            ],
            [
                'user_passport_id' => $passport1->id,
                'user_visa_history_id' => $visa5->id,
                'country_visited' => 'IN',
                'city_visited' => 'Mumbai',
                'region_visited' => 'Maharashtra',
                'purpose' => 'business',
                'purpose_details' => 'Business conference and client meetings with TCS India',
                'entry_date' => '2023-06-10',
                'exit_date' => '2023-06-17',
                'duration_days' => 7,
                'accommodation_type' => 'other',
                'accommodation_address' => 'Andheri East, Mumbai',
                'accommodation_name' => 'TCS Guest House',
                'travel_companions' => json_encode([]),
                'entry_port' => 'Chhatrapati Shivaji International Airport',
                'exit_port' => 'Chhatrapati Shivaji International Airport',
                'compliance_issues' => false,
                'compliance_notes' => null,
                'notes' => 'Business conference and client meetings. Hosted by TCS India Ltd.',
            ]
        );
        echo "  ✓ India (Mumbai): Jun 2023 - Business trip\n";

        // ========================================
        // 4. FAMILY MEMBERS (4 members)
        // ========================================
        echo "\n👨‍👩‍👧‍👦 Creating Family Members...\n";

        // Spouse
        $family1 = UserFamilyMember::updateOrCreate(
            [
                'user_id' => $user->id,
                'full_name' => 'Jane Doe',
            ],
            [
                'relationship' => 'spouse',
                'full_name' => 'Jane Doe',
                'date_of_birth' => '1992-05-20',
                'gender' => 'female',
                'nationality' => 'BD',
                'passport_number' => 'BD2345678',
                'occupation' => 'Teacher',
                'employer_name' => 'Dhaka International School',
                'phone_number' => '01712345679',
                'email' => 'jane.doe@example.com',
                'country_of_residence' => 'BD',
                'city' => 'Dhaka',
                'address' => 'House 15, Road 7, Dhanmondi, Dhaka',
                'is_traveling_with' => false,
            ]
        );
        echo "  ✓ Spouse: {$family1->full_name}\n";

        // Child 1
        $family2 = UserFamilyMember::updateOrCreate(
            [
                'user_id' => $user->id,
                'full_name' => 'Sarah Doe',
            ],
            [
                'relationship' => 'daughter',
                'full_name' => 'Sarah Doe',
                'date_of_birth' => '2015-08-12',
                'gender' => 'female',
                'nationality' => 'BD',
                'passport_number' => 'BD3456789',
                'occupation' => 'Student',
                'country_of_residence' => 'BD',
                'city' => 'Dhaka',
                'address' => 'House 15, Road 7, Dhanmondi, Dhaka',
                'is_traveling_with' => false,
            ]
        );
        echo "  ✓ Child: {$family2->full_name} (Age 9)\n";

        // Child 2
        $family3 = UserFamilyMember::updateOrCreate(
            [
                'user_id' => $user->id,
                'full_name' => 'Michael Doe',
            ],
            [
                'relationship' => 'son',
                'full_name' => 'Michael Doe',
                'date_of_birth' => '2018-03-25',
                'gender' => 'male',
                'nationality' => 'BD',
                'passport_number' => 'BD4567890',
                'occupation' => 'Student',
                'country_of_residence' => 'BD',
                'city' => 'Dhaka',
                'address' => 'House 15, Road 7, Dhanmondi, Dhaka',
                'is_traveling_with' => false,
            ]
        );
        echo "  ✓ Child: {$family3->full_name} (Age 6)\n";

        // Father
        $family4 = UserFamilyMember::updateOrCreate(
            [
                'user_id' => $user->id,
                'full_name' => 'Robert Doe',
            ],
            [
                'relationship' => 'father',
                'full_name' => 'Robert Doe',
                'date_of_birth' => '1960-12-10',
                'gender' => 'male',
                'nationality' => 'BD',
                'occupation' => 'Retired Government Officer',
                'phone_number' => '01712345680',
                'country_of_residence' => 'BD',
                'city' => 'Dhaka',
                'address' => 'House 8, Road 12, Gulshan, Dhaka',
            ]
        );
        echo "  ✓ Father: {$family4->full_name}\n";

        // ========================================
        // 5. EDUCATION (3 degrees)
        // ========================================
        echo "\n🎓 Creating Education Records...\n";

        // Masters (Current)
        $edu1 = UserEducation::updateOrCreate(
            [
                'user_id' => $user->id,
                'degree' => 'Master of Science (M.Sc.)',
                'institution_name' => 'University of London',
            ],
            [
                'degree' => 'Master of Science (M.Sc.)',
                'field_of_study' => 'Computer Science',
                'institution_name' => 'University of London',
                'country' => 'GB',
                'city' => 'London',
                'start_date' => '2024-09-01',
                'end_date' => null,
                'is_completed' => false,
                'gpa_or_grade' => null,
                'language_of_instruction' => 'English',
            ]
        );
        echo "  ✓ Masters: {$edu1->field_of_study} at {$edu1->institution_name} (Current)\n";

        // Bachelors
        $edu2 = UserEducation::updateOrCreate(
            [
                'user_id' => $user->id,
                'degree' => 'Bachelor of Science (B.Sc.)',
                'institution_name' => 'Dhaka University',
            ],
            [
                'degree' => 'Bachelor of Science (B.Sc.)',
                'field_of_study' => 'Software Engineering',
                'institution_name' => 'Dhaka University',
                'country' => 'BD',
                'city' => 'Dhaka',
                'start_date' => '2008-01-15',
                'end_date' => '2012-06-30',
                'is_completed' => true,
                'gpa_or_grade' => '3.75/4.00',
                'language_of_instruction' => 'English',
            ]
        );
        echo "  ✓ Bachelors: {$edu2->field_of_study} at {$edu2->institution_name} (GPA 3.75)\n";

        // High School
        $edu3 = UserEducation::updateOrCreate(
            [
                'user_id' => $user->id,
                'degree' => 'Higher Secondary (HSC)',
                'institution_name' => 'Notre Dame College',
            ],
            [
                'degree' => 'Higher Secondary (HSC)',
                'field_of_study' => 'Science',
                'institution_name' => 'Notre Dame College',
                'country' => 'BD',
                'city' => 'Dhaka',
                'start_date' => '2006-01-01',
                'end_date' => '2008-05-31',
                'is_completed' => true,
                'gpa_or_grade' => '5.00/5.00',
                'language_of_instruction' => 'Bengali & English',
            ]
        );
        echo "  ✓ High School: {$edu3->institution_name} (GPA 5.00)\n";

        // ========================================
        // 6. WORK EXPERIENCE (3 jobs)
        // ========================================
        echo "\n💼 Creating Work Experience...\n";

        // Current job
        $work1 = UserWorkExperience::updateOrCreate(
            [
                'user_id' => $user->id,
                'company_name' => 'Tech Solutions Ltd',
            ],
            [
                'company_name' => 'Tech Solutions Ltd',
                'position' => 'Senior Software Engineer',
                'employment_type' => 'full_time',
                'country' => 'BD',
                'city' => 'Dhaka',
                'start_date' => '2020-07-01',
                'end_date' => null,
                'is_current_employment' => true,
                'job_description' => 'Lead development team, architect solutions, code review, mentor junior developers',
                'salary' => 100000.00,
                'currency' => 'BDT',
                'salary_period' => 'monthly',
            ]
        );
        echo "  ✓ Current: {$work1->position} at {$work1->company_name}\n";

        // Previous job 1
        $work2 = UserWorkExperience::updateOrCreate(
            [
                'user_id' => $user->id,
                'company_name' => 'Digital Innovations',
            ],
            [
                'company_name' => 'Digital Innovations',
                'position' => 'Software Engineer',
                'employment_type' => 'full_time',
                'country' => 'BD',
                'city' => 'Dhaka',
                'start_date' => '2015-03-01',
                'end_date' => '2020-06-30',
                'is_current_employment' => false,
                'job_description' => 'Full-stack development, API design, database optimization',
                'salary' => 50000.00,
                'currency' => 'BDT',
                'salary_period' => 'monthly',
                'reason_for_leaving' => 'Career growth opportunity',
            ]
        );
        echo "  ✓ Previous: {$work2->position} at {$work2->company_name} (2015-2020)\n";

        // Previous job 2
        $work3 = UserWorkExperience::updateOrCreate(
            [
                'user_id' => $user->id,
                'company_name' => 'StartupHub Bangladesh',
            ],
            [
                'company_name' => 'StartupHub Bangladesh',
                'position' => 'Junior Developer',
                'employment_type' => 'full_time',
                'country' => 'BD',
                'city' => 'Dhaka',
                'start_date' => '2012-08-01',
                'end_date' => '2015-02-28',
                'is_current_employment' => false,
                'job_description' => 'Web development, bug fixes, feature implementation',
                'salary' => 25000.00,
                'currency' => 'BDT',
                'salary_period' => 'monthly',
                'reason_for_leaving' => 'Better opportunity',
            ]
        );
        echo "  ✓ Previous: {$work3->position} at {$work3->company_name} (2012-2015)\n";

        // ========================================
        // 7. LANGUAGES (3 languages with test scores)
        // ========================================
        echo "\n🗣️  Creating Language Proficiency...\n";

        // English (IELTS)
        $lang1 = UserLanguage::updateOrCreate(
            [
                'user_id' => $user->id,
                'language' => 'English',
            ],
            [
                'language' => 'English',
                'proficiency' => 'advanced',
                'can_read' => true,
                'can_write' => true,
                'can_speak' => true,
                'can_understand' => true,
                'test_taken' => 'ielts',
                'test_score' => '7.5',
                'test_date' => '2024-03-15',
                'test_reference_number' => 'IELTS2024001234',
                'listening_score' => '8.0',
                'reading_score' => '7.5',
                'writing_score' => '7.0',
                'speaking_score' => '7.5',
                'is_native' => false,
            ]
        );
        echo "  ✓ English: Advanced (IELTS 7.5)\n";

        // Bengali (Native)
        $lang2 = UserLanguage::updateOrCreate(
            [
                'user_id' => $user->id,
                'language' => 'Bengali',
            ],
            [
                'language' => 'Bengali',
                'proficiency' => 'native',
                'can_read' => true,
                'can_write' => true,
                'can_speak' => true,
                'can_understand' => true,
                'test_taken' => 'none',
                'is_native' => true,
            ]
        );
        echo "  ✓ Bengali: Native\n";

        // Hindi (Intermediate)
        $lang3 = UserLanguage::updateOrCreate(
            [
                'user_id' => $user->id,
                'language' => 'Hindi',
            ],
            [
                'language' => 'Hindi',
                'proficiency' => 'intermediate',
                'can_read' => true,
                'can_write' => false,
                'can_speak' => true,
                'can_understand' => true,
                'test_taken' => 'none',
                'is_native' => false,
            ]
        );
        echo "  ✓ Hindi: Intermediate\n";

        // ========================================
        // 8. FINANCIAL INFORMATION
        // ========================================
        echo "\n💰 Creating Financial Information...\n";

        $financial = UserFinancialInformation::updateOrCreate(
            ['user_id' => $user->id],
            [
                'annual_income' => 1200000.00,
                'monthly_income' => 100000.00,
                'currency' => 'BDT',
                'source_of_income' => 'salary',
                'income_details' => 'Software Engineer salary at Tech Solutions Ltd',
                'employer_name' => 'Tech Solutions Ltd',
                'employer_address' => 'Banani, Dhaka',
                'job_title' => 'Senior Software Engineer',
                'employment_start_date' => '2020-07-01',
                'primary_bank_name' => 'Standard Chartered Bank',
                'bank_account_number' => '1234567890',
                'bank_branch' => 'Gulshan Branch',
                'current_balance' => 500000.00,
                'balance_currency' => 'BDT',
                'tax_id_number' => '123456789012',
                'tax_id_country' => 'BD',
                'files_tax_returns' => true,
                'property_ownership' => 'own_home',
                'property_value' => 8000000.00,
                'property_value_currency' => 'BDT',
                'property_address' => 'House 15, Road 7, Dhanmondi, Dhaka',
                'owns_vehicle' => true,
                'vehicle_details' => 'Toyota Premio, 2019',
                'vehicle_value' => 1500000.00,
                'investment_value' => 500000.00,
                'investment_details' => 'Mutual funds and fixed deposits',
                'total_assets' => 10000000.00,
                'total_liabilities' => 3000000.00,
                'has_sponsor' => false,
                'available_funds' => 500000.00,
                'funds_currency' => 'BDT',
            ]
        );
        echo "  ✓ Annual Income: ৳12,00,000 (Employment)\n";
        echo "  ✓ Assets: Property (৳80L), Car, Investments\n";
        echo "  ✓ Bank Balance: ৳5,00,000\n";

        // ========================================
        // 9. SECURITY INFORMATION
        // ========================================
        echo "\n🔒 Creating Security Information...\n";

        $security = UserSecurityInformation::updateOrCreate(
            ['user_id' => $user->id],
            [
                // Criminal History
                'has_criminal_record' => false,
                // Deportation
                'has_been_deported' => false,
                // Visa Violations
                'has_overstayed_visa' => true,
                'overstay_country' => 'TH',
                'overstay_duration_days' => 15,
                'overstay_date' => '2023-04-16',
                'overstay_explanation' => 'Medical emergency - hospitalization at Bumrungrad Hospital for appendicitis. Paid all fines.',
                'has_worked_illegally' => false,
                'has_violated_visa_conditions' => false,
                // Immigration Bans
                'has_immigration_ban' => false,
                // Military Service
                'has_military_service' => false,
                'military_discharge_type' => 'not_applicable',
                // Security Concerns
                'has_terrorist_affiliation' => false,
                'member_of_banned_organization' => false,
                // Background Check
                'has_police_clearance' => false,
                'background_check_completed' => false,
                'background_check_result' => 'not_applicable',
                'security_questions_answered' => true,
                'security_declaration_date' => now(),
            ]
        );
        echo "  ⚠ Security Issues:\n";
        echo "    - USA Visa Refusal (2022)\n";
        echo "    - Thailand Overstay (2023)\n";
        echo "    - Medical History: Appendicitis\n";

        echo "\n" . str_repeat('=', 60) . "\n";
        echo "✅ PROFILE MANAGEMENT SEEDING COMPLETE!\n";
        echo str_repeat('=', 60) . "\n\n";

        echo "📊 Summary:\n";
        echo "  - User: {$user->email} (password: password)\n";
        echo "  - Passports: 3 (1 valid, 1 expiring, 1 expired)\n";
        echo "  - Visa History: 5 (1 rejected, 1 with overstay, 3 approved)\n";
        echo "  - Travel History: 3 trips\n";
        echo "  - Family Members: 4 (spouse, 2 children, father)\n";
        echo "  - Education: 3 degrees\n";
        echo "  - Work Experience: 3 jobs\n";
        echo "  - Languages: 3 (English IELTS 7.5, Bengali, Hindi)\n";
        echo "  - Financial Info: ✓\n";
        echo "  - Security Info: ✓ (with red flags)\n\n";

        echo "🔍 Test Scenarios Covered:\n";
        echo "  ✓ Expired passport detection\n";
        echo "  ✓ Expiring soon warning\n";
        echo "  ✓ USA visa rejection (critical data)\n";
        echo "  ✓ Thailand overstay (red flag)\n";
        echo "  ✓ Multiple visa statuses\n";
        echo "  ✓ Complete family structure\n";
        echo "  ✓ Education history with current studies\n";
        echo "  ✓ Work history with current employment\n";
        echo "  ✓ Language tests (IELTS)\n";
        echo "  ✓ Financial information\n";
        echo "  ✓ Security issues/red flags\n\n";

        echo "🚀 Ready to test all dropdowns and components!\n\n";
    }
}
