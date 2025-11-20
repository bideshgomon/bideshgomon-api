<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'overall_score',
        'profile_completeness',
        'document_readiness',
        'visa_eligibility',
        'strengths',
        'weaknesses',
        'recommendations',
        'missing_documents',
        'visa_eligibility_breakdown',
        'risk_level',
        'risk_factors',
        'personal_info_score',
        'education_score',
        'work_experience_score',
        'language_proficiency_score',
        'financial_score',
        'travel_history_score',
        'passport_score',
        'recommended_visa_types',
        'eligible_countries',
        'ai_summary',
        'ai_metadata',
        'assessed_at',
        'assessment_version',
    ];

    protected $casts = [
        'overall_score' => 'decimal:2',
        'profile_completeness' => 'decimal:2',
        'document_readiness' => 'decimal:2',
        'visa_eligibility' => 'decimal:2',
        'strengths' => 'array',
        'weaknesses' => 'array',
        'recommendations' => 'array',
        'missing_documents' => 'array',
        'visa_eligibility_breakdown' => 'array',
        'risk_factors' => 'array',
        'personal_info_score' => 'decimal:2',
        'education_score' => 'decimal:2',
        'work_experience_score' => 'decimal:2',
        'language_proficiency_score' => 'decimal:2',
        'financial_score' => 'decimal:2',
        'travel_history_score' => 'decimal:2',
        'passport_score' => 'decimal:2',
        'recommended_visa_types' => 'array',
        'eligible_countries' => 'array',
        'ai_metadata' => 'array',
        'assessed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the assessment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get score color class based on score value.
     */
    public function getScoreColorAttribute(): string
    {
        if ($this->overall_score >= 80) {
            return 'green';
        } elseif ($this->overall_score >= 60) {
            return 'yellow';
        } elseif ($this->overall_score >= 40) {
            return 'orange';
        } else {
            return 'red';
        }
    }

    /**
     * Get risk badge color.
     */
    public function getRiskColorAttribute(): string
    {
        return match($this->risk_level) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'red',
            default => 'gray',
        };
    }

    /**
     * Check if assessment is recent (within last 7 days).
     */
    public function isRecent(): bool
    {
        return $this->assessed_at && $this->assessed_at->gt(now()->subDays(7));
    }

    /**
     * Check if assessment needs update.
     */
    public function needsUpdate(): bool
    {
        return !$this->assessed_at || $this->assessed_at->lt(now()->subDays(7));
    }
}
