<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgencyTeamMember extends Model
{
    protected $fillable = [
        'agency_id',
        'user_id',
        'role',
        'permissions',
        'status',
        'invitation_token',
        'invited_at',
        'joined_at',
        'name',
        'position',
        'email',
        'phone',
        'photo',
        'bio',
        'qualifications',
        'languages',
        'years_experience',
        'is_visible',
        'display_order',
    ];

    protected $casts = [
        'permissions' => 'array',
        'qualifications' => 'array',
        'languages' => 'array',
        'years_experience' => 'integer',
        'is_visible' => 'boolean',
        'display_order' => 'integer',
        'invited_at' => 'datetime',
        'joined_at' => 'datetime',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    // NEW: Link to User account (for consultants with login)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }
}
