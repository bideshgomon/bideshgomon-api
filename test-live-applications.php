#!/usr/bin/env php
<?php

/**
 * Live Application Testing Script
 * Tests creating actual applications in the database to verify full workflow
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Country;
use App\Models\StudentVisa;
use App\Models\WorkVisa;
use App\Models\Translation;
use App\Models\Attestation;
use App\Models\HajjUmrah;
use App\Models\ServiceApplication;
use App\Models\ServiceModule;
use Illuminate\Support\Facades\DB;

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║          LIVE APPLICATION TESTING - DATABASE EDITION            ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Get or create test user
$user = User::firstOrCreate(
    ['email' => 'testuser@bideshgomon.com'],
    [
        'name' => 'Test User',
        'password' => bcrypt('password123'),
        'phone' => '01712345678',
    ]
);

echo "✅ Test User: {$user->name} (ID: {$user->id})\n\n";

// Get destination countries
$canada = Country::where('name', 'Canada')->first();
$usa = Country::where('name', 'United States')->first();
$uk = Country::where('name', 'United Kingdom')->first();
$saudiArabia = Country::where('name', 'Saudi Arabia')->first();

if (!$canada || !$usa || !$uk || !$saudiArabia) {
    echo "❌ ERROR: Required countries not found in database\n";
    echo "   Make sure countries are seeded!\n";
    exit(1);
}

// Get service modules
$studentVisaService = ServiceModule::where('slug', 'student-visa')->first();
$workVisaService = ServiceModule::where('slug', 'work-visa')->first();
$translationService = ServiceModule::where('slug', 'translation')->first();
$attestationService = ServiceModule::where('slug', 'attestation')->first();
$hajjUmrahService = ServiceModule::where('slug', 'hajj-umrah')->first();

if (!$studentVisaService || !$workVisaService || !$translationService || !$attestationService || !$hajjUmrahService) {
    echo "❌ ERROR: Required service modules not found\n";
    echo "   Student Visa: " . ($studentVisaService ? '✅' : '❌') . "\n";
    echo "   Work Visa: " . ($workVisaService ? '✅' : '❌') . "\n";
    echo "   Translation: " . ($translationService ? '✅' : '❌') . "\n";
    echo "   Attestation: " . ($attestationService ? '✅' : '❌') . "\n";
    echo "   Hajj & Umrah: " . ($hajjUmrahService ? '✅' : '❌') . "\n";
    exit(1);
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "📝 CREATING TEST APPLICATIONS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$createdApplications = [];

try {
    // 1. Student Visa Application
    echo "1️⃣  Creating Student Visa Application...\n";
    DB::beginTransaction();
    
    $studentVisa = StudentVisa::create([
        'user_id' => $user->id,
        'destination_country_id' => $canada->id,
        'education_level' => 'masters',
        'study_field' => 'Computer Science',
        'institution_name' => 'University of Toronto',
        'course_name' => 'Master of Science in Computer Science',
        'intended_start_date' => now()->addMonths(6),
        'course_duration_months' => 24,
        'tuition_fee' => 35000,
        'currency' => 'CAD',
        'has_admission_letter' => true,
        'english_test_type' => 'IELTS',
        'english_test_score' => '7.5',
        'previous_education_gpa' => '3.8',
        'funding_source' => 'Self-funded',
        'annual_income' => 50000,
        'intended_program_type' => 'Full-time',
        'accommodation_plan' => 'University Housing',
        'has_study_gap' => false,
        'has_previous_visa_rejection' => false,
        'special_requirements' => 'Need assistance with SOP writing',
        'status' => 'pending',
    ]);

    $serviceApp1 = ServiceApplication::create([
        'user_id' => $user->id,
        'service_module_id' => $studentVisaService->id,
        'application_data' => [
            'destination_country' => 'Canada',
            'education_level' => 'masters',
            'institution' => 'University of Toronto',
            'course' => 'Master of Science in Computer Science',
        ],
        'status' => 'pending',
    ]);

    DB::commit();
    $createdApplications[] = [
        'type' => 'Student Visa',
        'id' => $studentVisa->id,
        'reference' => $serviceApp1->application_number,
        'service_app_id' => $serviceApp1->id,
    ];
    echo "   ✅ Student Visa created (ID: {$studentVisa->id})\n";
    echo "   ✅ ServiceApplication created (Ref: {$serviceApp1->application_number})\n\n";

    // 2. Work Visa Application
    echo "2️⃣  Creating Work Visa Application...\n";
    DB::beginTransaction();
    
    $workVisa = WorkVisa::create([
        'user_id' => $user->id,
        'destination_country_id' => $canada->id,
        'job_title' => 'Software Engineer',
        'job_category' => 'Information Technology',
        'employer_name' => 'Tech Innovations Inc.',
        'employment_type' => 'full-time',
        'offered_salary' => 85000,
        'salary_currency' => 'CAD',
        'years_of_experience' => 5,
        'work_permit_type' => 'Temporary Foreign Worker',
        'requires_sponsorship' => true,
        'has_lmia_approval' => true,
        'job_offer_letter_upload' => null,
        'has_relevant_degree' => true,
        'degree_field' => 'Computer Science',
        'language_proficiency' => 'English - Fluent',
        'previous_work_countries' => json_encode(['Bangladesh', 'Singapore']),
        'contract_duration_months' => 24,
        'job_start_date' => now()->addMonths(3),
        'accommodation_provided' => false,
        'relocation_assistance' => true,
        'family_accompanying' => false,
        'special_requirements' => 'Need assistance with credential evaluation',
        'status' => 'pending',
    ]);

    $serviceApp2 = ServiceApplication::create([
        'user_id' => $user->id,
        'service_module_id' => $workVisaService->id,
        'application_data' => [
            'destination_country' => 'Canada',
            'job_title' => 'Software Engineer',
            'employer' => 'Tech Innovations Inc.',
            'salary' => '85000 CAD',
        ],
        'status' => 'pending',
    ]);

    DB::commit();
    $createdApplications[] = [
        'type' => 'Work Visa',
        'id' => $workVisa->id,
        'reference' => $serviceApp2->application_number,
        'service_app_id' => $serviceApp2->id,
    ];
    echo "   ✅ Work Visa created (ID: {$workVisa->id})\n";
    echo "   ✅ ServiceApplication created (Ref: {$serviceApp2->application_number})\n\n";

    // 3. Translation Application
    echo "3️⃣  Creating Translation Application...\n";
    DB::beginTransaction();
    
    $translation = Translation::create([
        'user_id' => $user->id,
        'document_type' => 'Educational Certificate',
        'source_language' => 'Bengali',
        'target_language' => 'English',
        'page_count' => 3,
        'certification_type' => 'Notarized',
        'is_urgent' => false,
        'required_by_date' => now()->addDays(7),
        'document_upload' => null,
        'additional_notes' => 'Need certified translation for university application',
        'status' => 'pending',
    ]);

    $serviceApp3 = ServiceApplication::create([
        'user_id' => $user->id,
        'service_module_id' => $translationService->id,
        'application_data' => [
            'document_type' => 'Educational Certificate',
            'source_language' => 'Bengali',
            'target_language' => 'English',
            'pages' => 3,
        ],
        'status' => 'pending',
    ]);

    DB::commit();
    $createdApplications[] = [
        'type' => 'Translation',
        'id' => $translation->id,
        'reference' => $serviceApp3->application_number,
        'service_app_id' => $serviceApp3->id,
    ];
    echo "   ✅ Translation created (ID: {$translation->id})\n";
    echo "   ✅ ServiceApplication created (Ref: {$serviceApp3->application_number})\n\n";

    // 4. Attestation Application
    echo "4️⃣  Creating Attestation Application...\n";
    DB::beginTransaction();
    
    $attestation = Attestation::create([
        'user_id' => $user->id,
        'target_country_id' => $uk->id,
        'document_type' => 'Degree Certificate',
        'attestation_type' => 'MOFA',
        'purpose' => 'Employment',
        'document_count' => 2,
        'is_urgent' => false,
        'required_by_date' => now()->addDays(14),
        'document_upload' => null,
        'additional_requirements' => 'Need embassy attestation after MOFA',
        'status' => 'pending',
    ]);

    $serviceApp4 = ServiceApplication::create([
        'user_id' => $user->id,
        'service_module_id' => $attestationService->id,
        'application_data' => [
            'target_country' => 'United Kingdom',
            'document_type' => 'Degree Certificate',
            'attestation_type' => 'MOFA',
            'documents' => 2,
        ],
        'status' => 'pending',
    ]);

    DB::commit();
    $createdApplications[] = [
        'type' => 'Attestation',
        'id' => $attestation->id,
        'reference' => $serviceApp4->application_number,
        'service_app_id' => $serviceApp4->id,
    ];
    echo "   ✅ Attestation created (ID: {$attestation->id})\n";
    echo "   ✅ ServiceApplication created (Ref: {$serviceApp4->application_number})\n\n";

    // 5. Hajj & Umrah Application
    echo "5️⃣  Creating Hajj & Umrah Application...\n";
    DB::beginTransaction();
    
    $hajjUmrah = HajjUmrah::create([
        'user_id' => $user->id,
        'package_type' => 'Umrah',
        'number_of_travelers' => 2,
        'preferred_travel_date' => now()->addMonths(4),
        'duration' => 14,
        'accommodation_type' => '4-star hotel',
        'meal_plan' => 'Full Board',
        'transport_type' => 'Private',
        'requires_visa_assistance' => true,
        'requires_training' => true,
        'special_requirements' => 'Need wheelchair accessibility',
        'status' => 'pending',
    ]);

    $serviceApp5 = ServiceApplication::create([
        'user_id' => $user->id,
        'service_module_id' => $hajjUmrahService->id,
        'application_data' => [
            'package_type' => 'Umrah',
            'travelers' => 2,
            'duration' => '14 days',
            'accommodation' => '4-star hotel',
        ],
        'status' => 'pending',
    ]);

    DB::commit();
    $createdApplications[] = [
        'type' => 'Hajj & Umrah',
        'id' => $hajjUmrah->id,
        'reference' => $serviceApp5->application_number,
        'service_app_id' => $serviceApp5->id,
    ];
    echo "   ✅ Hajj & Umrah created (ID: {$hajjUmrah->id})\n";
    echo "   ✅ ServiceApplication created (Ref: {$serviceApp5->application_number})\n\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
    exit(1);
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "✅ VERIFICATION\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Verify all records exist
foreach ($createdApplications as $app) {
    echo "📋 {$app['type']}\n";
    echo "   Application ID: {$app['id']}\n";
    echo "   Reference Code: {$app['reference']}\n";
    echo "   ServiceApp ID: {$app['service_app_id']}\n\n";
}

// Check database counts
$studentCount = StudentVisa::where('user_id', $user->id)->count();
$workCount = WorkVisa::where('user_id', $user->id)->count();
$translationCount = Translation::where('user_id', $user->id)->count();
$attestationCount = Attestation::where('user_id', $user->id)->count();
$hajjUmrahCount = HajjUmrah::where('user_id', $user->id)->count();
$serviceAppCount = ServiceApplication::where('user_id', $user->id)->count();

echo "═══════════════════════════════════════════════════════════════\n";
echo "📊 DATABASE COUNTS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";
echo "Student Visas:     {$studentCount}\n";
echo "Work Visas:        {$workCount}\n";
echo "Translations:      {$translationCount}\n";
echo "Attestations:      {$attestationCount}\n";
echo "Hajj & Umrah:      {$hajjUmrahCount}\n";
echo "ServiceApps:       {$serviceAppCount}\n\n";

$expectedApps = 5;
if ($serviceAppCount >= $expectedApps) {
    echo "✅ SUCCESS: All {$expectedApps} applications created!\n";
} else {
    echo "⚠️  WARNING: Expected {$expectedApps} ServiceApplications, found {$serviceAppCount}\n";
}

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "🎉 TEST COMPLETE!\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "\n";
echo "Next Steps:\n";
echo "1. Check the applications in the web interface\n";
echo "2. Test API endpoints with these application IDs\n";
echo "3. Test agency quote submission flow\n";
echo "\n";
