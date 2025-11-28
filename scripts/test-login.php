<?php

// Quick test script to verify database and authentication
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== BGPlatform Fresh Login Test ===\n\n";

// Test database connection
try {
    $userCount = User::count();
    echo "✓ Database connected: {$userCount} users found\n\n";
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
    exit(1);
}

// Test user credentials
$testEmails = [
    'admin@bgplatform.com',
    'agency@bgplatform.com',
    'consultant@bgplatform.com',
    'user@bgplatform.com',
];

echo "Testing credentials (password: 'password'):\n";
echo str_repeat('-', 70) . "\n";

foreach ($testEmails as $email) {
    $user = User::where('email', $email)->with('role')->first();
    
    if ($user) {
        $passwordValid = Hash::check('password', $user->password);
        $status = $passwordValid ? '✓ VALID' : '✗ INVALID';
        $role = $user->role ? $user->role->name : 'No role';
        $verified = $user->email_verified_at ? 'Verified' : 'Not verified';
        echo "{$status} | {$email} | Role: {$role} | {$verified}\n";
    } else {
        echo "✗ NOT FOUND | {$email}\n";
    }
}

echo "\n" . str_repeat('-', 70) . "\n";
echo "Use these credentials to login at: http://127.0.0.1:8001/login\n";
