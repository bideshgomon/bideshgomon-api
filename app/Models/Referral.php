<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_id',
        'referred_id',
        'referral_code',
        'status',
        'reward_amount',
        'reward_paid',
        'reward_paid_at',
    ];

    protected $casts = [
        'reward_amount' => 'decimal:2',
        'reward_paid' => 'boolean',
        'reward_paid_at' => 'datetime',
    ];

    /**
     * Get the user who made the referral.
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    /**
     * Get the user who was referred.
     */
    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    /**
     * Mark referral as completed.
     */
    public function complete(): void
    {
        $this->update(['status' => 'completed']);
    }

    /**
     * Mark referral as cancelled.
     */
    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    /**
     * Mark reward as paid.
     */
    public function markRewardPaid(float $amount): void
    {
        $this->update([
            'reward_amount' => $amount,
            'reward_paid' => true,
            'reward_paid_at' => now(),
        ]);
    }

    /**
     * Check if referral is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if referral is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
