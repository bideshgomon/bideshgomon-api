<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Thin alias model to satisfy legacy tests expecting WorkExperience instead of UserWorkExperience.
 * Bridges differing column names (job_title -> position, currently_working -> is_current_employment).
 */
class WorkExperience extends Model
{
    use HasFactory;

    protected $table = 'user_work_experiences';

    protected $fillable = [
        'user_id',
        'company_name',
        'position', // internal column
        'start_date',
        'end_date',
        'is_current_employment',
        'job_description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'currently_working' => 'boolean',
        'is_current_employment' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (WorkExperience $model) {
            // Map legacy attributes passed by tests before insert
            if (empty($model->position)) {
                $model->position = $model->getAttribute('job_title') ?: 'Unknown';
            }
            if ($model->getAttribute('currently_working') !== null) {
                $model->is_current_employment = (bool) $model->getAttribute('currently_working');
            }
            // Remove legacy attributes so they are not part of the insert statement
            unset($model->job_title, $model->currently_working);
            if (empty($model->start_date)) {
                $model->start_date = now()->subYears(1);
            }
        });

        static::updating(function (WorkExperience $model) {
            if ($model->getAttribute('job_title')) {
                $model->position = $model->getAttribute('job_title') ?: $model->position;
                unset($model->job_title);
            }
            if ($model->getAttribute('currently_working') !== null) {
                $model->is_current_employment = (bool) $model->getAttribute('currently_working');
                unset($model->currently_working);
            }
        });
    }
}
