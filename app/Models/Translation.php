<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Translation extends Model
{
    protected $fillable = [
        'user_id', 'application_reference', 'document_type', 'source_language',
        'target_language', 'page_count', 'certification_type', 'is_urgent',
        'required_by_date', 'status', 'user_notes'
    ];

    protected $casts = [
        'is_urgent' => 'boolean',
        'required_by_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($translation) {
            if (empty($translation->application_reference)) {
                $translation->application_reference = 'TR' . strtoupper(uniqid());
            }
        });
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function serviceApplication(): HasOne { return $this->hasOne(ServiceApplication::class, 'translation_id'); }
    public function canBeEditedByUser(): bool { return in_array($this->status, ['pending', 'submitted']); }
}
