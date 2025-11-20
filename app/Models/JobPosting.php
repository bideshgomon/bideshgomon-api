<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class JobPosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'company_name',
        'company_logo',
        'company_description',
        'country_id',
        'city',
        'address',
        'job_type',
        'category',
        'description',
        'responsibilities',
        'requirements',
        'skills',
        'benefits',
        'salary_min',
        'salary_max',
        'salary_currency',
        'salary_period',
        'salary_negotiable',
        'positions_available',
        'experience_years',
        'experience_level',
        'education_level',
        'gender_preference',
        'age_min',
        'age_max',
        'application_fee',
        'application_deadline',
        'contact_email',
        'contact_phone',
        'is_featured',
        'is_active',
        'is_urgent',
        'posted_by',
        'published_at',
        'expires_at',
        'views',
        'applications_count',
    ];

    protected $casts = [
        'skills' => 'array',
        'benefits' => 'array',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'application_fee' => 'decimal:2',
        'salary_negotiable' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'is_urgent' => 'boolean',
        'published_at' => 'datetime',
        'application_deadline' => 'date',
        'expires_at' => 'datetime',
    ];

    // Relationships
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    // Scopes
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('application_deadline')
                  ->orWhere('application_deadline', '>=', now());
            });
    }

    public function scopeFeatured(Builder $query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeUrgent(Builder $query)
    {
        return $query->where('is_urgent', true);
    }

    public function scopeByCountry(Builder $query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    public function scopeByCategory(Builder $query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByJobType(Builder $query, $jobType)
    {
        return $query->where('job_type', $jobType);
    }

    public function scopeSearch(Builder $query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('company_name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Helper Methods
    public function incrementViews()
    {
        $this->increment('views');
    }

    public function incrementApplications()
    {
        $this->increment('applications_count');
    }

    public function isExpired()
    {
        return $this->application_deadline && $this->application_deadline < now();
    }

    public function isFree()
    {
        return $this->application_fee == 0;
    }

    public function getSalaryRangeAttribute()
    {
        $symbol = $this->salary_currency === 'BDT' ? '৳' : $this->salary_currency;
        
        if ($this->salary_max && $this->salary_max > $this->salary_min) {
            return "{$symbol}" . number_format($this->salary_min) . " - {$symbol}" . number_format($this->salary_max);
        }
        
        return "{$symbol}" . number_format($this->salary_min);
    }
}