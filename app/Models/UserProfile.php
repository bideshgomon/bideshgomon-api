<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'dob',
        'nationality',
        'passport_number',
        'ai_analysis_results',

        // --- NEWLY ADDED ---
        'gender',
        'marital_status',
        'current_occupation',
        'bio',
        'present_address_line',
        'present_city',
        'present_country',
        'present_postal_code',
        'is_permanent_same_as_present',
        'permanent_address_line',
        'permanent_city',
        'permanent_country',
        'permanent_postal_code',
        'social_linkedin',
        'social_github',
        'social_website',
        'portfolio_link',
        'travel_purpose',
        'funding_source',
        'estimated_funds',
        'preferred_countries',
        'intended_intake',
        'has_dependents',
        'dependents_count',

        // Deprecated, but fillable for now
        'address_line_1',
        'address_line_2',
        'city',
        'country',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'dob' => 'date',
        'ai_analysis_results' => 'array',
        'preferred_countries' => 'array',
        'is_permanent_same_as_present' => 'boolean',
        'has_dependents' => 'boolean',
        'estimated_funds' => 'decimal:2',
    ];

    /**
     * Get the user this profile belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
