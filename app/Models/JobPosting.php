<?php namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
class JobPosting extends Model {
    use HasFactory;
    protected $table = 'job_postings';
    protected $fillable = [
        'job_category_id', 'country_id', 'title', 'company_name', 'location_city',
        'employment_type', 'description', 'responsibilities', 'qualifications',
        'skills_required', 'salary_min', 'salary_max', 'salary_currency',
        'salary_period', 'apply_url', 'posting_date', 'closing_date', 'status', 'is_featured',
    ];
    protected $casts = [
        'salary_min' => 'decimal:2', 'salary_max' => 'decimal:2',
        'posting_date' => 'date', 'closing_date' => 'date', 'is_featured' => 'boolean',
        'responsibilities' => 'array', 'qualifications' => 'array', 'skills_required' => 'array', // Assuming text/json from migration
    ];
    public function jobCategory(): BelongsTo { return $this->belongsTo(JobCategory::class); }
    public function country(): BelongsTo { return $this->belongsTo(Country::class); }
}