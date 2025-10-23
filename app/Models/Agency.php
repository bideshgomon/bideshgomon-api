<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Agency extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'company_name',
        'contact_email',
        'contact_phone',
        'address',
        'license_number',
        'status',
    ];

    /**
     * Get the owner (a user with 'agency' role) of this agency.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the consultants (users with 'consultant' role)
     * associated with this agency.
     */
    public function consultants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'agency_consultant', 'agency_id', 'consultant_id');
    }
}