<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminImpersonationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'impersonator_id',
        'target_user_id',
        'started_at',
        'ended_at',
        'purpose',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /**
     * Duration in minutes (integer) if the session has ended, otherwise null.
     */
    public function getDurationMinutesAttribute(): ?int
    {
        return $this->ended_at ? abs($this->started_at->diffInMinutes($this->ended_at, false)) : null;
    }

    public function impersonator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'impersonator_id');
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
