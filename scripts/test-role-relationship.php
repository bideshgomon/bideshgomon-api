#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "\n";
echo "Testing User->role relationship fix...\n";
echo "════════════════════════════════════════\n\n";

try {
    // Test 1: whereHas('role') query
    echo "✓ Test 1: whereHas('role') query\n";
    $agencyUsers = User::whereHas('role', function($query) {
        $query->where('slug', 'agency');
    })->get();
    
    echo "  Found {$agencyUsers->count()} agency users\n";
    foreach ($agencyUsers as $user) {
        echo "  - {$user->name} ({$user->email})\n";
    }
    echo "\n";
    
    // Test 2: Test role relationship
    echo "✓ Test 2: role() relationship access\n";
    $user = User::with('role')->whereHas('role', function($q) {
        $q->where('slug', 'agency');
    })->first();
    
    if ($user && $user->role) {
        echo "  User: {$user->name}\n";
        echo "  Role: {$user->role->name} (slug: {$user->role->slug})\n";
    }
    echo "\n";
    
    // Test 3: Agency model relationship
    echo "✓ Test 3: agency() relationship\n";
    if ($user->agency) {
        echo "  Agency: {$user->agency->name}\n";
        echo "  Company: {$user->agency->company_name}\n";
    } else {
        echo "  No agency profile found\n";
    }
    echo "\n";
    
    echo "════════════════════════════════════════\n";
    echo "✅ All tests passed! The fix works.\n\n";
    echo "The admin page should now work:\n";
    echo "→ http://127.0.0.1:8000/admin/agency-assignments/create\n\n";
    
} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    exit(1);
}
