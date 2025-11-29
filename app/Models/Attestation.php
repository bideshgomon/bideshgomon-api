<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Attestation extends Model
{
    protected $fillable = [
        'user_id', 'target_country_id', 'application_reference', 'document_type',
        'attestation_type', 'purpose', 'document_count', 'is_urgent',
        'required_by_date', 'status', 'user_notes'
    ];

    protected $casts = [
        'is_urgent' => 'boolean',
        'required_by_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($attestation) {
            if (empty($attestation->application_reference)) {
                $attestation->application_reference = 'AT' . strtoupper(uniqid());
            }
        });
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function targetCountry(): BelongsTo { return $this->belongsTo(Country::class, 'target_country_id'); }
    public function serviceApplication(): HasOne { return $this->hasOne(ServiceApplication::class, 'attestation_id'); }
    public function canBeEditedByUser(): bool { return in_array($this->status, ['pending', 'submitted']); }
}
