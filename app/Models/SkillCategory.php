<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SkillCategory extends Model
{
    protected $fillable = [
        'name',
        'name_bn',
        'slug',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug from name if not provided
        static::creating(function ($skillCategory) {
            if (empty($skillCategory->slug) && !empty($skillCategory->name)) {
                $skillCategory->slug = Str::slug($skillCategory->name);
            }
        });

        static::updating(function ($skillCategory) {
            if (empty($skillCategory->slug) && !empty($skillCategory->name)) {
                $skillCategory->slug = Str::slug($skillCategory->name);
            }
        });
    }

    /**
     * Skills in this category.
     */
    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class)->orderBy('name');
    }

    /**
     * Scope: Only active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Ordered by order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }
}
