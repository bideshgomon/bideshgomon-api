<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Page extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'title_bn',
        'slug',
        'content',
        'content_bn',
        'page_type',
        'template',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_published',
        'show_in_footer',
        'sort_order',
        'published_at',
        'updated_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'show_in_footer' => 'boolean',
        'published_at' => 'datetime',
        'sort_order' => 'integer',
    ];

    // Auto-generate slug from title
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    // Relationships
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Query Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeFooterPages($query)
    {
        return $query->where('show_in_footer', true)
            ->published()
            ->orderBy('sort_order', 'asc');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('page_type', $type);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%")
              ->orWhere('slug', 'like', "%{$search}%");
        });
    }

    // Helper Methods
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getExcerpt($length = 150)
    {
        return Str::limit(strip_tags($this->content), $length);
    }

    public function getPageTypeLabel()
    {
        return match($this->page_type) {
            'terms' => 'Terms of Service',
            'privacy' => 'Privacy Policy',
            'refund' => 'Refund Policy',
            'about' => 'About Us',
            'help' => 'Help Center',
            'contact' => 'Contact Us',
            default => ucfirst($this->page_type),
        };
    }

    public function getTemplateLabel()
    {
        return match($this->template) {
            'default' => 'Default Template',
            'legal' => 'Legal Document',
            'informational' => 'Informational',
            'full-width' => 'Full Width',
            default => ucfirst($this->template),
        };
    }
}
