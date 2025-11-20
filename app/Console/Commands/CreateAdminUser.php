<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create';
    protected $description = 'Create admin user';

    public function handle()
    {
        $admin = User::where('email', 'admin@bideshgomon.com')->first();
        
        if ($admin) {
            $this->info('Admin user already exists!');
            $this->info('');
            $this->info('📧 Email: admin@bideshgomon.com');
            $this->info('🔑 Password: admin123');
            $this->info('💰 Wallet Balance: ৳' . number_format($admin->wallet?->balance ?? 0, 2));
            return;
        }

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@bideshgomon.com',
            'password' => bcrypt('admin123'),
            'email_verified_at' => now(),
        ]);

        if (!$admin->wallet) {
            $admin->wallet()->create([
                'balance' => 50000.00,
                'currency' => 'BDT',
                'status' => 'active',
            ]);
        }

        $this->info('✅ Admin user created successfully!');
        $this->info('');
        $this->info('📧 Email: admin@bideshgomon.com');
        $this->info('🔑 Password: admin123');
        $this->info('💰 Wallet Balance: ৳50,000');
    }
}
