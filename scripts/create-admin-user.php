<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Create admin user
$admin = User::create([
    'name' => 'Admin User',
    'email' => 'admin@bideshgomon.com',
    'password' => Hash::make('password'),
    'email_verified_at' => now(),
    'role_id' => 1,
]);

echo "✅ Admin user created successfully!\n\n";
echo "Email: admin@bideshgomon.com\n";
echo "Password: password\n";
echo "Role ID: 1 (admin)\n";
echo "User ID: {$admin->id}\n";
