<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'wallet_id',
        'transaction_id',
        'gateway',
        'gateway_transaction_id',
        'payment_reference_id',
        'amount',
        'currency',
        'gateway_fee',
        'net_amount',
        'status',
        'payment_method',
        'customer_name',
        'customer_email',
        'customer_phone',
        'product_name',
        'description',
        'gateway_response',
        'metadata',
        'callback_url',
        'redirect_url',
        'error_code',
        'error_message',
        'refund_amount',
        'refund_reference',
        'refunded_at',
        'paid_at',
        'failed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'gateway_response' => 'array',
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wallet associated with the transaction.
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Scope for filtering by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by gateway.
     */
    public function scopeByGateway($query, string $gateway)
    {
        return $query->where('gateway', $gateway);
    }

    /**
     * Scope for successful transactions.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for failed transactions.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for pending transactions.
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'processing']);
    }

    /**
     * Scope for refunded transactions.
     */
    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    /**
     * Mark transaction as paid.
     */
    public function markAsPaid(string $gatewayTransactionId = null, array $gatewayResponse = []): void
    {
        $this->update([
            'status' => 'completed',
            'gateway_transaction_id' => $gatewayTransactionId ?? $this->gateway_transaction_id,
            'gateway_response' => array_merge($this->gateway_response ?? [], $gatewayResponse),
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark transaction as failed.
     */
    public function markAsFailed(string $errorCode = null, string $errorMessage = null, array $gatewayResponse = []): void
    {
        $this->update([
            'status' => 'failed',
            'error_code' => $errorCode,
            'error_message' => $errorMessage,
            'gateway_response' => array_merge($this->gateway_response ?? [], $gatewayResponse),
            'failed_at' => now(),
        ]);
    }

    /**
     * Mark transaction as cancelled.
     */
    public function markAsCancelled(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'error_message' => $reason,
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Mark transaction as processing.
     */
    public function markAsProcessing(): void
    {
        $this->update([
            'status' => 'processing',
        ]);
    }

    /**
     * Mark transaction as refunded.
     */
    public function markAsRefunded(float $refundAmount, string $refundReference = null, array $gatewayResponse = []): void
    {
        $this->update([
            'status' => 'refunded',
            'refund_amount' => $refundAmount,
            'refund_reference' => $refundReference,
            'gateway_response' => array_merge($this->gateway_response ?? [], $gatewayResponse),
            'refunded_at' => now(),
        ]);
    }

    /**
     * Check if transaction is successful.
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if transaction is pending.
     */
    public function isPending(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Check if transaction has failed.
     */
    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if transaction is refunded.
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    /**
     * Check if transaction is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
