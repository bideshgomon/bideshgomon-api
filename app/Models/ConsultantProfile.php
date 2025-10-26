<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultantProfile extends Model
{
    use HasFactory;
    
    // Check your migration, if table is 'consultant_user', uncomment the line below
    // protected $table = 'consultant_user'; 

    protected $fillable = [
        'user_id',
        'title',
        'bio',
        'specializations',
        'experience_years',
        'hourly_rate',
        'is_available',
    ];

    protected $casts = [
        'specializations' => 'array',
        'is_available' => 'boolean',
        'hourly_rate' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}