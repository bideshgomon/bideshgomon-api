<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'courses';

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'university_id',
        'name',
        'degree_level', // e.g., 'Bachelors', 'Masters', 'PhD'
        'field_of_study', // e.g., 'Computer Science', 'Business'
        'tuition_fee',
        'duration_years',
        'description',
        'application_deadline',
    ];

    /**
     * The attributes that should be cast.
     * @var array
     */
    protected $casts = [
        'tuition_fee' => 'decimal:2',
        'duration_years' => 'float',
        'application_deadline' => 'date',
    ];

    /**
     * Get the university that offers this course.
     */
    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }
}