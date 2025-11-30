<?php

namespace App\Services;

use App\Models\Referral;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReferralService
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Generate a unique referral code for a user.
     */
    public function generateReferralCode(User $user): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (User::where('referral_code', $code)->exists());

        $user->update(['referral_code' => $code]);

        return $code;
    }

    /**
     * Track a referral when a new user signs up.
     */
    public function trackReferral(User $newUser, string $referralCode): ?Referral
    {
        $referrer = User::where('referral_code', $referralCode)->first();

        if (!$referrer || $referrer->id === $newUser->id) {
            return null;
        }

        // Update new user's referred_by
        $newUser->update(['referred_by' => $referrer->id]);

        // Create referral record
        $referral = Referral::create([
            'referrer_id' => $referrer->id,
            'referred_id' => $newUser->id,
            'referral_code' => $referralCode,
            'status' => 'pending',
        ]);

        // Create pending reward
        $this->createSignupReward($referrer, $referral);

        return $referral;
    }

    /**
     * Create a signup reward for the referrer.
     */
    protected function createSignupReward(User $referrer, Referral $referral): Reward
    {
        $rewardAmount = 100.00; // ৳100 signup bonus

        return Reward::create([
            'user_id' => $referrer->id,
            'reward_type' => 'referral_signup',
            'amount' => $rewardAmount,
            'description' => "Referral signup bonus for {$referral->referred->name}",
            'status' => 'pending',
            'referral_id' => $referral->id,
        ]);
    }

    /**
     * Approve a reward and credit wallet.
     */
    public function approveReward(Reward $reward, int $approvedBy): void
    {
        DB::transaction(function () use ($reward, $approvedBy) {
            // Approve the reward
            $reward->approve($approvedBy);

            // Credit user's wallet
            $wallet = $reward->user->wallet;
            if ($wallet) {
                $this->walletService->creditWallet(
                    $wallet,
                    (float) $reward->amount,
                    $reward->description,
                    'referral_reward',
                    $reward->id,
                    ['reward_type' => $reward->reward_type],
                    $approvedBy
                );
            }

            // Mark reward as paid
            $reward->markPaid();

            // Update referral
            if ($reward->referral) {
                $reward->referral->complete();
                $reward->referral->markRewardPaid((float) $reward->amount);
            }
        });
    }

    /**
     * Get referral statistics for a user.
     */
    public function getReferralStats(User $user): array
    {
        $totalReferrals = $user->referrals()->count();
        $completedReferrals = $user->referrals()->where('status', 'completed')->count();
        $pendingReferrals = $user->referrals()->where('status', 'pending')->count();
        // Calculate total earnings from rewards table where status is 'paid'
        $totalEarnings = $user->rewards()->where('status', 'paid')->sum('amount');
        $pendingRewards = $user->rewards()->where('status', 'pending')->sum('amount');

        return [
            'total_referrals' => $totalReferrals,
            'completed_referrals' => $completedReferrals,
            'pending_referrals' => $pendingReferrals,
            'total_earnings' => $totalEarnings,
            'pending_rewards' => $pendingRewards,
        ];
    }

    /**
     * Get all referrals for a user with pagination.
     */
    public function getUserReferrals(User $user, int $perPage = 15)
    {
        return $user->referrals()
            ->with(['referred:id,name,email,created_at'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get all rewards for a user with pagination.
     */
    public function getUserRewards(User $user, int $perPage = 15)
    {
        return $user->rewards()
            ->with(['referral.referred:id,name'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get global referral statistics (admin).
     */
    public function getGlobalStats(): array
    {
        return [
            'total_referrals' => Referral::count(),
            'completed_referrals' => Referral::where('status', 'completed')->count(),
            'pending_referrals' => Referral::where('status', 'pending')->count(),
            'total_rewards' => Reward::count(),
            'pending_rewards' => Reward::where('status', 'pending')->count(),
            'approved_rewards' => Reward::where('status', 'approved')->count(),
            'paid_rewards' => Reward::where('status', 'paid')->count(),
            'total_reward_amount' => Reward::where('status', 'paid')->sum('amount'),
        ];
    }
}
