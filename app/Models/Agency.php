<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agency extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'name',
        'address',
        'city',
        'country',
        'license_number',
        'website',
        'status',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function consultants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'agency_consultant', 'agency_id', 'consultant_id');
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'agency_user', 'agency_id', 'user_id');
    }
}
