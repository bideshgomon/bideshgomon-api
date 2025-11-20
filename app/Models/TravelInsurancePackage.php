<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TravelInsurancePackage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'features',
        'price_per_day',
        'min_price',
        'max_coverage',
        'min_days',
        'max_days',
        'min_age',
        'max_age',
        'covered_countries',
        'coverage_details',
        'is_popular',
        'is_active',
        'sort_order',
        'badge_text',
        'badge_color',
    ];

    protected $casts = [
        'features' => 'array',
        'covered_countries' => 'array',
        'coverage_details' => 'array',
        'price_per_day' => 'decimal:2',
        'min_price' => 'decimal:2',
        'max_coverage' => 'decimal:2',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function bookings()
    {
        return $this->hasMany(TravelInsuranceBooking::class, 'package_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Helpers
    public function calculatePrice(int $days, int $travelers = 1): float
    {
        $basePrice = $this->price_per_day * $days * $travelers;
        return max($basePrice, $this->min_price);
    }

    public function getFormattedPricePerDayAttribute(): string
    {
        return '৳ ' . number_format($this->price_per_day, 2);
    }

    public function getFormattedMaxCoverageAttribute(): string
    {
        return '৳ ' . number_format($this->max_coverage, 0);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($package) {
            if (empty($package->slug)) {
                $package->slug = Str::slug($package->name);
            }
        });
    }
}