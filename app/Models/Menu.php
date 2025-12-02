<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'location',
        'url',
        'route_name',
        'icon',
        'parent_id',
        'type',
        'target',
        'classes',
        'order',
        'is_active',
        'permissions',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_external' => 'boolean',
        'order' => 'integer',
        'permissions' => 'array',
    ];

    // Clear cache on changes
    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('menus');
        });

        static::deleted(function () {
            Cache::forget('menus');
        });
    }

    // Relationships
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    // Static methods for menu retrieval
    public static function getMenuByLocation($location)
    {
        return Cache::remember("menu_{$location}", 3600, function () use ($location) {
            return self::active()
                ->byLocation($location)
                ->topLevel()
                ->with('children')
                ->orderBy('order')
                ->get();
        });
    }

    public static function clearMenuCache()
    {
        Cache::forget('menus');
        $locations = ['header_main', 'footer_column_1', 'footer_column_2', 'footer_column_3', 'mobile_menu'];
        foreach ($locations as $location) {
            Cache::forget("menu_{$location}");
        }
    }

    // Generate URL
    public function getFullUrl()
    {
        if ($this->is_external) {
            return $this->url;
        }
        
        if ($this->route_name) {
            try {
                return route($this->route_name);
            } catch (\Exception $e) {
                return $this->url ?? '#';
            }
        }
        
        return $this->url ?? '#';
    }
}
