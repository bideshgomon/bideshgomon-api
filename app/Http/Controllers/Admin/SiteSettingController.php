<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SiteSettingController extends Controller
{
    /**
     * Display settings by group
     */
    public function index(Request $request)
    {
        $group = $request->get('group', 'general');
        
        $settings = SiteSetting::where('group', $group)
            ->orderBy('sort_order', 'asc')
            ->get();

        $groups = [
            'general' => 'General Settings',
            'branding' => 'Branding & Logo',
            'seo' => 'SEO & Analytics',
            'social' => 'Social Media',
            'contact' => 'Contact Information',
            'api' => 'API Keys',
            'advanced' => 'Advanced Settings',
        ];

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
            'currentGroup' => $group,
            'groups' => $groups,
        ]);
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $settings = $request->input('settings', []);

        foreach ($settings as $key => $value) {
            $setting = SiteSetting::where('key', $key)->first();

            if (!$setting || !$setting->is_editable) {
                continue;
            }

            // Handle file uploads for image type settings
            if ($setting->type === 'image' && $request->hasFile($key)) {
                // Delete old file if exists
                if ($setting->value) {
                    Storage::disk('public')->delete($setting->value);
                }

                // Store new file
                $path = $request->file($key)->store('settings', 'public');
                $value = $path;
            }

            // Handle boolean values
            if ($setting->type === 'boolean') {
                $value = $value ? '1' : '0';
            }

            // Handle JSON values
            if ($setting->type === 'json' && is_array($value)) {
                $value = json_encode($value);
            }

            $setting->update(['value' => $value]);
        }

        return back()->with('success', 'Settings updated successfully.');
    }

    /**
     * Update a single setting (for AJAX requests)
     */
    public function updateSingle(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'nullable',
        ]);

        $setting = SiteSetting::where('key', $request->key)->first();

        if (!$setting || !$setting->is_editable) {
            return response()->json(['error' => 'Setting not found or not editable'], 404);
        }

        // Handle file upload
        if ($setting->type === 'image' && $request->hasFile('value')) {
            // Delete old file
            if ($setting->value) {
                Storage::disk('public')->delete($setting->value);
            }

            $path = $request->file('value')->store('settings', 'public');
            $value = $path;
        } else {
            $value = $request->value;

            // Handle boolean
            if ($setting->type === 'boolean') {
                $value = $value ? '1' : '0';
            }

            // Handle JSON
            if ($setting->type === 'json' && is_array($value)) {
                $value = json_encode($value);
            }
        }

        $setting->update(['value' => $value]);

        return response()->json([
            'success' => true,
            'message' => 'Setting updated successfully.',
            'setting' => $setting,
        ]);
    }

    /**
     * Delete uploaded file (logo, favicon, etc.)
     */
    public function deleteFile(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
        ]);

        $setting = SiteSetting::where('key', $request->key)->first();

        if (!$setting || $setting->type !== 'image' || !$setting->value) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // Delete file from storage
        Storage::disk('public')->delete($setting->value);

        // Update setting
        $setting->update(['value' => null]);

        return response()->json([
            'success' => true,
            'message' => 'File deleted successfully.',
        ]);
    }

    /**
     * Reset settings to default
     */
    public function reset(Request $request)
    {
        $group = $request->input('group');

        if ($group) {
            // Reset specific group
            $settings = SiteSetting::where('group', $group)->get();
        } else {
            // Reset all settings
            $settings = SiteSetting::all();
        }

        foreach ($settings as $setting) {
            if ($setting->type === 'image' && $setting->value) {
                Storage::disk('public')->delete($setting->value);
            }
        }

        // Re-run seeder
        \Artisan::call('db:seed', ['--class' => 'SiteSettingsSeeder']);

        return back()->with('success', 'Settings reset to default successfully.');
    }

    /**
     * Clear settings cache
     */
    public function clearCache()
    {
        \Cache::forget('site_settings');

        return back()->with('success', 'Settings cache cleared successfully.');
    }
}

