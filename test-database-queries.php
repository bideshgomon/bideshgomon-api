<?php

/**
 * Comprehensive Database Query Tester
 * 
 * Tests all critical database queries to identify any QueryException errors
 * 
 * Usage: php test-database-queries.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Referral;
use App\Models\Reward;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\UserProfile;
use App\Models\UserPassport;
use App\Models\UserEducation;
use App\Models\UserWorkExperience;
use App\Models\UserLanguage;
use App\Models\ServiceModule;
use App\Models\ProfileAssessment;

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║         Database Query Exception Checker                        ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$tests = [];
$passed = 0;
$failed = 0;

function runTest($name, $callback) {
    global $passed, $failed;
    
    echo "🔍 Testing: {$name}... ";
    
    try {
        $result = $callback();
        echo "\033[1;32m✅ PASSED\033[0m";
        if ($result !== null) {
            echo " (returned: " . (is_array($result) ? count($result) . " records" : $result) . ")";
        }
        echo "\n";
        $passed++;
        return true;
    } catch (\Illuminate\Database\QueryException $e) {
        echo "\033[1;31m❌ FAILED - QueryException\033[0m\n";
        echo "   Error: " . $e->getMessage() . "\n";
        echo "   SQL: " . $e->getSql() . "\n";
        $failed++;
        return false;
    } catch (\Exception $e) {
        echo "\033[1;33m⚠️  FAILED - Other Exception\033[0m\n";
        echo "   Error: " . $e->getMessage() . "\n";
        $failed++;
        return false;
    }
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "USER & AUTHENTICATION QUERIES\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

runTest("Fetch all users", function() {
    return User::count();
});

runTest("Fetch user with profile", function() {
    return User::with('userProfile')->first();
});

runTest("Fetch user with wallet", function() {
    return User::with('wallet')->first();
});

runTest("Fetch user with referrals", function() {
    return User::with('referrals')->first();
});

runTest("Fetch user with rewards", function() {
    return User::with('rewards')->first();
});

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "REFERRAL SYSTEM QUERIES\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

runTest("Count all referrals", function() {
    return Referral::count();
});

runTest("Fetch referrals with reward_amount", function() {
    return Referral::select('id', 'referrer_id', 'referred_id', 'status', 'reward_amount', 'reward_paid')->get();
});

runTest("Calculate total earnings from referrals", function() {
    return Referral::where('reward_paid', 1)->sum('reward_amount');
});

runTest("Fetch completed referrals", function() {
    return Referral::where('is_completed', 1)->count();
});

runTest("Fetch referrals with rewards relationship", function() {
    return Referral::with('reward')->first();
});

runTest("Count all rewards", function() {
    return Reward::count();
});

runTest("Calculate total paid rewards", function() {
    return Reward::where('status', 'paid')->sum('amount');
});

runTest("Leaderboard query - top referrers", function() {
    $startDate = now()->startOfMonth();
    $endDate = now()->endOfMonth();
    
    return Referral::select(
            'referrals.referrer_id',
            DB::raw('COUNT(DISTINCT referrals.id) as referral_count'),
            DB::raw('COALESCE(SUM(CASE WHEN rewards.status = "paid" THEN rewards.amount ELSE 0 END), 0) as total_earnings')
        )
        ->leftJoin('rewards', 'referrals.id', '=', 'rewards.referral_id')
        ->whereBetween('referrals.created_at', [$startDate, $endDate])
        ->where('referrals.status', 'completed')
        ->groupBy('referrals.referrer_id')
        ->orderByDesc('referral_count')
        ->limit(5)
        ->get();
});

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "WALLET SYSTEM QUERIES\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

runTest("Count all wallets", function() {
    return Wallet::count();
});

runTest("Fetch wallet with user", function() {
    return Wallet::with('user')->first();
});

runTest("Count all wallet transactions", function() {
    return WalletTransaction::count();
});

runTest("Fetch transactions by type", function() {
    return WalletTransaction::where('type', 'credit')->count();
});

runTest("Calculate total credits", function() {
    return WalletTransaction::where('type', 'credit')->sum('amount');
});

runTest("Calculate total debits", function() {
    return WalletTransaction::where('type', 'debit')->sum('amount');
});

runTest("Fetch wallet transactions with wallet relationship", function() {
    return WalletTransaction::with('wallet')->first();
});

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "PROFILE SYSTEM QUERIES\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

runTest("Count all user profiles", function() {
    return UserProfile::count();
});

runTest("Fetch profiles with all new columns", function() {
    return UserProfile::select('id', 'user_id', 'first_name', 'middle_name', 'last_name', 
        'nid_number', 'city', 'state', 'postal_code', 'country')->first();
});

runTest("Count all passports", function() {
    return UserPassport::count();
});

runTest("Fetch passports with new columns", function() {
    return UserPassport::select('id', 'user_id', 'passport_number', 'issue_country', 
        'issue_place', 'scan_front_upload', 'scan_back_upload', 'is_primary')->first();
});

runTest("Count all educations", function() {
    return UserEducation::count();
});

runTest("Fetch educations with degree_certificate_path", function() {
    return UserEducation::select('id', 'user_id', 'institution_name', 'degree_certificate_path', 
        'transcript_path')->first();
});

runTest("Count all work experiences", function() {
    return UserWorkExperience::count();
});

runTest("Fetch work experiences with new columns", function() {
    return UserWorkExperience::select('id', 'user_id', 'company_name', 'job_title', 
        'is_current', 'responsibilities', 'achievements')->first();
});

runTest("Count all languages", function() {
    return UserLanguage::count();
});

runTest("Fetch languages with new columns", function() {
    return UserLanguage::select('id', 'user_id', 'language', 'proficiency_level', 
        'test_type', 'overall_score', 'certificate_path')->first();
});

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "SERVICE & ASSESSMENT QUERIES\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

runTest("Count all service modules", function() {
    return ServiceModule::count();
});

runTest("Fetch services with description", function() {
    return ServiceModule::select('id', 'name', 'slug', 'description', 'service_type')->first();
});

runTest("Fetch revenue services", function() {
    return ServiceModule::where('service_type', 'revenue_service')->count();
});

runTest("Fetch platform tool services", function() {
    return ServiceModule::where('service_type', 'platform_tool')->count();
});

runTest("Count all profile assessments", function() {
    return ProfileAssessment::count();
});

runTest("Fetch assessments with new columns", function() {
    return ProfileAssessment::select('id', 'user_id', 'overall_score', 'visa_readiness',
        'language_score', 'family_score', 'usa_readiness', 'canada_readiness')->first();
});

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "RELATIONSHIP QUERIES\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

runTest("User → Profile → Passports", function() {
    return User::with(['userProfile.userPassports'])->first();
});

runTest("User → Educations", function() {
    return User::with('educations')->first();
});

runTest("User → Work Experiences", function() {
    return User::with('workExperiences')->first();
});

runTest("User → Languages", function() {
    return User::with('languages')->first();
});

runTest("Referral → Referrer → Wallet", function() {
    return Referral::with(['referrer.wallet'])->first();
});

runTest("Wallet → Transactions", function() {
    return Wallet::with('transactions')->first();
});

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 SUMMARY\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$total = $passed + $failed;

echo "Total Tests:  {$total}\n";
echo "\033[1;32mPassed:       {$passed}\033[0m\n";

if ($failed > 0) {
    echo "\033[1;31mFailed:       {$failed}\033[0m\n";
    echo "\n";
    echo "\033[1;31m❌ Some database queries failed! Review the errors above.\033[0m\n\n";
    exit(1);
} else {
    echo "\033[0;90mFailed:       {$failed}\033[0m\n";
    echo "\n";
    echo "\033[1;32m✅ All database queries passed successfully!\033[0m\n\n";
}
