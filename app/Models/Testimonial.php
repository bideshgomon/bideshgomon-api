<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Testimonial extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'author_name',
        'author_name_bn',
        'position',
        'position_bn',
        'company',
        'company_bn',
        'content',
        'content_bn',
        'photo',
        'rating',
        'location',
        'is_featured',
        'is_approved',
        'sort_order',
        'updated_by'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_approved' => 'boolean',
        'rating' => 'integer',
        'sort_order' => 'integer'
    ];

    // Relationships
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    // Helpers
    public function getImageUrlAttribute()
    {
        return $this->photo ? Storage::url($this->photo) : null;
    }
    
    public function getStarsArray()
    {
        return array_fill(0, $this->rating, true);
    }

    public function getExcerpt($length = 100)
    {
        return strlen($this->content) > $length 
            ? substr($this->content, 0, $length) . '...' 
            : $this->content;
    }
    
    public static function updateOrder(array $ids): void
    {
        foreach ($ids as $index => $id) {
            static::where('id', $id)->update(['sort_order' => $index + 1]);
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($testimonial) {
            if ($testimonial->photo && Storage::disk('public')->exists($testimonial->photo)) {
                Storage::disk('public')->delete($testimonial->photo);
            }
        });
    }
}
