<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class PhoneVerificationCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_phone_number_id',
        'code',
        'expires_at',
        'is_used',
        'used_at',
        'ip_address',
        'attempts',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
        'used_at' => 'datetime',
    ];

    /**
     * Get the phone number that owns the verification code.
     */
    public function phoneNumber(): BelongsTo
    {
        return $this->belongsTo(UserPhoneNumber::class, 'user_phone_number_id');
    }

    /**
     * Check if the code is valid (not expired and not used).
     */
    public function isValid(): bool
    {
        return !$this->is_used && $this->expires_at->isFuture();
    }

    /**
     * Check if the code is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Mark the code as used.
     */
    public function markAsUsed(): void
    {
        $this->update([
            'is_used' => true,
            'used_at' => now(),
        ]);
    }

    /**
     * Increment attempt counter.
     */
    public function incrementAttempts(): void
    {
        $this->increment('attempts');
    }

    /**
     * Generate a random 6-digit code.
     */
    public static function generateCode(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new verification code for a phone number.
     */
    public static function createForPhoneNumber(UserPhoneNumber $phoneNumber, int $expiryMinutes = 10): self
    {
        // Invalidate any existing codes for this phone number
        static::where('user_phone_number_id', $phoneNumber->id)
            ->where('is_used', false)
            ->update(['is_used' => true]);

        return static::create([
            'user_phone_number_id' => $phoneNumber->id,
            'code' => static::generateCode(),
            'expires_at' => now()->addMinutes($expiryMinutes),
            'ip_address' => request()->ip(),
        ]);
    }
}
