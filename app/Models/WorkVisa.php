<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WorkVisa extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'destination_country_id',
        'application_reference',
        'job_title',
        'job_category',
        'employer_name',
        'employer_country',
        'employment_type',
        'industry_sector',
        'offered_salary',
        'salary_currency',
        'salary_period',
        'has_job_offer',
        'intended_start_date',
        'contract_duration_months',
        'experience_level',
        'years_of_experience',
        'key_skills',
        'highest_qualification',
        'has_language_certificate',
        'language_certificate_type',
        'work_permit_type',
        'requires_sponsorship',
        'has_lmia_approval',
        'status',
        'user_notes',
        'admin_notes',
        'submitted_at',
        'processed_at',
    ];

    protected $casts = [
        'intended_start_date' => 'date',
        'has_job_offer' => 'boolean',
        'has_language_certificate' => 'boolean',
        'requires_sponsorship' => 'boolean',
        'has_lmia_approval' => 'boolean',
        'offered_salary' => 'decimal:2',
        'submitted_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($workVisa) {
            if (empty($workVisa->application_reference)) {
                $workVisa->application_reference = 'WV' . strtoupper(uniqid());
            }
        });
    }

    /**
     * Get the user who submitted the application.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the destination country.
     */
    public function destinationCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'destination_country_id');
    }

    /**
     * Get documents attached to this visa application.
     * TODO: Create WorkVisaDocument model or use generic VisaDocument
     */
    public function documents(): HasMany
    {
        return $this->hasMany(VisaDocument::class, 'visa_id')->where('visa_type', 'work');
    }

    /**
     * Get the service application for agency processing.
     */
    public function serviceApplication(): HasOne
    {
        return $this->hasOne(ServiceApplication::class, 'work_visa_id');
    }

    /**
     * Scope to get applications for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get applications by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Check if application can be edited by user.
     */
    public function canBeEditedByUser(): bool
    {
        return in_array($this->status, ['pending', 'submitted']);
    }

    /**
     * Check if application can be deleted by user.
     */
    public function canBeDeletedByUser(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Get formatted job info.
     */
    public function getJobInfoAttribute(): string
    {
        $parts = [];
        
        if ($this->job_title) {
            $parts[] = $this->job_title;
        }
        
        if ($this->employer_name) {
            $parts[] = 'at ' . $this->employer_name;
        }
        
        if ($this->job_category) {
            $parts[] = '(' . $this->job_category . ')';
        }
        
        return implode(' ', $parts) ?: 'Position details pending';
    }

    /**
     * Get formatted salary info.
     */
    public function getSalaryInfoAttribute(): string
    {
        if (!$this->offered_salary) {
            return 'Salary not disclosed';
        }
        
        $currency = $this->salary_currency ?? 'USD';
        $amount = number_format((float) $this->offered_salary, 2);
        $period = $this->salary_period ? '/' . $this->salary_period : '';
        
        return "{$currency} {$amount}{$period}";
    }

    /**
     * Get formatted experience info.
     */
    public function getExperienceInfoAttribute(): string
    {
        $parts = [];
        
        if ($this->years_of_experience) {
            $parts[] = $this->years_of_experience . ' years';
        }
        
        if ($this->experience_level) {
            $parts[] = '(' . $this->experience_level . ')';
        }
        
        return implode(' ', $parts) ?: 'Experience not specified';
    }
}
