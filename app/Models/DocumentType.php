<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class DocumentType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'has_expiry_date', 'is_active', 'applies_to'];

    protected $casts = ['applies_to' => 'array', 'has_expiry_date' => 'boolean', 'is_active' => 'boolean'];

    protected static function booted(): void
    {
        static::saving(function ($d) {
            if (empty($d->slug)) {
                $d->slug = Str::slug($d->name);
            }
        });
    }

    public function userDocuments(): HasMany
    {
        return $this->hasMany(UserDocument::class);
    }

    public function touristVisaDocuments(): HasMany
    {
        return $this->hasMany(TouristVisaDocument::class);
    }
}
