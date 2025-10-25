<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DocumentType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description', // Keep other valid fields
        'has_expiry_date',
        'is_active',
        // --- FIX: REMOVE 'requires_upload' ---
        // 'requires_upload',
        // ------------------------------------
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::saving(function ($documentType) {
            $documentType->slug = Str::slug($documentType->name);
        });
    }
}