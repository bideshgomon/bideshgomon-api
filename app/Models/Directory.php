<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Directory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'directory_category_id',
        'name',
        'name_bn',
        'slug',
        'description',
        'description_bn',
        'country_id',
        'city',
        'address',
        'phone',
        'email',
        'website',
        'logo',
        'image',
        'latitude',
        'longitude',
        'operating_hours',
        'services',
        'social_media',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'views_count',
        'rating',
        'reviews_count',
        'is_verified',
        'is_featured',
        'is_published',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'operating_hours' => 'array',
        'services' => 'array',
        'social_media' => 'array',
        'views_count' => 'integer',
        'rating' => 'float',
        'reviews_count' => 'integer',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(DirectoryCategory::class, 'directory_category_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('directory_category_id', $categoryId);
    }

    public function scopeByCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('name_bn', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%");
        });
    }

    // Helpers
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($directory) {
            if (empty($directory->slug)) {
                $directory->slug = Str::slug($directory->name);
            }
            if (empty($directory->meta_title)) {
                $directory->meta_title = $directory->name;
            }
            if (empty($directory->meta_description)) {
                $directory->meta_description = Str::limit(strip_tags($directory->description), 160);
            }
        });

        static::updating(function ($directory) {
            if ($directory->isDirty('name') && !$directory->isDirty('slug')) {
                $directory->slug = Str::slug($directory->name);
            }
        });

        static::deleting(function ($directory) {
            if ($directory->logo && Storage::disk('public')->exists($directory->logo)) {
                Storage::disk('public')->delete($directory->logo);
            }
            if ($directory->image && Storage::disk('public')->exists($directory->image)) {
                Storage::disk('public')->delete($directory->image);
            }
        });
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo ? Storage::url($this->logo) : null;
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    public function getExcerpt($length = 150)
    {
        return Str::limit(strip_tags($this->description), $length);
    }

    public function getMapUrlAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }
        return null;
    }
}
