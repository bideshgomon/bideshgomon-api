<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StudentVisa extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'destination_country_id',
        'application_reference',
        'education_level',
        'study_field',
        'institution_name',
        'course_name',
        'intended_start_date',
        'course_duration_months',
        'has_admission_letter',
        'has_ielts_toefl',
        'english_test_type',
        'english_test_score',
        'previous_education_gpa',
        'status',
        'user_notes',
        'admin_notes',
        'submitted_at',
        'processed_at',
    ];

    protected $casts = [
        'intended_start_date' => 'date',
        'has_admission_letter' => 'boolean',
        'has_ielts_toefl' => 'boolean',
        'english_test_score' => 'decimal:1',
        'previous_education_gpa' => 'decimal:2',
        'submitted_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($studentVisa) {
            if (empty($studentVisa->application_reference)) {
                $studentVisa->application_reference = 'SV' . strtoupper(uniqid());
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
     * TODO: Create StudentVisaDocument model
     */
    public function documents(): HasMany
    {
        // return $this->hasMany(StudentVisaDocument::class);
        return $this->hasMany(VisaDocument::class, 'visa_id')->where('visa_type', 'student');
    }

    /**
     * Get the service application for agency processing.
     */
    public function serviceApplication(): HasOne
    {
        return $this->hasOne(ServiceApplication::class, 'student_visa_id');
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
     * Get formatted education info.
     */
    public function getEducationInfoAttribute(): string
    {
        $parts = [];
        
        if ($this->education_level) {
            $parts[] = $this->education_level;
        }
        
        if ($this->study_field) {
            $parts[] = 'in ' . $this->study_field;
        }
        
        return implode(' ', $parts) ?: 'Not specified';
    }

    /**
     * Get formatted institution info.
     */
    public function getInstitutionInfoAttribute(): string
    {
        $parts = [];
        
        if ($this->course_name) {
            $parts[] = $this->course_name;
        }
        
        if ($this->institution_name) {
            $parts[] = 'at ' . $this->institution_name;
        }
        
        return implode(' ', $parts) ?: 'Institution pending';
    }
}
