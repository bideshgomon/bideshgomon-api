<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserEducation extends Model
{
    use HasFactory;

    /**
     * Explicitly tell the model to use the 'user_educations' (plural) table.
     */
    protected $table = 'user_educations';

    protected $fillable = [
        'user_id',
        'institution_name',
        'degree_name',
        'field_of_study',
        'start_date',
        'end_date',
        'grade',
        'description'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}