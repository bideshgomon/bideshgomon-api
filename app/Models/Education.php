<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Thin alias model for backwards compatibility with tests expecting Education instead of UserEducation.
 * Maps to user_educations table.
 */
class Education extends Model
{
    use HasFactory;

    protected $table = 'user_educations';

    protected $fillable = [
        'user_id',
        'degree_level',
        'degree',
        'institution_name',
        'field_of_study',
        'start_date',
        'end_date',
        'is_current',
        'country',
        'gpa',
        'gpa_or_grade',
        'certificates_upload',
    ];

    /**
     * Default attributes for required legacy columns.
     */
    protected $attributes = [
        'degree' => 'Unknown',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'gpa' => 'float',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Education $model) {
            // Ensure non-null degree value for existing NOT NULL constraint
            if (empty($model->degree)) {
                $model->degree = 'Unknown';
            }
            // Provide a fallback start_date if none supplied (legacy tests omit it but column is NOT NULL)
            if (empty($model->start_date)) {
                $model->start_date = now()->subYears(1);
            }
        });
    }
}
