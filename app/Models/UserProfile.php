<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone', // Added from User model
        'avatar', // Added from User model
        'dob',
        'nationality',
        'passport_number',
        'address_line_1',
        'address_line_2',
        'city',
        'country',
        'ai_analysis_results',
        'bio',
        'passport_expiry',
    ];

    protected $casts = [
        'dob' => 'date',
        'passport_expiry' => 'date',
        'ai_analysis_results' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}