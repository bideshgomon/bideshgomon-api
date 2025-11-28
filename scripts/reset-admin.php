<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Role;

echo "Checking admin user...\n";

// Get admin role
$adminRole = Role::where('name', 'admin')->orWhere('slug', 'admin')->first();

if (!$adminRole) {
    echo "Error: Admin role not found in database!\n";
    exit(1);
}

$admin = User::where('email', 'admin@bideshgomon.com')->first();

if ($admin) {
    echo "Admin exists: {$admin->email}\n";
    echo "Updating password and role...\n";
    $admin->password = bcrypt('admin123');
    $admin->role_id = $adminRole->id;
    $admin->save();
    echo "Password and role updated successfully!\n";
} else {
    echo "Admin not found. Creating new admin user...\n";
    $admin = User::create([
        'name' => 'Admin User',
        'email' => 'admin@bideshgomon.com',
        'password' => bcrypt('admin123'),
        'role_id' => $adminRole->id,
        'email_verified_at' => now(),
    ]);
    
    if (!$admin->wallet) {
        $admin->wallet()->create([
            'balance' => 50000,
            'currency' => 'BDT',
            'status' => 'active',
        ]);
    }
    
    echo "Admin created successfully!\n";
}

echo "\n========================================\n";
echo "Login Credentials:\n";
echo "Email: admin@bideshgomon.com\n";
echo "Password: admin123\n";
echo "========================================\n";
