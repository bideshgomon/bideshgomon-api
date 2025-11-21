<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type',
        'user_id',
        'target_type',
        'target_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function scopeTypePrefix($query, string $prefix)
    {
        return $query->where('event_type', 'like', $prefix . '%');
    }
}
