<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'description',
        'is_editable',
        'sort_order',
    ];

    protected $casts = [
        'is_editable' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Cache settings for better performance
    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('site_settings');
        });

        static::deleted(function () {
            Cache::forget('site_settings');
        });
    }

    // Get a setting value by key
    public static function get($key, $default = null)
    {
        $settings = self::getAllCached();
        return $settings[$key] ?? $default;
    }

    // Get all settings cached
    public static function getAllCached()
    {
        return Cache::remember('site_settings', 3600, function () {
            return self::pluck('value', 'key')->toArray();
        });
    }

    // Get settings by group
    public static function getByGroup($group)
    {
        return self::where('group', $group)
            ->orderBy('sort_order', 'asc')
            ->get();
    }

    // Set a setting value
    public static function set($key, $value, $group = 'general', $type = 'text')
    {
        return self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
                'type' => $type,
            ]
        );
    }

    // Query Scopes
    public function scopeEditable($query)
    {
        return $query->where('is_editable', true);
    }

    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    // Get decoded JSON value if type is json
    public function getDecodedValue()
    {
        if ($this->type === 'json') {
            return json_decode($this->value, true);
        }
        return $this->value;
    }

    // Clear settings cache
    public static function clearCache()
    {
        Cache::forget('site_settings');
    }
}
