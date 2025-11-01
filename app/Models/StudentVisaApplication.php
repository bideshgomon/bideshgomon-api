<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentVisaApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'destination_country_id',
        'university_id',
        'course_id',
        'agency_id',
        'intended_intake_month',
        'intended_intake_year',
        'current_education_level',
        'english_proficiency_test',
        'english_proficiency_score',
        'status',
        'user_notes',
        'admin_notes',
        'application_reference',
    ];

    /**
     * Get the user (student applicant).
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
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the chosen university.
     */
    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    /**
     * Get the chosen course.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the assisting agency (optional).
     */
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    // Add document relationships later if needed
}
