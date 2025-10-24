<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobPosting extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'job_postings';

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'job_category_id',
        'country_id',
        'title',
        'company_name',
        'location_city',
        'employment_type',
        'description',
        'responsibilities',
        'qualifications',
        'skills_required',
        'salary_min',
        'salary_max',
        'salary_currency',
        'salary_period',
        'apply_url',
        'posting_date',
        'closing_date',
        'status',
        'is_featured',
    ];

    /**
     * The attributes that should be cast.
     * @var array
     */
    protected $casts = [
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'posting_date' => 'date',
        'closing_date' => 'date',
        'is_featured' => 'boolean',
        // 'skills_required' => 'array', // If you store skills as JSON
    ];

    /**
     * Get the job category this posting belongs to.
     */
    public function jobCategory(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class);
    }

    /**
     * Get the country associated with this job posting.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}