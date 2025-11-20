<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Role;

echo "\n=== TESTING ROLE RELATIONSHIP ===\n\n";

$admin = User::find(1);
echo "User: {$admin->name}\n";
echo "Role ID: {$admin->role_id}\n";

// Test direct query
$roleFromQuery = Role::find($admin->role_id);
echo "Direct query - Role slug: " . ($roleFromQuery ? $roleFromQuery->slug : 'NULL') . "\n";

// Test relationship
$admin->load('role');
echo "Relationship loaded\n";
echo "Role object type: " . gettype($admin->role) . "\n";

if (is_object($admin->role)) {
    echo "Role slug from relationship: {$admin->role->slug}\n";
} else {
    echo "ERROR: Role is not an object: " . var_export($admin->role, true) . "\n";
}

echo "\n=== TESTING MIDDLEWARE LOGIC ===\n";
$roleSlug = strtolower($admin->role->slug ?? 'none');
echo "Role slug (lowercase): {$roleSlug}\n";
echo "Is admin? " . ($roleSlug === 'admin' ? 'YES' : 'NO') . "\n";

echo "\n";
