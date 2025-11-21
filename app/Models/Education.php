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
        'institution_name',
        'field_of_study',
        'start_date',
        'end_date',
        'is_current',
        'country',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];
}
