<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$users = User::with('role')->get();

echo "\n👥 DATABASE USERS (" . $users->count() . " total)\n";
echo str_repeat("=", 60) . "\n\n";

foreach ($users as $user) {
    $role = 'No Role';
    if ($user->role) {
        $role = is_object($user->role) ? $user->role->name : $user->role;
    }
    
    echo "✓ {$user->name}\n";
    echo "  📧 Email: {$user->email}\n";
    echo "  🔑 Password: password\n";
    echo "  👤 Role: {$role}\n";
    echo "\n";
}

echo str_repeat("=", 60) . "\n";
