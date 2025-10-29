<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; // <-- Corrected
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWorkExperience extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_work_experiences';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'company',
        'location',
        'start_date',
        'end_date',
        'is_current',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    /**
     * Get the user that owns the work experience.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}