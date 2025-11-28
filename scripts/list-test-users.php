<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TEST USERS SEEDED ===\n\n";

$users = \App\Models\User::with('role')->get();

foreach ($users as $user) {
    echo "📧 {$user->email}\n";
    echo "   Name: {$user->name}\n";
    echo "   Role: " . ($user->role ? $user->role->name : 'NO ROLE') . "\n";
    echo "   Password: password\n";
    echo "\n";
}

echo "Total Users: " . $users->count() . "\n";
