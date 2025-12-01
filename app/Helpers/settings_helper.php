<?php

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

if (!function_exists('get_setting')) {
    /**
     * Get a setting value with safe fallback
     * GLOBAL STANDARD: Never returns null, always returns a default
     *
     * @param string $key Setting key
     * @param mixed $default Fallback value
     * @return mixed
     */
    function get_setting($key, $default = null)
    {
        try {
            $value = SiteSetting::get($key);
            
            // If value is null or empty, return default
            if ($value === null || $value === '') {
                return $default ?? config("defaults.{$key}", $default);
            }
            
            return $value;
        } catch (\Exception $e) {
            \Log::error("Failed to get setting: {$key}", ['error' => $e->getMessage()]);
            return $default ?? config("defaults.{$key}", $default);
        }
    }
}

if (!function_exists('module_enabled')) {
    /**
     * Check if a module is enabled
     *
     * @param string $module Module name (jobs, blogs, directory, university, packages)
     * @return bool
     */
    function module_enabled($module)
    {
        return (bool) get_setting("enable_{$module}", true);
    }
}

if (!function_exists('feature_enabled')) {
    /**
     * Check if a feature flag is enabled
     *
     * @param string $feature Feature name
     * @return bool
     */
    function feature_enabled($feature)
    {
        $value = get_setting($feature, '1');
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}

if (!function_exists('get_settings_group')) {
    /**
     * Get all settings for a specific group
     *
     * @param string $group Group name
     * @return array
     */
    function get_settings_group($group)
    {
        return Cache::remember("settings_group_{$group}", 3600, function () use ($group) {
            return SiteSetting::byGroup($group)->pluck('value', 'key')->toArray();
        });
    }
}

if (!function_exists('get_public_settings')) {
    /**
     * Get all public settings (safe for frontend)
     *
     * @return array
     */
    function get_public_settings()
    {
        return Cache::remember('public_settings', 3600, function () {
            $settings = SiteSetting::getAllCached();
            
            // Filter to only include keys that should be public
            $publicKeys = [
                'site_name',
                'site_description',
                'site_logo',
                'contact_email',
                'contact_phone',
                'support_hours',
                'facebook_url',
                'twitter_url',
                'linkedin_url',
                'instagram_url',
                'enable_registrations',
                'enable_job_applications',
                'maintenance_mode',
                // Module enables
                'enable_jobs',
                'enable_blogs',
                'enable_directory',
                'enable_university',
                'enable_packages',
                // Homepage widgets
                'show_hero_search',
                'show_featured_jobs',
                'show_top_universities',
                'show_latest_blogs',
                'show_visa_packages',
            ];
            
            return array_intersect_key($settings, array_flip($publicKeys));
        });
    }
}

if (!function_exists('clear_settings_cache')) {
    /**
     * Clear all settings-related caches
     *
     * @return void
     */
    function clear_settings_cache()
    {
        Cache::forget('site_settings');
        Cache::forget('public_settings');
        
        // Clear group caches
        $groups = ['general', 'branding', 'seo', 'email', 'contact', 'jobs', 'wallet', 'features', 'social', 'api', 'modules', 'homepage'];
        foreach ($groups as $group) {
            Cache::forget("settings_group_{$group}");
        }
        
        SiteSetting::clearCache();
    }
}
