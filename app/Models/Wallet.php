<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'currency',
        'status',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    /**
     * Get the user that owns the wallet.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all transactions for the wallet.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class)->latest();
    }

    /**
     * Credit the wallet with an amount.
     */
    public function credit(float $amount, string $description, ?string $referenceType = null, ?string $referenceId = null, ?array $metadata = null): WalletTransaction
    {
        $balanceBefore = $this->balance;
        $this->balance += $amount;
        $balanceAfter = $this->balance;
        $this->save();

        return $this->transactions()->create([
            'type' => 'credit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'status' => 'completed',
            'processed_at' => now(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Debit the wallet with an amount.
     */
    public function debit(float $amount, string $description, ?string $referenceType = null, ?string $referenceId = null, ?array $metadata = null): WalletTransaction
    {
        if (!$this->hasBalance($amount)) {
            throw new \Exception('Insufficient balance');
        }

        $balanceBefore = $this->balance;
        $this->balance -= $amount;
        $balanceAfter = $this->balance;
        $this->save();

        return $this->transactions()->create([
            'type' => 'debit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'status' => 'completed',
            'processed_at' => now(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Check if wallet has sufficient balance.
     */
    public function hasBalance(float $amount): bool
    {
        return $this->balance >= $amount;
    }

    /**
     * Get formatted balance with Bangladesh currency.
     */
    public function getFormattedBalanceAttribute(): string
    {
        return format_bd_currency($this->balance);
    }

    /**
     * Check if wallet is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if wallet is suspended.
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Suspend the wallet.
     */
    public function suspend(): void
    {
        $this->update(['status' => 'suspended']);
    }

    /**
     * Activate the wallet.
     */
    public function activate(): void
    {
        $this->update(['status' => 'active']);
    }
}
