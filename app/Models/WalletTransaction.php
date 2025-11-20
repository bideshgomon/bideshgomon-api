<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'transaction_type',
        'amount',
        'balance_before',
        'balance_after',
        'description',
        'reference_type',
        'reference_id',
        'status',
        'processed_by',
        'processed_at',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the wallet that owns the transaction.
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the admin who processed the transaction.
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Mark transaction as completed.
     */
    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'processed_at' => now(),
        ]);
    }

    /**
     * Mark transaction as failed.
     */
    public function fail(string $reason): void
    {
        $this->update([
            'status' => 'failed',
            'metadata' => array_merge($this->metadata ?? [], ['failure_reason' => $reason]),
        ]);
    }

    /**
     * Reverse the transaction.
     */
    public function reverse(): void
    {
        if ($this->status === 'reversed') {
            throw new \Exception('Transaction already reversed');
        }

        $wallet = $this->wallet;
        
        // Reverse the transaction effect
        if ($this->transaction_type === 'credit') {
            $wallet->balance -= $this->amount;
        } else {
            $wallet->balance += $this->amount;
        }
        
        $wallet->save();

        $this->update([
            'status' => 'reversed',
            'metadata' => array_merge($this->metadata ?? [], [
                'reversed_at' => now()->toIso8601String(),
                'reversed_by' => auth()->id(),
            ]),
        ]);
    }

    /**
     * Check if transaction is credit.
     */
    public function isCredit(): bool
    {
        return $this->transaction_type === 'credit';
    }

    /**
     * Check if transaction is debit.
     */
    public function isDebit(): bool
    {
        return $this->transaction_type === 'debit';
    }

    /**
     * Get formatted amount with Bangladesh currency.
     */
    public function getFormattedAmountAttribute(): string
    {
        return format_bd_currency((float) $this->amount);
    }
}
