<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SeoSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_type',
        'title',
        'description',
        'keywords',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'og_type',
        'twitter_card',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'twitter_site',
        'schema_markup',
        'index',
        'follow',
        'robots',
    ];

    protected $casts = [
        'schema_markup' => 'array',
        'index' => 'boolean',
        'follow' => 'boolean',
    ];

    /**
     * Get SEO settings for a specific page type
     */
    public static function getForPage(string $pageType): ?self
    {
        return Cache::remember("seo_settings_{$pageType}", 3600, function () use ($pageType) {
            return self::where('page_type', $pageType)->first();
        });
    }

    /**
     * Clear cache for this page type
     */
    public function clearCache(): void
    {
        Cache::forget("seo_settings_{$this->page_type}");
    }

    /**
     * Get robots meta tag value
     */
    public function getRobotsMetaAttribute(): string
    {
        $parts = [];
        if ($this->index) $parts[] = 'index';
        else $parts[] = 'noindex';
        
        if ($this->follow) $parts[] = 'follow';
        else $parts[] = 'nofollow';
        
        if ($this->robots) $parts[] = $this->robots;
        
        return implode(', ', $parts);
    }

    /**
     * Boot method to clear cache on save
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($seoSetting) {
            $seoSetting->clearCache();
        });

        static::deleted(function ($seoSetting) {
            $seoSetting->clearCache();
        });
    }
}
