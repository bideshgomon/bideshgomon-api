<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Seeder;

class WalletTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates demo wallet transactions for testing
     */
    public function run(): void
    {
        // Get test user
        $user = User::where('email', 'john@test.com')->first();
        
        if (!$user) {
            echo "⚠️ Test user not found. Run ProfileManagementSeeder first.\n";
            return;
        }

        // Get or create wallet
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'balance' => 0,
                'currency' => 'BDT',
                'status' => 'active',
            ]
        );

        echo "💰 Creating wallet transactions for {$user->email}...\n";

        $transactions = [
            // Initial deposit
            [
                'wallet_id' => $wallet->id,
                'transaction_type' => 'credit',
                'amount' => 10000.00,
                'balance_before' => 0,
                'balance_after' => 10000.00,
                'status' => 'completed',
                'description' => 'Initial wallet funding via bKash',
                'reference_type' => 'payment',
                'reference_id' => 'BKS' . rand(100000, 999999),
                'created_at' => now()->subDays(30),
            ],
            // Service payment
            [
                'wallet_id' => $wallet->id,
                'transaction_type' => 'debit',
                'amount' => 1500.00,
                'balance_before' => 10000.00,
                'balance_after' => 8500.00,
                'status' => 'completed',
                'description' => 'Payment for Travel Insurance service',
                'reference_type' => 'service_payment',
                'reference_id' => 'SRV' . rand(100000, 999999),
                'created_at' => now()->subDays(28),
            ],
            // Referral bonus
            [
                'wallet_id' => $wallet->id,
                'transaction_type' => 'credit',
                'amount' => 500.00,
                'balance_before' => 8500.00,
                'balance_after' => 9000.00,
                'status' => 'completed',
                'description' => 'Referral bonus - Friend joined!',
                'reference_type' => 'referral_bonus',
                'reference_id' => 'REF' . rand(100000, 999999),
                'created_at' => now()->subDays(25),
            ],
            // Another deposit
            [
                'wallet_id' => $wallet->id,
                'transaction_type' => 'credit',
                'amount' => 5000.00,
                'balance_before' => 9000.00,
                'balance_after' => 14000.00,
                'status' => 'completed',
                'description' => 'Add funds via Nagad',
                'reference_type' => 'payment',
                'reference_id' => 'NGD' . rand(100000, 999999),
                'created_at' => now()->subDays(20),
            ],
            // Service payment
            [
                'wallet_id' => $wallet->id,
                'transaction_type' => 'debit',
                'amount' => 2000.00,
                'balance_before' => 14000.00,
                'balance_after' => 12000.00,
                'status' => 'completed',
                'description' => 'CV Template purchase - Professional Design',
                'reference_type' => 'service_payment',
                'reference_id' => 'CV' . rand(100000, 999999),
                'created_at' => now()->subDays(15),
            ],
            // Withdrawal
            [
                'wallet_id' => $wallet->id,
                'transaction_type' => 'debit',
                'amount' => 3000.00,
                'balance_before' => 12000.00,
                'balance_after' => 9000.00,
                'status' => 'completed',
                'description' => 'Withdrawal to Bank Account',
                'reference_type' => 'withdrawal',
                'reference_id' => 'WTH' . rand(100000, 999999),
                'created_at' => now()->subDays(12),
            ],
            // Reward
            [
                'wallet_id' => $wallet->id,
                'transaction_type' => 'credit',
                'amount' => 300.00,
                'balance_before' => 9000.00,
                'balance_after' => 9300.00,
                'status' => 'completed',
                'description' => 'Reward for completing profile',
                'reference_type' => 'reward',
                'reference_id' => 'RWD' . rand(100000, 999999),
                'created_at' => now()->subDays(10),
            ],
            // Deposit
            [
                'wallet_id' => $wallet->id,
                'transaction_type' => 'credit',
                'amount' => 7500.00,
                'balance_before' => 9300.00,
                'balance_after' => 16800.00,
                'status' => 'completed',
                'description' => 'Add funds via Rocket',
                'reference_type' => 'payment',
                'reference_id' => 'RKT' . rand(100000, 999999),
                'created_at' => now()->subDays(7),
            ],
            // Service payment
            [
                'wallet_id' => $wallet->id,
                'transaction_type' => 'debit',
                'amount' => 1200.00,
                'balance_before' => 16800.00,
                'balance_after' => 15600.00,
                'status' => 'completed',
                'description' => 'Job Application service fee',
                'reference_type' => 'service_payment',
                'reference_id' => 'JOB' . rand(100000, 999999),
                'created_at' => now()->subDays(5),
            ],
            // Pending transaction
            [
                'wallet_id' => $wallet->id,
                'transaction_type' => 'credit',
                'amount' => 2500.00,
                'balance_before' => 15600.00,
                'balance_after' => 15600.00, // Not added yet (pending)
                'status' => 'pending',
                'description' => 'Deposit via Bank Transfer (Processing)',
                'reference_type' => 'payment',
                'reference_id' => 'BNK' . rand(100000, 999999),
                'created_at' => now()->subDays(2),
            ],
            // Recent referral
            [
                'wallet_id' => $wallet->id,
                'transaction_type' => 'credit',
                'amount' => 500.00,
                'balance_before' => 15600.00,
                'balance_after' => 16100.00,
                'status' => 'completed',
                'description' => 'Referral bonus - 2nd level commission',
                'reference_type' => 'referral_bonus',
                'reference_id' => 'REF' . rand(100000, 999999),
                'created_at' => now()->subDays(1),
            ],
            // Today's transaction
            [
                'wallet_id' => $wallet->id,
                'transaction_type' => 'credit',
                'amount' => 3500.00,
                'balance_before' => 16100.00,
                'balance_after' => 19600.00,
                'status' => 'completed',
                'description' => 'Add funds via bKash',
                'reference_type' => 'payment',
                'reference_id' => 'BKS' . rand(100000, 999999),
                'created_at' => now()->subHours(3),
            ],
        ];

        foreach ($transactions as $transaction) {
            WalletTransaction::create($transaction);
        }

        // Update wallet balance to match last transaction
        $wallet->update(['balance' => 19600.00]);

        echo "✅ Created " . count($transactions) . " wallet transactions\n";
        echo "💵 Final balance: ৳ 19,600.00\n";
    }
}
