<?php

namespace Database\Seeders;

use App\Models\Referral;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReferralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        if ($users->count() < 3) {
            $this->command->info('Not enough users to create referrals. Need at least 3 users.');
            return;
        }

        // Clear existing test referrals
        Referral::truncate();

        $now = now();
        
        // Create referrals for leaderboard
        $referrals = [
            // User 1 has 5 referrals (should be #1)
            ['referrer_id' => 1, 'referred_id' => 2, 'referral_code' => 'REF001', 'status' => 'completed', 'reward_amount' => 500, 'reward_paid' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['referrer_id' => 1, 'referred_id' => 3, 'referral_code' => 'REF001', 'status' => 'completed', 'reward_amount' => 500, 'reward_paid' => 1, 'created_at' => $now, 'updated_at' => $now],
            
            // User 2 has 3 referrals (should be #2)
            ['referrer_id' => 2, 'referred_id' => 1, 'referral_code' => 'REF002', 'status' => 'completed', 'reward_amount' => 500, 'reward_paid' => 1, 'created_at' => $now, 'updated_at' => $now],
            
            // User 3 has 1 referral (should be #3)
            ['referrer_id' => 3, 'referred_id' => 1, 'referral_code' => 'REF003', 'status' => 'completed', 'reward_amount' => 500, 'reward_paid' => 1, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($referrals as $referral) {
            Referral::create($referral);
        }

        $this->command->info('Created ' . count($referrals) . ' test referrals for leaderboard');
    }
}
