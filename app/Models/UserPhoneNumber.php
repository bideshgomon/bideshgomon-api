<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPhoneNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone_number',
        'phone_type',
        'is_primary',
        'is_verified',
        'verified_at',
        'country_code',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    protected $appends = ['label'];

    /**
     * Get the user that owns the phone number.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the full phone number with country code.
     */
    public function getFullPhoneNumberAttribute(): string
    {
        return $this->country_code . ' ' . $this->phone_number;
    }

    /**
     * Get formatted phone type.
     */
    public function getFormattedTypeAttribute(): string
    {
        return ucfirst($this->phone_type);
    }

    /**
     * Get label for frontend (same as phone_type but capitalized).
     */
    public function getLabelAttribute(): string
    {
        return ucfirst($this->phone_type);
    }

    /**
     * Phone type options.
     */
    public static function phoneTypes(): array
    {
        return [
            'mobile' => 'Mobile',
            'home' => 'Home',
            'work' => 'Work',
            'whatsapp' => 'WhatsApp',
        ];
    }
}
