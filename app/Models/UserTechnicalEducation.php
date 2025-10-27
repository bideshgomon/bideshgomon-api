<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- [PATCH]
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- [PATCH]

class UserTechnicalEducation extends Model
{
    // --- [PATCH START] ---
    use HasFactory;

    protected $fillable = [
        'user_id',
        'technical_education_type_id',
        'course_name',
        'institution_name',
        'start_date', // <-- MIGRATION HAS 'start_date', controller uses 'completion_date'. Review controller/migration.
        'end_date', // <-- MIGRATION HAS 'end_date'
        'file_path',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function educationType(): BelongsTo
    {
        return $this->belongsTo(TechnicalEducationType::class, 'technical_education_type_id');
    }
    // --- [PATCH END] ---
}