<?php

/**
 * Comprehensive Database Schema Checker
 * 
 * Checks all tables and their columns to ensure database integrity
 * across the entire BideshGomon platform
 * 
 * Usage: php check-database.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║         BideshGomon Platform - Database Schema Checker          ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$databaseName = config('database.connections.mysql.database');
echo "📊 Database: {$databaseName}\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Define expected schema for critical tables
$expectedSchema = [
    'users' => [
        'id', 'name', 'email', 'email_verified_at', 'password', 'phone', 
        'referral_code', 'remember_token', 'created_at', 'updated_at'
    ],
    'user_profiles' => [
        'id', 'user_id', 'first_name', 'middle_name', 'last_name', 'name_as_per_passport',
        'date_of_birth', 'gender', 'nationality', 'nid_number', 'nid_scan_upload',
        'birth_certificate_number', 'birth_certificate_upload', 'profile_picture',
        'marital_status', 'religion', 'blood_group', 'present_address', 'permanent_address',
        'city', 'state', 'postal_code', 'country', 'created_at', 'updated_at'
    ],
    'user_passports' => [
        'id', 'user_id', 'passport_number', 'passport_type', 'issue_date', 'expiry_date',
        'issue_country', 'issue_place', 'scan_front_upload', 'scan_back_upload',
        'is_primary', 'notes', 'created_at', 'updated_at'
    ],
    'user_educations' => [
        'id', 'user_id', 'institution_name', 'degree', 'degree_level', 'field_of_study', 
        'start_date', 'end_date', 'country', 'city', 'is_completed', 'gpa_or_grade',
        'language_of_instruction', 'courses_completed', 'honors_awards',
        'degree_certificate_path', 'transcript_path', 'created_at', 'updated_at', 'deleted_at'
    ],
    'user_work_experiences' => [
        'id', 'user_id', 'company_name', 'job_title', 'employment_type',
        'start_date', 'end_date', 'is_current', 'country', 'city',
        'responsibilities', 'achievements', 'reference_name', 'reference_contact',
        'experience_letter_path', 'created_at', 'updated_at', 'deleted_at'
    ],
    'user_languages' => [
        'id', 'user_id', 'language', 'proficiency_level', 'test_type',
        'test_date', 'overall_score', 'listening_score', 'reading_score',
        'writing_score', 'speaking_score', 'certificate_path',
        'created_at', 'updated_at', 'deleted_at'
    ],
    'referrals' => [
        'id', 'referrer_id', 'referred_id', 'referral_code', 'status',
        'is_completed', 'completed_at', 'reward_amount', 'reward_paid',
        'created_at', 'updated_at'
    ],
    'rewards' => [
        'id', 'referral_id', 'user_id', 'amount', 'reward_type', 'status',
        'description', 'approved_by', 'approved_at', 'created_at', 'updated_at'
    ],
    'wallets' => [
        'id', 'user_id', 'balance', 'currency', 'created_at', 'updated_at'
    ],
    'wallet_transactions' => [
        'id', 'wallet_id', 'type', 'amount', 'balance_before', 'balance_after',
        'description', 'reference_type', 'reference_id', 'created_at', 'updated_at'
    ],
    'countries' => [
        'id', 'name', 'iso_code_2', 'iso_code_3', 'phone_code', 'capital',
        'currency', 'currency_symbol', 'nationality', 'region',
        'created_at', 'updated_at'
    ],
    'service_modules' => [
        'id', 'name', 'slug', 'description', 'icon', 'service_type',
        'is_active', 'is_featured', 'sort_order', 'created_at', 'updated_at'
    ],
    'profile_assessments' => [
        'id', 'user_id', 'overall_score', 'profile_completeness',
        'document_readiness', 'visa_readiness', 'recommendations',
        'missing_documents', 'strengths', 'weaknesses',
        'education_score', 'work_experience_score', 'language_score',
        'financial_score', 'travel_history_score', 'family_score',
        'document_quality_score', 'data_quality_score',
        'profile_strength', 'visa_success_probability',
        'priority_actions', 'quick_wins', 'long_term_improvements',
        'estimated_completion_time', 'next_milestone',
        'usa_readiness', 'canada_readiness', 'uk_readiness',
        'australia_readiness', 'europe_readiness',
        'last_assessed_at', 'created_at', 'updated_at'
    ],
];

$allTables = DB::select('SHOW TABLES');
$tableKey = 'Tables_in_' . $databaseName;
$issues = [];
$warnings = [];
$stats = [
    'total_tables' => 0,
    'checked_tables' => 0,
    'missing_columns' => 0,
    'unexpected_columns' => 0,
    'total_records' => 0,
];

echo "🔍 Scanning database tables...\n\n";

foreach ($allTables as $table) {
    $tableName = $table->$tableKey;
    $stats['total_tables']++;
    
    // Get column information
    $columns = DB::select("DESCRIBE {$tableName}");
    $columnNames = array_map(fn($col) => $col->Field, $columns);
    
    // Get row count
    $rowCount = DB::table($tableName)->count();
    $stats['total_records'] += $rowCount;
    
    echo "📋 Table: \033[1;36m{$tableName}\033[0m";
    echo " (" . count($columnNames) . " columns, {$rowCount} rows)\n";
    
    // Check if this is a critical table with expected schema
    if (isset($expectedSchema[$tableName])) {
        $stats['checked_tables']++;
        $expected = $expectedSchema[$tableName];
        $missing = array_diff($expected, $columnNames);
        $unexpected = array_diff($columnNames, $expected);
        
        if (!empty($missing)) {
            $stats['missing_columns'] += count($missing);
            $issues[] = "❌ Table '{$tableName}' is missing columns: " . implode(', ', $missing);
            echo "   \033[1;31m❌ Missing columns:\033[0m " . implode(', ', $missing) . "\n";
        }
        
        if (!empty($unexpected)) {
            $stats['unexpected_columns'] += count($unexpected);
            $warnings[] = "⚠️  Table '{$tableName}' has unexpected columns: " . implode(', ', $unexpected);
            echo "   \033[1;33m⚠️  Unexpected columns:\033[0m " . implode(', ', $unexpected) . "\n";
        }
        
        if (empty($missing) && empty($unexpected)) {
            echo "   \033[1;32m✅ Schema matches expected structure\033[0m\n";
        }
    } else {
        echo "   \033[0;90mℹ️  Columns:\033[0m " . implode(', ', array_slice($columnNames, 0, 5));
        if (count($columnNames) > 5) {
            echo ", ... (+" . (count($columnNames) - 5) . " more)";
        }
        echo "\n";
    }
    
    echo "\n";
}

// Summary
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n📊 SUMMARY\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "Total Tables:            {$stats['total_tables']}\n";
echo "Critical Tables Checked: {$stats['checked_tables']}\n";
echo "Total Records:           " . number_format($stats['total_records']) . "\n";
echo "Missing Columns:         {$stats['missing_columns']}\n";
echo "Unexpected Columns:      {$stats['unexpected_columns']}\n";

echo "\n";

if (!empty($issues)) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "\n🚨 CRITICAL ISSUES\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    foreach ($issues as $issue) {
        echo $issue . "\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "\n⚠️  WARNINGS\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    foreach ($warnings as $warning) {
        echo $warning . "\n";
    }
    echo "\n";
}

// Check for common relationships
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n🔗 RELATIONSHIP INTEGRITY CHECKS\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Check users without wallets
$usersWithoutWallets = DB::table('users')
    ->leftJoin('wallets', 'users.id', '=', 'wallets.user_id')
    ->whereNull('wallets.id')
    ->count();

if ($usersWithoutWallets > 0) {
    echo "⚠️  {$usersWithoutWallets} users without wallets\n";
} else {
    echo "✅ All users have wallets\n";
}

// Check referrals without rewards
$completedReferralsWithoutRewards = DB::table('referrals')
    ->where('is_completed', 1)
    ->leftJoin('rewards', 'referrals.id', '=', 'rewards.referral_id')
    ->whereNull('rewards.id')
    ->count();

if ($completedReferralsWithoutRewards > 0) {
    echo "⚠️  {$completedReferralsWithoutRewards} completed referrals without rewards\n";
} else {
    echo "✅ All completed referrals have rewards\n";
}

// Check users without profiles
$usersWithoutProfiles = DB::table('users')
    ->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id')
    ->whereNull('user_profiles.id')
    ->count();

if ($usersWithoutProfiles > 0) {
    echo "⚠️  {$usersWithoutProfiles} users without profiles\n";
} else {
    echo "✅ All users have profiles\n";
}

// Check orphaned records
$orphanedWallets = DB::table('wallets')
    ->leftJoin('users', 'wallets.user_id', '=', 'users.id')
    ->whereNull('users.id')
    ->count();

if ($orphanedWallets > 0) {
    echo "❌ {$orphanedWallets} orphaned wallet records (no matching user)\n";
} else {
    echo "✅ No orphaned wallet records\n";
}

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

if (empty($issues)) {
    echo "\n\033[1;32m✅ Database schema check completed successfully!\033[0m\n\n";
} else {
    echo "\n\033[1;31m❌ Database schema check found " . count($issues) . " critical issue(s)!\033[0m\n\n";
    exit(1);
}
