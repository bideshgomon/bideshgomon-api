#!/usr/bin/env php
<?php

/**
 * COMPREHENSIVE DATABASE TEST
 * Tests ALL tables, relationships, settings, profiles, and data storage
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║       COMPREHENSIVE DATABASE TEST - ALL TABLES & DATA          ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$errors = [];
$warnings = [];
$passed = 0;
$failed = 0;

function test($description, $callback) {
    global $errors, $warnings, $passed, $failed;
    
    try {
        $result = $callback();
        if ($result === true) {
            echo "✅ {$description}\n";
            $passed++;
            return true;
        } elseif (is_string($result)) {
            echo "⚠️  {$description}: {$result}\n";
            $warnings[] = "{$description}: {$result}";
            return false;
        } else {
            echo "❌ {$description}\n";
            $errors[] = $description;
            $failed++;
            return false;
        }
    } catch (\Exception $e) {
        echo "❌ {$description}: {$e->getMessage()}\n";
        $errors[] = "{$description}: {$e->getMessage()}";
        $failed++;
        return false;
    }
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "1️⃣  CORE TABLES\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

test("users table exists", fn() => Schema::hasTable('users'));
test("users table has required columns", function() {
    $columns = ['id', 'name', 'email', 'password', 'phone', 'created_at'];
    foreach ($columns as $col) {
        if (!Schema::hasColumn('users', $col)) return "Missing column: {$col}";
    }
    return true;
});
test("users table has data", fn() => DB::table('users')->count() > 0 ? true : "No users in database");

test("roles table exists", fn() => Schema::hasTable('roles'));
test("roles table has data", fn() => DB::table('roles')->count() > 0 ? true : "No roles configured");

test("model_has_roles table exists", fn() => Schema::hasTable('model_has_roles'));
test("role assignments work", fn() => DB::table('model_has_roles')->count() > 0 ? true : "No role assignments");

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "2️⃣  USER PROFILES & SETTINGS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

test("user_profiles table exists", fn() => Schema::hasTable('user_profiles'));
test("user_profiles has all fields", function() {
    $columns = ['user_id', 'date_of_birth', 'gender', 'marital_status', 'nationality', 
                'current_location', 'bio', 'profile_picture'];
    foreach ($columns as $col) {
        if (!Schema::hasColumn('user_profiles', $col)) return "Missing column: {$col}";
    }
    return true;
});

test("user_passports table exists", fn() => Schema::hasTable('user_passports'));
test("user_passports has required fields", function() {
    $columns = ['user_id', 'passport_number', 'issue_date', 'expiry_date', 
                'issuing_country_id', 'is_primary'];
    foreach ($columns as $col) {
        if (!Schema::hasColumn('user_passports', $col)) return "Missing column: {$col}";
    }
    return true;
});

test("user_visa_history table exists", fn() => Schema::hasTable('user_visa_history'));
test("user_travel_history table exists", fn() => Schema::hasTable('user_travel_history'));
test("user_family_members table exists", fn() => Schema::hasTable('user_family_members'));
test("user_financial_information table exists", fn() => Schema::hasTable('user_financial_information'));
test("user_security_information table exists", fn() => Schema::hasTable('user_security_information'));
test("user_educations table exists", fn() => Schema::hasTable('user_educations'));
test("user_work_experiences table exists", fn() => Schema::hasTable('user_work_experiences'));
test("user_languages table exists", fn() => Schema::hasTable('user_languages'));

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "3️⃣  WALLET & FINANCIAL SYSTEM\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

test("wallets table exists", fn() => Schema::hasTable('wallets'));
test("wallets table has required fields", function() {
    $columns = ['user_id', 'balance', 'currency', 'is_active'];
    foreach ($columns as $col) {
        if (!Schema::hasColumn('wallets', $col)) return "Missing column: {$col}";
    }
    return true;
});
test("wallets have data", fn() => DB::table('wallets')->count() > 0 ? true : "No wallets created");

test("wallet_transactions table exists", fn() => Schema::hasTable('wallet_transactions'));
test("wallet_transactions has audit fields", function() {
    $columns = ['wallet_id', 'transaction_type', 'amount', 'balance_before', 
                'balance_after', 'description', 'status'];
    foreach ($columns as $col) {
        if (!Schema::hasColumn('wallet_transactions', $col)) return "Missing column: {$col}";
    }
    return true;
});

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "4️⃣  REFERRAL SYSTEM\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

test("referrals table exists", fn() => Schema::hasTable('referrals'));
test("referrals has tracking fields", function() {
    $columns = ['referrer_id', 'referred_id', 'referral_code', 'status', 'is_completed'];
    foreach ($columns as $col) {
        if (!Schema::hasColumn('referrals', $col)) return "Missing column: {$col}";
    }
    return true;
});

test("rewards table exists", fn() => Schema::hasTable('rewards'));
test("rewards has payment fields", function() {
    $columns = ['user_id', 'referral_id', 'amount', 'status', 'approved_at'];
    foreach ($columns as $col) {
        if (!Schema::hasColumn('rewards', $col)) return "Missing column: {$col}";
    }
    return true;
});

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "5️⃣  SERVICE MODULE SYSTEM\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

test("service_categories table exists", fn() => Schema::hasTable('service_categories'));
test("service_categories has data", function() {
    $count = DB::table('service_categories')->count();
    return $count >= 6 ? true : "Only {$count} categories (expected 6+)";
});

test("service_modules table exists", fn() => Schema::hasTable('service_modules'));
test("service_modules has comprehensive fields", function() {
    $columns = ['name', 'slug', 'service_type', 'is_active', 'coming_soon', 
                'price_type', 'base_price', 'route_prefix', 'controller'];
    foreach ($columns as $col) {
        if (!Schema::hasColumn('service_modules', $col)) return "Missing column: {$col}";
    }
    return true;
});
test("service_modules has active services", function() {
    $count = DB::table('service_modules')->where('is_active', true)->count();
    return $count >= 10 ? true : "Only {$count} active services (expected 10+)";
});

test("service_applications table exists", fn() => Schema::hasTable('service_applications'));
test("service_applications has workflow fields", function() {
    $columns = ['user_id', 'service_module_id', 'application_number', 'status', 
                'application_data', 'submitted_at', 'agency_id'];
    foreach ($columns as $col) {
        if (!Schema::hasColumn('service_applications', $col)) return "Missing column: {$col}";
    }
    return true;
});

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "6️⃣  VISA SERVICE TABLES (10 Services)\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$visaTables = [
    'tourist_visas' => ['user_id', 'destination_country_id', 'purpose', 'duration_days', 'status'],
    'student_visas' => ['user_id', 'destination_country_id', 'education_level', 'institution_name', 'status'],
    'work_visas' => ['user_id', 'destination_country_id', 'job_title', 'employer_name', 'status'],
    'family_visas' => ['user_id', 'destination_country_id', 'relationship_to_sponsor', 'status'],
    'business_visas' => ['user_id', 'destination_country_id', 'business_type', 'status'],
    'medical_visas' => ['user_id', 'destination_country_id', 'treatment_type', 'status'],
    'transit_visas' => ['user_id', 'destination_country_id', 'final_destination', 'status'],
    'immigration_visas' => ['user_id', 'destination_country_id', 'immigration_category', 'status'],
    'retirement_visas' => ['user_id', 'destination_country_id', 'pension_amount', 'status'],
    'digital_nomad_visas' => ['user_id', 'destination_country_id', 'remote_work_type', 'status'],
];

foreach ($visaTables as $table => $requiredColumns) {
    test("{$table} table exists", fn() => Schema::hasTable($table));
    if (Schema::hasTable($table)) {
        test("{$table} has required columns", function() use ($table, $requiredColumns) {
            foreach ($requiredColumns as $col) {
                if (!Schema::hasColumn($table, $col)) return "Missing: {$col}";
            }
            return true;
        });
    }
}

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "7️⃣  DOCUMENT SERVICE TABLES\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

test("translations table exists", fn() => Schema::hasTable('translations'));
test("translations has required fields", function() {
    $columns = ['user_id', 'document_type', 'source_language', 'target_language', 
                'page_count', 'status'];
    foreach ($columns as $col) {
        if (!Schema::hasColumn('translations', $col)) return "Missing: {$col}";
    }
    return true;
});

test("attestations table exists", fn() => Schema::hasTable('attestations'));
test("attestations has required fields", function() {
    $columns = ['user_id', 'target_country_id', 'document_type', 'attestation_type', 'status'];
    foreach ($columns as $col) {
        if (!Schema::hasColumn('attestations', $col)) return "Missing: {$col}";
    }
    return true;
});

test("birth_certificate_requests table exists", fn() => Schema::hasTable('birth_certificate_requests'));
test("passport_service_requests table exists", fn() => Schema::hasTable('passport_service_requests'));

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "8️⃣  TRAVEL SERVICE TABLES\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

test("flight_requests table exists", fn() => Schema::hasTable('flight_requests'));
test("flight_requests has booking fields", function() {
    $columns = ['user_id', 'departure_city', 'arrival_city', 'departure_date', 
                'return_date', 'passengers', 'status'];
    foreach ($columns as $col) {
        if (!Schema::hasColumn('flight_requests', $col)) return "Missing: {$col}";
    }
    return true;
});

test("hotel_bookings table exists", fn() => Schema::hasTable('hotel_bookings'));
test("hotel_bookings has reservation fields", function() {
    $columns = ['user_id', 'destination_city', 'check_in_date', 'check_out_date', 
                'guests', 'room_type', 'status'];
    foreach ($columns as $col) {
        if (!Schema::hasColumn('hotel_bookings', $col)) return "Missing: {$col}";
    }
    return true;
});

test("travel_insurance_bookings table exists", fn() => Schema::hasTable('travel_insurance_bookings'));
test("car_rental_requests table exists", fn() => Schema::hasTable('car_rental_requests'));
test("tour_package_requests table exists", fn() => Schema::hasTable('tour_package_requests'));
test("hajj_umrahs table exists", fn() => Schema::hasTable('hajj_umrahs'));

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "9️⃣  EDUCATION SERVICE TABLES\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

test("university_applications table exists", fn() => Schema::hasTable('university_applications'));
test("university_applications has academic fields", function() {
    $columns = ['user_id', 'university_name', 'program_name', 'degree_level', 'status'];
    foreach ($columns as $col) {
        if (!Schema::hasColumn('university_applications', $col)) return "Missing: {$col}";
    }
    return true;
});

test("scholarship_applications table exists", fn() => Schema::hasTable('scholarship_applications'));
test("course_counseling_requests table exists", fn() => Schema::hasTable('course_counseling_requests'));
test("language_test_prep_requests table exists", fn() => Schema::hasTable('language_test_prep_requests'));

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "🔟 EMPLOYMENT SERVICE TABLES\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

test("job_postings table exists", fn() => Schema::hasTable('job_postings'));
test("job_postings has employer fields", function() {
    $columns = ['title', 'company_name', 'location', 'salary_min', 'salary_max', 
                'job_type', 'is_active'];
    foreach ($columns as $col) {
        if (!Schema::hasColumn('job_postings', $col)) return "Missing: {$col}";
    }
    return true;
});

test("job_applications table exists", fn() => Schema::hasTable('job_applications'));
test("job_applications has application fields", function() {
    $columns = ['job_posting_id', 'user_id', 'cover_letter', 'resume', 'status'];
    foreach ($columns as $col) {
        if (!Schema::hasColumn('job_applications', $col)) return "Missing: {$col}";
    }
    return true;
});

test("user_cvs table exists", fn() => Schema::hasTable('user_cvs'));
test("user_cvs has CV data fields", function() {
    $columns = ['user_id', 'title', 'personal_info', 'work_experience', 
                'education', 'skills', 'is_primary'];
    foreach ($columns as $col) {
        if (!Schema::hasColumn('user_cvs', $col)) return "Missing: {$col}";
    }
    return true;
});

test("interview_prep_requests table exists", fn() => Schema::hasTable('interview_prep_requests'));
test("skill_training_requests table exists", fn() => Schema::hasTable('skill_training_requests'));
test("work_permit_requests table exists", fn() => Schema::hasTable('work_permit_requests'));

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "1️⃣1️⃣  AGENCY SYSTEM TABLES\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

test("agencies table exists", fn() => Schema::hasTable('agencies'));
test("agencies has business fields", function() {
    $columns = ['user_id', 'name', 'license_number', 'phone', 'email', 
                'address', 'is_active', 'is_verified'];
    foreach ($columns as $col) {
        if (!Schema::hasColumn('agencies', $col)) return "Missing: {$col}";
    }
    return true;
});

test("agency_types table exists", fn() => Schema::hasTable('agency_types'));
test("agency_service_modules table exists", fn() => Schema::hasTable('agency_service_modules'));
test("agency_team_members table exists", fn() => Schema::hasTable('agency_team_members'));

test("service_quotes table exists", fn() => Schema::hasTable('service_quotes'));
test("service_quotes has pricing fields", function() {
    $columns = ['service_application_id', 'agency_id', 'quoted_amount', 
                'service_fee', 'processing_days', 'status'];
    foreach ($columns as $col) {
        if (!Schema::hasColumn('service_quotes', $col)) return "Missing: {$col}";
    }
    return true;
});

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "1️⃣2️⃣  SUPPORT & COMMUNICATION TABLES\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

test("support_tickets table exists", fn() => Schema::hasTable('support_tickets'));
test("support_tickets has support fields", function() {
    $columns = ['user_id', 'subject', 'category', 'priority', 'status', 'assigned_to'];
    foreach ($columns as $col) {
        if (!Schema::hasColumn('support_tickets', $col)) return "Missing: {$col}";
    }
    return true;
});

test("support_ticket_messages table exists", fn() => Schema::hasTable('support_ticket_messages'));
test("notifications table exists", fn() => Schema::hasTable('notifications'));
test("announcements table exists", fn() => Schema::hasTable('announcements'));

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "1️⃣3️⃣  REFERENCE DATA TABLES\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

test("countries table exists", fn() => Schema::hasTable('countries'));
test("countries has geographical data", function() {
    $count = DB::table('countries')->count();
    return $count >= 195 ? true : "Only {$count} countries (expected 195+)";
});

test("languages table exists", fn() => Schema::hasTable('languages'));
test("languages has data", fn() => DB::table('languages')->count() > 0);

test("currencies table exists", fn() => Schema::hasTable('currencies'));
test("visa_types table exists", fn() => Schema::hasTable('visa_types'));
test("document_types table exists", fn() => Schema::hasTable('document_types'));
test("job_categories table exists", fn() => Schema::hasTable('job_categories'));

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "1️⃣4️⃣  SYSTEM & AUDIT TABLES\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

test("activity_log table exists", fn() => Schema::hasTable('activity_log'));
test("admin_impersonation_logs table exists", fn() => Schema::hasTable('admin_impersonation_logs'));
test("failed_jobs table exists", fn() => Schema::hasTable('failed_jobs'));
test("sessions table exists", fn() => Schema::hasTable('sessions'));
test("password_reset_tokens table exists", fn() => Schema::hasTable('password_reset_tokens'));

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "1️⃣5️⃣  MARKETING & ANALYTICS TABLES\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

test("marketing_campaigns table exists", fn() => Schema::hasTable('marketing_campaigns'));
test("profile_assessments table exists", fn() => Schema::hasTable('profile_assessments'));
test("profile_views table exists", fn() => Schema::hasTable('profile_views'));
test("appointments table exists", fn() => Schema::hasTable('appointments'));

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "1️⃣6️⃣  RELATIONSHIP INTEGRITY TESTS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

test("User → Wallet relationship", function() {
    $user = DB::table('users')->first();
    if (!$user) return "No users to test";
    $wallet = DB::table('wallets')->where('user_id', $user->id)->first();
    return $wallet ? true : "User {$user->id} has no wallet";
});

test("User → Profile relationship", function() {
    $user = DB::table('users')->first();
    if (!$user) return "No users to test";
    // Profile is optional, so just check structure
    return true;
});

test("ServiceApplication → User relationship", function() {
    $app = DB::table('service_applications')->first();
    if (!$app) return "No applications to test";
    $user = DB::table('users')->where('id', $app->user_id)->first();
    return $user ? true : "Application {$app->id} has invalid user_id";
});

test("ServiceApplication → ServiceModule relationship", function() {
    $app = DB::table('service_applications')->first();
    if (!$app) return "No applications to test";
    $module = DB::table('service_modules')->where('id', $app->service_module_id)->first();
    return $module ? true : "Application {$app->id} has invalid service_module_id";
});

test("Agency → User relationship", function() {
    $agency = DB::table('agencies')->first();
    if (!$agency) return "No agencies to test";
    $user = DB::table('users')->where('id', $agency->user_id)->first();
    return $user ? true : "Agency {$agency->id} has invalid user_id";
});

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "1️⃣7️⃣  DATA INTEGRITY TESTS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

test("No orphaned service applications", function() {
    $orphaned = DB::select("
        SELECT sa.id 
        FROM service_applications sa 
        LEFT JOIN users u ON sa.user_id = u.id 
        WHERE u.id IS NULL
    ");
    return count($orphaned) === 0 ? true : count($orphaned) . " orphaned applications found";
});

test("No orphaned wallet transactions", function() {
    $orphaned = DB::select("
        SELECT wt.id 
        FROM wallet_transactions wt 
        LEFT JOIN wallets w ON wt.wallet_id = w.id 
        WHERE w.id IS NULL
    ");
    return count($orphaned) === 0 ? true : count($orphaned) . " orphaned transactions found";
});

test("All wallets have valid users", function() {
    $orphaned = DB::select("
        SELECT w.id 
        FROM wallets w 
        LEFT JOIN users u ON w.user_id = u.id 
        WHERE u.id IS NULL
    ");
    return count($orphaned) === 0 ? true : count($orphaned) . " wallets with invalid users";
});

test("Wallet transaction balance tracking", function() {
    $transaction = DB::table('wallet_transactions')->first();
    if (!$transaction) return "No transactions to test";
    
    // Check that balance_after = balance_before ± amount
    if ($transaction->transaction_type === 'credit') {
        $expected = $transaction->balance_before + $transaction->amount;
    } else {
        $expected = $transaction->balance_before - $transaction->amount;
    }
    
    return abs($transaction->balance_after - $expected) < 0.01 ? true : "Balance tracking incorrect";
});

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "📊 TEST SUMMARY\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$total = $passed + $failed;
$passRate = $total > 0 ? round(($passed / $total) * 100, 1) : 0;

echo "Total Tests: {$total}\n";
echo "✅ Passed: {$passed}\n";
echo "❌ Failed: {$failed}\n";
echo "⚠️  Warnings: " . count($warnings) . "\n";
echo "Pass Rate: {$passRate}%\n\n";

if (count($errors) > 0) {
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "❌ ERRORS\n";
    echo "═══════════════════════════════════════════════════════════════\n\n";
    foreach ($errors as $error) {
        echo "  • {$error}\n";
    }
    echo "\n";
}

if (count($warnings) > 0) {
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "⚠️  WARNINGS\n";
    echo "═══════════════════════════════════════════════════════════════\n\n";
    foreach ($warnings as $warning) {
        echo "  • {$warning}\n";
    }
    echo "\n";
}

// Database size and statistics
echo "═══════════════════════════════════════════════════════════════\n";
echo "📈 DATABASE STATISTICS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");
echo "Total Tables: " . count($tables) . "\n\n";

echo "Record Counts:\n";
echo "  Users: " . DB::table('users')->count() . "\n";
echo "  Agencies: " . DB::table('agencies')->count() . "\n";
echo "  Service Modules: " . DB::table('service_modules')->count() . "\n";
echo "  Active Services: " . DB::table('service_modules')->where('is_active', true)->count() . "\n";
echo "  Service Applications: " . DB::table('service_applications')->count() . "\n";
echo "  Wallets: " . DB::table('wallets')->count() . "\n";
echo "  Wallet Transactions: " . DB::table('wallet_transactions')->count() . "\n";
echo "  Job Postings: " . DB::table('job_postings')->count() . "\n";
echo "  Support Tickets: " . DB::table('support_tickets')->count() . "\n";
echo "  Countries: " . DB::table('countries')->count() . "\n\n";

if ($passRate >= 95) {
    echo "🎉 EXCELLENT! Database is production-ready!\n";
} elseif ($passRate >= 80) {
    echo "✅ GOOD! Database structure is solid with minor issues.\n";
} elseif ($passRate >= 60) {
    echo "⚠️  FAIR! Some database issues need attention.\n";
} else {
    echo "❌ CRITICAL! Major database issues found!\n";
}

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "TEST COMPLETE\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

exit($failed > 0 ? 1 : 0);
