<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HajjUmrah extends Model
{
    protected $fillable = [
        'user_id', 'application_reference', 'package_type', 'number_of_travelers',
        'preferred_travel_date', 'duration', 'accommodation_type', 'meal_plan',
        'transport_type', 'requires_visa_assistance', 'requires_training',
        'status', 'special_requirements'
    ];

    protected $casts = [
        'preferred_travel_date' => 'date',
        'requires_visa_assistance' => 'boolean',
        'requires_training' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($hajj) {
            if (empty($hajj->application_reference)) {
                $hajj->application_reference = 'HJ' . strtoupper(uniqid());
            }
        });
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function serviceApplication(): HasOne { return $this->hasOne(ServiceApplication::class, 'hajj_umrah_id'); }
    public function canBeEditedByUser(): bool { return in_array($this->status, ['pending', 'submitted']); }
}
