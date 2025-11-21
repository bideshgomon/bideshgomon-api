<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileView extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'referrer',
        'country',
        'city',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    /**
     * Get the user whose profile was viewed.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
