<?php

namespace App\Http\Controllers;

use App\Services\ReferralService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReferralController extends Controller
{
    protected ReferralService $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * Display the referral dashboard.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        
        // Generate referral code if doesn't exist
        if (!$user->referral_code) {
            $this->referralService->generateReferralCode($user);
            $user->refresh();
        }

        $stats = $this->referralService->getReferralStats($user);
        $referrals = $this->referralService->getUserReferrals($user, 10);
        $rewards = $this->referralService->getUserRewards($user, 5);

        $referralLink = url('/register?ref=' . $user->referral_code);

        return Inertia::render('Referral/Index', [
            'referralCode' => $user->referral_code,
            'referralLink' => $referralLink,
            'stats' => $stats,
            'referrals' => $referrals,
            'recentRewards' => $rewards,
        ]);
    }

    /**
     * Display all referrals.
     */
    public function referrals(Request $request): Response
    {
        $user = $request->user();
        $referrals = $this->referralService->getUserReferrals($user, 20);

        return Inertia::render('Referral/Referrals', [
            'referrals' => $referrals,
        ]);
    }

    /**
     * Display all rewards.
     */
    public function rewards(Request $request): Response
    {
        $user = $request->user();
        $rewards = $this->referralService->getUserRewards($user, 20);

        return Inertia::render('Referral/Rewards', [
            'rewards' => $rewards,
        ]);
    }
}
