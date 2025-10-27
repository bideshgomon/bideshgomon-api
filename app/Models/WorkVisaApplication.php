<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes; // Add if using soft deletes

class WorkVisaApplication extends Model
{
    use HasFactory;
    use SoftDeletes; // Add if using soft deletes

    protected $fillable = [
        'user_id',
        'destination_country_id',
        'job_category_id',
        'job_posting_id',
        'agency_id',
        'status',
        'user_notes',
        'admin_notes',
        'application_reference',
    ];

    /**
     * Get the user (applicant) that owns the application.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the destination country for the application.
     */
    public function destinationCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the related job category (optional).
     */
    public function jobCategory(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class);
    }

    /**
     * Get the related job posting (optional).
     */
    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    /**
     * Get the agency handling the application (optional).
     */
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    // You might add relationships for associated documents later
    // public function documents(): MorphMany // Or ManyToMany depending on structure
    // {
    //     // ... relationship definition
    // }
}