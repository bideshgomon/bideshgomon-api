<?php

/**
 * Fix Users Without Role - Assign Default 'user' Role
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Role;

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║           FIX USERS WITHOUT ROLE ASSIGNMENT                    ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// Get default user role
$userRole = Role::where('slug', 'user')->first();

if (!$userRole) {
    echo "❌ ERROR: 'user' role not found in database\n";
    echo "   Please run: php artisan db:seed --class=RolesSeeder\n\n";
    exit(1);
}

echo "✓ Found role: {$userRole->name} (ID: {$userRole->id})\n\n";

// Find users without role
$usersWithoutRole = User::whereNull('role_id')->get();

if ($usersWithoutRole->count() === 0) {
    echo "✅ All users already have roles assigned!\n\n";
    exit(0);
}

echo "Found {$usersWithoutRole->count()} users without role:\n";
echo str_repeat("─", 70) . "\n";

foreach ($usersWithoutRole as $user) {
    echo "  • {$user->name} ({$user->email})\n";
}

echo "\n";
echo "Assigning 'user' role to these users...\n";

$updated = User::whereNull('role_id')->update(['role_id' => $userRole->id]);

echo "✅ Updated {$updated} user(s) with default 'user' role\n\n";

// Verify
$remaining = User::whereNull('role_id')->count();
if ($remaining === 0) {
    echo "✅ SUCCESS: All users now have roles assigned\n\n";
} else {
    echo "⚠️  WARNING: {$remaining} users still without role\n\n";
}

// Show role distribution
echo "Role Distribution:\n";
echo str_repeat("─", 70) . "\n";

$roles = Role::withCount('users')->get();
foreach ($roles as $role) {
    echo sprintf("  %-15s : %d users\n", $role->name, $role->users_count);
}

echo "\n";
