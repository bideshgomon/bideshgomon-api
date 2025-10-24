<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_category_id',
        'title',
        'slug',
        'description',
        'company_name',
        'location',
        'salary_range',
        'employment_type', // e.g., Full-time, Part-time
        'experience_level',
        'requirements',
        'responsibilities',
        'apply_url',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'requirements' => 'array',
        'responsibilities' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the category this job belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class, 'job_category_id');
    }
}