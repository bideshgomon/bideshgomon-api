<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reward extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reward_type',
        'amount',
        'description',
        'status',
        'referral_id',
        'approved_by',
        'approved_at',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the user who owns the reward.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the referral associated with this reward.
     */
    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class);
    }

    /**
     * Get the admin who approved the reward.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Approve the reward.
     */
    public function approve(int $approvedBy): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
    }

    /**
     * Reject the reward.
     */
    public function reject(int $rejectedBy, ?string $reason = null): void
    {
        $metadata = $this->metadata ?? [];
        $metadata['rejection_reason'] = $reason;
        $metadata['rejected_by'] = $rejectedBy;
        $metadata['rejected_at'] = now()->toIso8601String();

        $this->update([
            'status' => 'rejected',
            'metadata' => $metadata,
        ]);
    }

    /**
     * Mark reward as paid.
     */
    public function markPaid(): void
    {
        $this->update(['status' => 'paid']);
    }

    /**
     * Check if reward is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if reward is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if reward is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return format_bd_currency((float) $this->amount);
    }
}
