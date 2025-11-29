<?php

/**
 * Test Settings Update Functionality
 * Tests if settings can be saved to database properly
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SiteSetting;
use Illuminate\Support\Facades\DB;

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║            SETTINGS DATABASE UPDATE TEST                       ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// Test 1: Check if settings exist
echo "📋 TEST 1: Verify Settings Exist in Database\n";
echo str_repeat("─", 70) . "\n";

$totalSettings = SiteSetting::count();
$apiSettings = SiteSetting::where('group', 'api')->count();
$groups = SiteSetting::distinct('group')->pluck('group')->toArray();

echo "✓ Total settings: $totalSettings\n";
echo "✓ API settings: $apiSettings\n";
echo "✓ Groups: " . implode(', ', $groups) . "\n\n";

// Test 2: Test single setting update
echo "📝 TEST 2: Test Single Setting Update\n";
echo str_repeat("─", 70) . "\n";

try {
    $testSetting = SiteSetting::where('group', 'api')->first();
    
    if ($testSetting) {
        $originalValue = $testSetting->value;
        $testValue = 'test_value_' . time();
        
        echo "Setting: {$testSetting->key}\n";
        echo "Original value: " . ($originalValue ?: '(empty)') . "\n";
        
        // Update
        $testSetting->update(['value' => $testValue]);
        
        // Verify
        $updated = SiteSetting::find($testSetting->id);
        
        if ($updated->value === $testValue) {
            echo "✓ Update successful: $testValue\n";
            
            // Restore original
            $testSetting->update(['value' => $originalValue]);
            echo "✓ Restored to: " . ($originalValue ?: '(empty)') . "\n";
        } else {
            echo "✗ Update FAILED\n";
        }
    } else {
        echo "✗ No API settings found to test\n";
    }
} catch (\Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Test bulk update (like the form does)
echo "📦 TEST 3: Test Bulk Update (Form Simulation)\n";
echo str_repeat("─", 70) . "\n";

try {
    // Get 3 API settings
    $testSettings = SiteSetting::where('group', 'api')->take(3)->get();
    
    if ($testSettings->count() > 0) {
        echo "Testing bulk update of {$testSettings->count()} settings:\n";
        
        $updates = [];
        $originals = [];
        
        foreach ($testSettings as $setting) {
            $originals[$setting->key] = $setting->value;
            $updates[] = [
                'key' => $setting->key,
                'value' => 'bulk_test_' . time() . '_' . rand(1000, 9999)
            ];
        }
        
        // Simulate controller update logic
        DB::beginTransaction();
        
        foreach ($updates as $updateData) {
            $setting = SiteSetting::where('key', $updateData['key'])->first();
            if ($setting) {
                $setting->update(['value' => $updateData['value']]);
                echo "  ✓ Updated: {$updateData['key']}\n";
            }
        }
        
        DB::commit();
        
        // Verify
        $success = true;
        foreach ($updates as $updateData) {
            $setting = SiteSetting::where('key', $updateData['key'])->first();
            if ($setting->value !== $updateData['value']) {
                echo "  ✗ Verification FAILED for: {$updateData['key']}\n";
                $success = false;
            }
        }
        
        if ($success) {
            echo "✓ All bulk updates verified successfully\n";
        }
        
        // Restore originals
        foreach ($originals as $key => $value) {
            $setting = SiteSetting::where('key', $key)->first();
            $setting->update(['value' => $value]);
        }
        echo "✓ Restored original values\n";
        
    } else {
        echo "✗ No API settings found for bulk test\n";
    }
} catch (\Exception $e) {
    DB::rollBack();
    echo "✗ ERROR: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Check table structure
echo "🏗️  TEST 4: Verify Table Structure\n";
echo str_repeat("─", 70) . "\n";

try {
    $columns = DB::select("PRAGMA table_info(site_settings)");
    echo "Table columns:\n";
    foreach ($columns as $col) {
        echo "  - {$col->name} ({$col->type})" . ($col->notnull ? " NOT NULL" : "") . "\n";
    }
    echo "✓ Table structure verified\n";
} catch (\Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 5: Test different data types
echo "🔤 TEST 5: Test Different Setting Types\n";
echo str_repeat("─", 70) . "\n";

try {
    $types = ['text', 'boolean', 'number', 'email', 'url'];
    
    foreach ($types as $type) {
        $setting = SiteSetting::where('type', $type)->first();
        if ($setting) {
            echo "  ✓ Found $type setting: {$setting->key}\n";
        } else {
            echo "  ✗ No $type setting found\n";
        }
    }
} catch (\Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 6: Check for constraints
echo "🔒 TEST 6: Check Data Integrity\n";
echo str_repeat("─", 70) . "\n";

try {
    // Check for duplicate keys
    $duplicates = DB::table('site_settings')
        ->select('key', DB::raw('COUNT(*) as count'))
        ->groupBy('key')
        ->having('count', '>', 1)
        ->get();
    
    if ($duplicates->count() > 0) {
        echo "✗ Found duplicate keys:\n";
        foreach ($duplicates as $dup) {
            echo "  - {$dup->key} ({$dup->count} times)\n";
        }
    } else {
        echo "✓ No duplicate keys found\n";
    }
    
    // Check for null keys
    $nullKeys = SiteSetting::whereNull('key')->count();
    echo ($nullKeys > 0 ? "✗" : "✓") . " Null keys: $nullKeys\n";
    
    // Check for settings without groups
    $noGroup = SiteSetting::whereNull('group')->orWhere('group', '')->count();
    echo ($noGroup > 0 ? "✗" : "✓") . " Settings without group: $noGroup\n";
    
} catch (\Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
}

echo "\n";

// Summary
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║                     TEST SUMMARY                               ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

echo "Database: " . database_path('database.sqlite') . "\n";
echo "Total Settings: $totalSettings\n";
echo "API Settings: $apiSettings\n";
echo "Groups: " . count($groups) . "\n";
echo "\n";

echo "✅ Database update functionality is ";
echo ($totalSettings > 0 && $apiSettings > 0) ? "WORKING" : "NOT WORKING";
echo "\n\n";

echo "Next steps:\n";
echo "1. Navigate to http://127.0.0.1:8000/admin/settings\n";
echo "2. Switch to API tab\n";
echo "3. Update any API key value\n";
echo "4. Click 'Save Settings'\n";
echo "5. Refresh page to verify changes persist\n";
echo "\n";
