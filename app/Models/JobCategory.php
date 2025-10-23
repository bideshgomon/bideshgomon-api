<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Import Str for slug generation

class JobCategory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'job_categories';

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically create/update the slug when the name changes
        static::saving(function ($category) {
            $category->slug = Str::slug($category->name);
        });
    }

    // Add relationships later if needed (e.g., to JobPostings)
    // public function jobPostings(): HasMany
    // {
    //     return $this->hasMany(JobPosting::class);
    // }
}