<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataAccessRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'consultant_id',
        'user_id',
        'status',
        'requested_at',
        'responded_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'requested_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    /**
     * Get the consultant (User) who made the request.
     */
    public function consultant(): BelongsTo
    {
        // Links 'consultant_id' in this table to the 'id' in the 'users' table
        return $this->belongsTo(User::class, 'consultant_id');
    }

    /**
     * Get the user whose data is being requested.
     */
    public function user(): BelongsTo
    {
        // Links 'user_id' in this table to the 'id' in the 'users' table
        return $this->belongsTo(User::class, 'user_id');
    }
}
