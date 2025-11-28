<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgencyTeamMember extends Model
{
    protected $fillable = [
        'agency_id',
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
        'qualifications' => 'array',
        'languages' => 'array',
        'years_experience' => 'integer',
        'is_visible' => 'boolean',
        'display_order' => 'integer',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
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
