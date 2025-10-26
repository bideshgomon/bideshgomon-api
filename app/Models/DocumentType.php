<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // <-- ADDED
use Illuminate\Support\Str;
use App\Models\TouristVisaDocument; // <-- ADDED

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
        'description',
        'has_expiry_date',
        'is_active',
        'applies_to', // <-- MERGED from snippet 1
    ];

    /**
     * The attributes that should be cast.
     * (MERGED from snippet 1)
     *
     * @var array
     */
    protected $casts = [
        'applies_to' => 'array',
        'has_expiry_date' => 'boolean', // Assuming this should be boolean
        'is_active' => 'boolean',
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

    /**
     * Get the tourist visa document entries associated with this document type.
     * (MERGED from snippet 1)
     */
    public function touristVisaDocuments(): HasMany
    {
        return $this->hasMany(TouristVisaDocument::class);
    }
    
    // You may still have the original userDocuments relationship
    // public function userDocuments(): HasMany
    // {
    //     return $this->hasMany(UserDocument::class);
    // }
}