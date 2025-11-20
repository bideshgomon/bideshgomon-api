<?php

namespace App\Observers;

use App\Models\User;
use App\Services\ReferralService;
use App\Services\WalletService;

class UserObserver
{
    protected WalletService $walletService;
    protected ReferralService $referralService;

    public function __construct(WalletService $walletService, ReferralService $referralService)
    {
        $this->walletService = $walletService;
        $this->referralService = $referralService;
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Auto-create wallet for new users
        $this->walletService->createWallet($user);
        
        // Generate referral code
        $this->referralService->generateReferralCode($user);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
