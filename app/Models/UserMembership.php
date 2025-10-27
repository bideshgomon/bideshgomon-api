<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- [PATCH]
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- [PATCH]

class UserMembership extends Model
{
    // --- [PATCH START] ---
    use HasFactory;

    protected $fillable = [
        'user_id',
        'organization_type_id',
        'organization_name',
        'position', // <-- MIGRATION HAS 'position', controller uses 'membership_id'. Review controller/migration. Assuming 'position' from migration is correct.
        'start_date',
        'end_date',
        'is_current',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organizationType(): BelongsTo
    {
        return $this->belongsTo(OrganizationType::class);
    }
    // --- [PATCH END] ---
}