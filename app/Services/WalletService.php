<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    /**
     * Create a wallet for a user.
     */
    public function createWallet(User $user): Wallet
    {
        return $user->wallet()->create([
            'balance' => 0.00,
            'currency' => 'BDT',
            'status' => 'active',
        ]);
    }

    /**
     * Credit a wallet with an amount.
     */
    public function creditWallet(
        Wallet $wallet,
        float $amount,
        string $description,
        ?string $referenceType = null,
        ?string $referenceId = null,
        ?array $metadata = null,
        ?int $processedBy = null
    ): WalletTransaction {
        return DB::transaction(function () use ($wallet, $amount, $description, $referenceType, $referenceId, $metadata, $processedBy) {
            if (!$wallet->isActive()) {
                throw new \Exception('Wallet is not active');
            }

            $transaction = $wallet->credit($amount, $description, $referenceType, $referenceId, $metadata);
            
            if ($processedBy) {
                $transaction->update(['processed_by' => $processedBy]);
            }

            return $transaction;
        });
    }

    /**
     * Debit a wallet with an amount.
     */
    public function debitWallet(
        Wallet $wallet,
        float $amount,
        string $description,
        ?string $referenceType = null,
        ?string $referenceId = null,
        ?array $metadata = null,
        ?int $processedBy = null
    ): WalletTransaction {
        return DB::transaction(function () use ($wallet, $amount, $description, $referenceType, $referenceId, $metadata, $processedBy) {
            if (!$wallet->isActive()) {
                throw new \Exception('Wallet is not active');
            }

            if (!$wallet->hasBalance($amount)) {
                throw new \Exception('Insufficient balance');
            }

            $transaction = $wallet->debit($amount, $description, $referenceType, $referenceId, $metadata);
            
            if ($processedBy) {
                $transaction->update(['processed_by' => $processedBy]);
            }

            return $transaction;
        });
    }

    /**
     * Get wallet balance for a user.
     */
    public function getBalance(User $user): float
    {
        $wallet = $user->wallet;
        
        if (!$wallet) {
            return 0.00;
        }

        return (float) $wallet->balance;
    }

    /**
     * Get transaction history for a user.
     */
    public function getTransactionHistory(User $user, int $perPage = 15)
    {
        $wallet = $user->wallet;
        
        if (!$wallet) {
            return collect();
        }

        return $wallet->transactions()->paginate($perPage);
    }

    /**
     * Check if user can debit a specific amount.
     */
    public function canDebit(User $user, float $amount): bool
    {
        $wallet = $user->wallet;
        
        if (!$wallet || !$wallet->isActive()) {
            return false;
        }

        return $wallet->hasBalance($amount);
    }

    /**
     * Reverse a transaction.
     */
    public function reverseTransaction(WalletTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $transaction->reverse();
        });
    }

    /**
     * Suspend a wallet.
     */
    public function suspendWallet(Wallet $wallet): void
    {
        $wallet->suspend();
    }

    /**
     * Activate a wallet.
     */
    public function activateWallet(Wallet $wallet): void
    {
        $wallet->activate();
    }

    /**
     * Get wallet statistics.
     */
    public function getWalletStats(): array
    {
        return [
            'total_wallets' => Wallet::count(),
            'active_wallets' => Wallet::where('status', 'active')->count(),
            'total_balance' => Wallet::sum('balance'),
            'total_transactions' => WalletTransaction::count(),
            'total_credits' => WalletTransaction::where('type', 'credit')->sum('amount'),
            'total_debits' => WalletTransaction::where('type', 'debit')->sum('amount'),
        ];
    }
}
