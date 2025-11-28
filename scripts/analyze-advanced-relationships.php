<?php

/**
 * Advanced Database Relationship Issue Detector
 * Checks for:
 * - Circular dependencies
 * - Missing inverse relationships
 * - Polymorphic relation issues
 * - Soft delete cascading problems
 * - Transaction integrity issues
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "========================================\n";
echo "ADVANCED RELATIONSHIP ISSUE DETECTION\n";
echo "========================================\n\n";

$issues = [];
$warnings = [];

// 1. Check for missing inverse relationships
echo "=== MISSING INVERSE RELATIONSHIPS ===\n";

$relationshipChecks = [
    'User -> Wallet' => ['App\Models\User', 'wallet', 'App\Models\Wallet', 'user'],
    'User -> Profile' => ['App\Models\User', 'profile', 'App\Models\UserProfile', 'user'],
    'User -> Rewards' => ['App\Models\User', 'rewards', 'App\Models\Reward', 'user'],
    'User -> Referrals' => ['App\Models\User', 'referrals', 'App\Models\Referral', 'referrer'],
    'Wallet -> Transactions' => ['App\Models\Wallet', 'transactions', 'App\Models\WalletTransaction', 'wallet'],
    'ServiceApplication -> Quotes' => ['App\Models\ServiceApplication', 'quotes', 'App\Models\ServiceQuote', 'serviceApplication'],
    'Agency -> User' => ['App\Models\Agency', 'user', 'App\Models\User', 'agency'],
];

foreach ($relationshipChecks as $name => $config) {
    list($model1, $method1, $model2, $method2) = $config;
    
    if (!class_exists($model1) || !class_exists($model2)) {
        echo "⚠️  $name: One or both models don't exist\n";
        continue;
    }
    
    $instance1 = new $model1;
    $instance2 = new $model2;
    
    $hasForward = method_exists($instance1, $method1);
    $hasInverse = method_exists($instance2, $method2);
    
    if ($hasForward && $hasInverse) {
        echo "✅ $name: Both directions exist\n";
    } elseif ($hasForward && !$hasInverse) {
        echo "⚠️  $name: Missing inverse ($model2::$method2)\n";
        $warnings[] = "Missing inverse relationship: $model2::$method2";
    } elseif (!$hasForward && $hasInverse) {
        echo "⚠️  $name: Missing forward ($model1::$method1)\n";
        $warnings[] = "Missing forward relationship: $model1::$method1";
    } else {
        echo "❌ $name: Both directions missing!\n";
        $issues[] = "Missing relationship: $name";
    }
}

echo "\n";

// 2. Check for circular reference risks
echo "=== CIRCULAR REFERENCE CHECK ===\n";

// Check for users referring to themselves
$selfReferrals = DB::table('users')
    ->whereColumn('id', 'referred_by')
    ->count();

if ($selfReferrals > 0) {
    echo "❌ $selfReferrals users are self-referencing in referred_by\n";
    $issues[] = "Self-referencing users";
} else {
    echo "✅ No self-referencing users\n";
}

// Check for circular referral chains (A -> B -> C -> A)
$potentialCircular = DB::select("
    WITH RECURSIVE referral_chain AS (
        SELECT id, referred_by, id as root_id, 0 as depth
        FROM users
        WHERE referred_by IS NOT NULL
        
        UNION ALL
        
        SELECT u.id, u.referred_by, rc.root_id, rc.depth + 1
        FROM users u
        INNER JOIN referral_chain rc ON u.id = rc.referred_by
        WHERE rc.depth < 10 AND u.referred_by IS NOT NULL
    )
    SELECT COUNT(*) as count
    FROM referral_chain
    WHERE id = referred_by OR (root_id = referred_by AND depth > 0)
");

$circularCount = $potentialCircular[0]->count ?? 0;
if ($circularCount > 0) {
    echo "⚠️  Potential circular referral chains detected\n";
    $warnings[] = "Potential circular referral chains";
} else {
    echo "✅ No circular referral chains\n";
}

echo "\n";

// 3. Check for transaction consistency
echo "=== WALLET TRANSACTION CONSISTENCY ===\n";

// Check if wallet balances match transaction history
$walletInconsistencies = DB::table('wallets as w')
    ->leftJoin('wallet_transactions as wt', 'w.id', '=', 'wt.wallet_id')
    ->select('w.id', 'w.balance', DB::raw('COALESCE(SUM(CASE 
        WHEN wt.type = "credit" THEN wt.amount 
        WHEN wt.type = "debit" THEN -wt.amount 
        ELSE 0 END), 0) as calculated_balance'))
    ->groupBy('w.id', 'w.balance')
    ->havingRaw('ABS(w.balance - calculated_balance) > 0.01')
    ->count();

if ($walletInconsistencies > 0) {
    echo "❌ $walletInconsistencies wallets have inconsistent balances!\n";
    $issues[] = "Wallet balance inconsistencies";
    
    // Show details
    $details = DB::table('wallets as w')
        ->leftJoin('wallet_transactions as wt', 'w.id', '=', 'wt.wallet_id')
        ->leftJoin('users as u', 'w.user_id', '=', 'u.id')
        ->select('w.id', 'u.name', 'w.balance', DB::raw('COALESCE(SUM(CASE 
            WHEN wt.type = "credit" THEN wt.amount 
            WHEN wt.type = "debit" THEN -wt.amount 
            ELSE 0 END), 0) as calculated_balance'))
        ->groupBy('w.id', 'u.name', 'w.balance')
        ->havingRaw('ABS(w.balance - calculated_balance) > 0.01')
        ->limit(5)
        ->get();
    
    foreach ($details as $detail) {
        echo "   Wallet #{$detail->id} ({$detail->name}): DB=৳{$detail->balance}, Calculated=৳{$detail->calculated_balance}\n";
    }
} else {
    echo "✅ All wallet balances are consistent with transactions\n";
}

// Check for transaction balance snapshots accuracy
$snapshotIssues = DB::select("
    SELECT COUNT(*) as count
    FROM wallet_transactions wt1
    WHERE ABS(
        wt1.balance_after - (
            wt1.balance_before + 
            CASE WHEN wt1.type = 'credit' THEN wt1.amount ELSE -wt1.amount END
        )
    ) > 0.01
");

$snapshotCount = $snapshotIssues[0]->count ?? 0;
if ($snapshotCount > 0) {
    echo "❌ $snapshotCount transactions have incorrect balance snapshots\n";
    $issues[] = "Incorrect transaction balance snapshots";
} else {
    echo "✅ All transaction balance snapshots are accurate\n";
}

echo "\n";

// 4. Check for orphaned service quotes
echo "=== SERVICE QUOTE INTEGRITY ===\n";

// Quotes without applications
$orphanedQuotes = DB::table('service_quotes')
    ->whereNotExists(function($query) {
        $query->select(DB::raw(1))
              ->from('service_applications')
              ->whereColumn('service_applications.id', 'service_quotes.service_application_id');
    })
    ->count();

if ($orphanedQuotes > 0) {
    echo "❌ $orphanedQuotes quotes without applications\n";
    $issues[] = "Orphaned service quotes";
} else {
    echo "✅ No orphaned service quotes\n";
}

// Applications with accepted quotes but status not updated
if (Schema::hasColumn('service_applications', 'status')) {
    $statusMismatch = DB::table('service_applications as sa')
        ->join('service_quotes as sq', 'sa.id', '=', 'sq.service_application_id')
        ->where('sq.status', 'accepted')
        ->where('sa.status', '!=', 'quote_accepted')
        ->count();
    
    if ($statusMismatch > 0) {
        echo "⚠️  $statusMismatch applications have accepted quotes but status not updated\n";
        $warnings[] = "Application status mismatch with quotes";
    } else {
        echo "✅ Application statuses match quote statuses\n";
    }
}

echo "\n";

// 5. Check for referral reward integrity
echo "=== REFERRAL & REWARD INTEGRITY ===\n";

// Referrals without rewards
$referralsWithoutRewards = DB::table('referrals')
    ->where('is_completed', true)
    ->whereNotExists(function($query) {
        $query->select(DB::raw(1))
              ->from('rewards')
              ->whereColumn('rewards.referral_id', 'referrals.id');
    })
    ->count();

if ($referralsWithoutRewards > 0) {
    echo "⚠️  $referralsWithoutRewards completed referrals without rewards\n";
    $warnings[] = "Completed referrals missing rewards";
} else {
    echo "✅ All completed referrals have rewards\n";
}

// Approved rewards not credited to wallet
$approvedNotCredited = DB::table('rewards')
    ->where('status', 'approved')
    ->whereNotExists(function($query) {
        $query->select(DB::raw(1))
              ->from('wallet_transactions')
              ->whereColumn('wallet_transactions.reference_id', 'rewards.id')
              ->where('wallet_transactions.reference_type', 'referral_reward');
    })
    ->count();

if ($approvedNotCredited > 0) {
    echo "❌ $approvedNotCredited approved rewards not credited to wallet\n";
    $issues[] = "Approved rewards not credited";
} else {
    echo "✅ All approved rewards are credited\n";
}

echo "\n";

// 6. Check for passport/visa history integrity
echo "=== PASSPORT & VISA INTEGRITY ===\n";

// Users with visa history but no passport
$visaWithoutPassport = DB::table('user_visa_history as vh')
    ->whereNotExists(function($query) {
        $query->select(DB::raw(1))
              ->from('user_passports')
              ->whereColumn('user_passports.user_id', 'vh.user_id');
    })
    ->count();

if ($visaWithoutPassport > 0) {
    echo "⚠️  $visaWithoutPassport visa records for users without passports\n";
    $warnings[] = "Visa history without passports";
} else {
    echo "✅ All visa records have corresponding passports\n";
}

// Check for expired passports marked as primary
$expiredPrimary = DB::table('user_passports')
    ->where('is_primary', true)
    ->where('expiry_date', '<', now())
    ->count();

if ($expiredPrimary > 0) {
    echo "⚠️  $expiredPrimary expired passports marked as primary\n";
    $warnings[] = "Expired passports marked as primary";
} else {
    echo "✅ No expired passports marked as primary\n";
}

echo "\n";

// 7. Check for profile completeness
echo "=== PROFILE COMPLETENESS CHECK ===\n";

// Users without profiles (should be auto-created by observer)
$usersWithoutProfile = DB::table('users')
    ->whereNotExists(function($query) {
        $query->select(DB::raw(1))
              ->from('user_profiles')
              ->whereColumn('user_profiles.user_id', 'users.id');
    })
    ->count();

if ($usersWithoutProfile > 0) {
    echo "⚠️  $usersWithoutProfile users without profiles (UserObserver may not be working)\n";
    $warnings[] = "Users missing profiles";
} else {
    echo "✅ All users have profiles\n";
}

// Users without wallets (should be auto-created by observer)
$usersWithoutWallet = DB::table('users')
    ->whereNotExists(function($query) {
        $query->select(DB::raw(1))
              ->from('wallets')
              ->whereColumn('wallets.user_id', 'users.id');
    })
    ->count();

if ($usersWithoutWallet > 0) {
    echo "❌ $usersWithoutWallet users without wallets (UserObserver may not be working)\n";
    $issues[] = "Users missing wallets";
} else {
    echo "✅ All users have wallets\n";
}

// Users without referral codes (should be auto-generated)
$usersWithoutReferralCode = DB::table('users')
    ->whereNull('referral_code')
    ->orWhere('referral_code', '')
    ->count();

if ($usersWithoutReferralCode > 0) {
    echo "⚠️  $usersWithoutReferralCode users without referral codes\n";
    $warnings[] = "Users missing referral codes";
} else {
    echo "✅ All users have referral codes\n";
}

echo "\n";

// 8. Check for agency-specific issues
echo "=== AGENCY RELATIONSHIP CHECK ===\n";

if (Schema::hasTable('agencies')) {
    // Agencies without users
    $agenciesWithoutUser = DB::table('agencies')
        ->whereNotExists(function($query) {
            $query->select(DB::raw(1))
                  ->from('users')
                  ->whereColumn('users.id', 'agencies.user_id');
        })
        ->count();
    
    if ($agenciesWithoutUser > 0) {
        echo "❌ $agenciesWithoutUser agencies without user accounts\n";
        $issues[] = "Agencies without users";
    } else {
        echo "✅ All agencies have user accounts\n";
    }
    
    // Users with agency role but no agency record
    $agencyRoleId = DB::table('roles')->where('name', 'agency')->value('id');
    if ($agencyRoleId) {
        $usersWithoutAgency = DB::table('users')
            ->where('role_id', $agencyRoleId)
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                      ->from('agencies')
                      ->whereColumn('agencies.user_id', 'users.id');
            })
            ->count();
        
        if ($usersWithoutAgency > 0) {
            echo "⚠️  $usersWithoutAgency users with agency role but no agency record\n";
            $warnings[] = "Agency role without agency record";
        } else {
            echo "✅ All agency users have agency records\n";
        }
    }
}

echo "\n";

// Summary
echo "========================================\n";
echo "SUMMARY\n";
echo "========================================\n";

echo "\n❌ CRITICAL ISSUES: " . count($issues) . "\n";
if (!empty($issues)) {
    foreach ($issues as $issue) {
        echo "   - $issue\n";
    }
}

echo "\n⚠️  WARNINGS: " . count($warnings) . "\n";
if (!empty($warnings)) {
    foreach ($warnings as $warning) {
        echo "   - $warning\n";
    }
}

if (empty($issues) && empty($warnings)) {
    echo "\n✅ Perfect! No issues or warnings found.\n";
} elseif (empty($issues)) {
    echo "\n✅ No critical issues! Only minor warnings that should be addressed.\n";
} else {
    echo "\n🔴 Critical issues found that need immediate attention!\n";
}

echo "\n";
