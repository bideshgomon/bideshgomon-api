<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CvTemplate extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'thumbnail',
        'category',
        'is_premium',
        'price',
        'color_scheme',
        'sections',
        'download_count',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'price' => 'decimal:2',
        'color_scheme' => 'array',
        'sections' => 'array',
        'is_active' => 'boolean',
        'download_count' => 'integer',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function userCvs()
    {
        return $this->hasMany(UserCv::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFree($query)
    {
        return $query->where('is_premium', false);
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Helpers
    public function isFree()
    {
        return !$this->is_premium;
    }

    public function getFormattedPriceAttribute()
    {
        return '৳' . number_format((float) $this->price, 0);
    }

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($template) {
            if (empty($template->slug)) {
                $template->slug = Str::slug($template->name);
            }
        });
    }
}
