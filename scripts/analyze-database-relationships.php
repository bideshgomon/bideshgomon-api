<?php

/**
 * Database Relationship Analysis Script
 * Checks for orphaned records, missing foreign keys, and relationship integrity
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "========================================\n";
echo "DATABASE RELATIONSHIP ANALYSIS\n";
echo "========================================\n\n";

// 1. Check for orphaned records
echo "=== ORPHANED RECORDS CHECK ===\n";

$orphanChecks = [
    'wallets' => ['user_id', 'users'],
    'wallet_transactions' => ['wallet_id', 'wallets'],
    'referrals' => ['referrer_id', 'users'],
    'rewards' => ['user_id', 'users'],
    'service_applications' => ['user_id', 'users'],
    'service_quotes' => ['service_application_id', 'service_applications'],
    'user_profiles' => ['user_id', 'users'],
    'user_passports' => ['user_id', 'users'],
    'user_educations' => ['user_id', 'users'],
    'user_work_experiences' => ['user_id', 'users'],
    'user_languages' => ['user_id', 'users'],
    'user_visa_history' => ['user_id', 'users'],
    'user_travel_history' => ['user_id', 'users'],
    'user_family_members' => ['user_id', 'users'],
    'user_financial_information' => ['user_id', 'users'],
    'user_security_information' => ['user_id', 'users'],
    'user_documents' => ['user_id', 'users'],
    'agencies' => ['user_id', 'users'],
    'job_applications' => ['user_id', 'users'],
    'support_tickets' => ['user_id', 'users'],
    'appointments' => ['user_id', 'users'],
];

$issues = [];

foreach ($orphanChecks as $table => $config) {
    list($foreignKey, $parentTable) = $config;
    
    if (!Schema::hasTable($table)) {
        echo "⚠️  Table '$table' does not exist\n";
        continue;
    }
    
    $orphanedCount = DB::table($table)
        ->whereNotExists(function($query) use ($parentTable, $table, $foreignKey) {
            $query->select(DB::raw(1))
                  ->from($parentTable)
                  ->whereColumn("$parentTable.id", "$table.$foreignKey");
        })
        ->count();
    
    if ($orphanedCount > 0) {
        echo "❌ $table: $orphanedCount orphaned records (missing $parentTable)\n";
        $issues[] = "Orphaned records in $table";
    } else {
        echo "✅ $table: No orphaned records\n";
    }
}

echo "\n";

// 2. Check for missing foreign key indexes
echo "=== MISSING INDEXES CHECK ===\n";

$tablesNeedingIndexes = [];

foreach ($orphanChecks as $table => $config) {
    list($foreignKey, $parentTable) = $config;
    
    if (!Schema::hasTable($table)) {
        continue;
    }
    
    // Check if index exists
    $indexes = DB::select("PRAGMA index_list($table)");
    $hasIndex = false;
    
    foreach ($indexes as $index) {
        $indexInfo = DB::select("PRAGMA index_info({$index->name})");
        foreach ($indexInfo as $col) {
            if ($col->name === $foreignKey) {
                $hasIndex = true;
                break 2;
            }
        }
    }
    
    if (!$hasIndex) {
        echo "⚠️  $table.$foreignKey has no index\n";
        $tablesNeedingIndexes[] = "$table.$foreignKey";
    }
}

if (empty($tablesNeedingIndexes)) {
    echo "✅ All foreign keys have indexes\n";
}

echo "\n";

// 3. Check for referential integrity issues
echo "=== REFERENTIAL INTEGRITY CHECK ===\n";

// Users with role_id pointing to non-existent roles
$usersWithInvalidRole = DB::table('users')
    ->whereNotNull('role_id')
    ->whereNotExists(function($query) {
        $query->select(DB::raw(1))
              ->from('roles')
              ->whereColumn('roles.id', 'users.role_id');
    })
    ->count();

if ($usersWithInvalidRole > 0) {
    echo "❌ $usersWithInvalidRole users with invalid role_id\n";
    $issues[] = "Users with invalid role_id";
} else {
    echo "✅ All users have valid roles\n";
}

// Check for self-referential issues in users (referred_by)
$usersWithInvalidReferrer = DB::table('users')
    ->whereNotNull('referred_by')
    ->whereNotExists(function($query) {
        $query->select(DB::raw(1))
              ->from('users as u2')
              ->whereColumn('u2.id', 'users.referred_by');
    })
    ->count();

if ($usersWithInvalidReferrer > 0) {
    echo "❌ $usersWithInvalidReferrer users with invalid referred_by\n";
    $issues[] = "Users with invalid referrer";
} else {
    echo "✅ All referrals point to valid users\n";
}

// Check wallet transactions without wallet
$transactionsWithoutWallet = DB::table('wallet_transactions')
    ->whereNotExists(function($query) {
        $query->select(DB::raw(1))
              ->from('wallets')
              ->whereColumn('wallets.id', 'wallet_transactions.wallet_id');
    })
    ->count();

if ($transactionsWithoutWallet > 0) {
    echo "❌ $transactionsWithoutWallet wallet transactions without wallet\n";
    $issues[] = "Wallet transactions without wallet";
} else {
    echo "✅ All wallet transactions have valid wallets\n";
}

// Check service applications with invalid service_module_id
if (Schema::hasTable('service_modules')) {
    $applicationsWithInvalidModule = DB::table('service_applications')
        ->whereNotNull('service_module_id')
        ->whereNotExists(function($query) {
            $query->select(DB::raw(1))
                  ->from('service_modules')
                  ->whereColumn('service_modules.id', 'service_applications.service_module_id');
        })
        ->count();

    if ($applicationsWithInvalidModule > 0) {
        echo "❌ $applicationsWithInvalidModule applications with invalid service_module_id\n";
        $issues[] = "Applications with invalid service module";
    } else {
        echo "✅ All applications have valid service modules\n";
    }
}

echo "\n";

// 4. Check for duplicate relationships
echo "=== DUPLICATE RELATIONSHIP CHECK ===\n";

// Users with multiple primary passports
$usersWithMultiplePrimary = DB::table('user_passports')
    ->select('user_id', DB::raw('COUNT(*) as count'))
    ->where('is_primary', true)
    ->groupBy('user_id')
    ->having('count', '>', 1)
    ->count();

if ($usersWithMultiplePrimary > 0) {
    echo "❌ $usersWithMultiplePrimary users with multiple primary passports\n";
    $issues[] = "Multiple primary passports";
} else {
    echo "✅ No duplicate primary passports\n";
}

// Users with multiple wallets
$usersWithMultipleWallets = DB::table('wallets')
    ->select('user_id', DB::raw('COUNT(*) as count'))
    ->groupBy('user_id')
    ->having('count', '>', 1)
    ->count();

if ($usersWithMultipleWallets > 0) {
    echo "❌ $usersWithMultipleWallets users with multiple wallets\n";
    $issues[] = "Multiple wallets per user";
} else {
    echo "✅ No duplicate wallets\n";
}

// Users with multiple profiles
$usersWithMultipleProfiles = DB::table('user_profiles')
    ->select('user_id', DB::raw('COUNT(*) as count'))
    ->groupBy('user_id')
    ->having('count', '>', 1)
    ->count();

if ($usersWithMultipleProfiles > 0) {
    echo "❌ $usersWithMultipleProfiles users with multiple profiles\n";
    $issues[] = "Multiple profiles per user";
} else {
    echo "✅ No duplicate profiles\n";
}

echo "\n";

// 5. Check cascade delete setup
echo "=== CASCADE DELETE VERIFICATION ===\n";

$criticalTables = [
    'wallets' => 'Should cascade delete wallet_transactions',
    'users' => 'Should cascade delete user_* tables',
    'service_applications' => 'Should cascade delete quotes and documents',
];

// Since SQLite stores foreign keys differently, we check pragma
foreach ($criticalTables as $table => $description) {
    if (!Schema::hasTable($table)) {
        continue;
    }
    
    $foreignKeys = DB::select("PRAGMA foreign_key_list($table)");
    echo "ℹ️  $table: " . count($foreignKeys) . " foreign key constraints\n";
}

echo "\n";

// 6. Model vs Database mismatch check
echo "=== MODEL RELATIONSHIP VERIFICATION ===\n";

$modelChecks = [
    'User' => [
        'wallet' => ['hasOne', 'Wallet'],
        'profile' => ['hasOne', 'UserProfile'],
        'passports' => ['hasMany', 'UserPassport'],
        'referrals' => ['hasMany', 'Referral'],
        'rewards' => ['hasMany', 'Reward'],
    ],
    'Wallet' => [
        'user' => ['belongsTo', 'User'],
        'transactions' => ['hasMany', 'WalletTransaction'],
    ],
    'ServiceApplication' => [
        'user' => ['belongsTo', 'User'],
        'serviceModule' => ['belongsTo', 'ServiceModule'],
        'quotes' => ['hasMany', 'ServiceQuote'],
    ],
];

foreach ($modelChecks as $model => $relationships) {
    $className = "App\\Models\\$model";
    if (!class_exists($className)) {
        echo "⚠️  Model $model does not exist\n";
        continue;
    }
    
    $instance = new $className;
    echo "✅ Model $model exists with " . count($relationships) . " relationships\n";
    
    foreach ($relationships as $method => $config) {
        if (!method_exists($instance, $method)) {
            echo "   ❌ Missing method: $method\n";
            $issues[] = "$model missing $method relationship";
        }
    }
}

echo "\n";

// 7. Summary
echo "========================================\n";
echo "SUMMARY\n";
echo "========================================\n";

if (empty($issues)) {
    echo "✅ No critical issues found!\n";
} else {
    echo "❌ Issues found:\n";
    foreach ($issues as $issue) {
        echo "   - $issue\n";
    }
}

echo "\nTotal issues: " . count($issues) . "\n";

if (!empty($tablesNeedingIndexes)) {
    echo "\n⚠️  Performance recommendations:\n";
    echo "   Add indexes to: " . implode(', ', $tablesNeedingIndexes) . "\n";
}

echo "\n";
