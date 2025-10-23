<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserExperience extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * We add this to avoid singular/plural issues.
     *
     * @var string
     */
    protected $table = 'user_experiences';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'company_name',
        'designation',
        'description',
        'start_date',
        'end_date',
        'is_current',
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
     * Get the user that owns the experience record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}