<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AdminSettingsController extends Controller
{
    public function index(Request $request)
    {
        $currentGroup = $request->get('group', 'general');
        
        $settings = SiteSetting::orderBy('group')->orderBy('sort_order')->get();

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
            'currentGroup' => $currentGroup,
            'groups' => SiteSetting::distinct('group')->pluck('group')->mapWithKeys(function($group) {
                return [$group => ucfirst(str_replace('_', ' ', $group))];
            }),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable',
        ]);

        $updatedCount = 0;
        
        foreach ($validated['settings'] as $settingData) {
            $setting = SiteSetting::where('key', $settingData['key'])->first();
            
            if ($setting) {
                $value = $settingData['value'];
                
                // Handle different types
                if ($setting->type === 'boolean') {
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
                } elseif ($setting->type === 'json' && is_array($value)) {
                    $value = json_encode($value);
                } elseif ($value === null || $value === '') {
                    // Keep empty values as null for optional fields
                    $value = null;
                }

                $setting->update(['value' => $value]);
                $updatedCount++;
                
                Log::info("Updated setting: {$setting->key} = " . ($value ?? '(null)'));
            }
        }

        SiteSetting::clearCache();

        return back()->with('success', "Successfully updated {$updatedCount} setting(s)!");
    }

    public function create()
    {
        return Inertia::render('Admin/Settings/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:site_settings,key',
            'value' => 'nullable',
            'group' => 'required|string',
            'type' => 'required|in:text,number,boolean,json,email,url',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $value = $validated['value'] ?? '';
        
        if ($validated['type'] === 'boolean') {
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
        } elseif ($validated['type'] === 'json' && is_array($value)) {
            $value = json_encode($value);
        }

        SiteSetting::create([
            'key' => $validated['key'],
            'value' => $value,
            'group' => $validated['group'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'is_public' => $validated['is_public'] ?? false,
        ]);

        return redirect()->route('admin.settings.index')->with('success', 'Setting created successfully!');
    }

    public function destroy($id)
    {
        $setting = SiteSetting::findOrFail($id);
        $setting->delete();

        return back()->with('success', 'Setting deleted successfully!');
    }

    public function seed()
    {
        $defaultSettings = [
            // General Settings
            ['key' => 'site_name', 'value' => 'Bidesh Gomon', 'group' => 'general', 'type' => 'text', 'description' => 'Website name', 'is_public' => true],
            ['key' => 'site_description', 'value' => 'Your trusted platform for international migration services', 'group' => 'general', 'type' => 'text', 'description' => 'Website description', 'is_public' => true],
            ['key' => 'site_logo', 'value' => '/images/logo.png', 'group' => 'general', 'type' => 'text', 'description' => 'Website logo URL', 'is_public' => true],
            ['key' => 'contact_email', 'value' => 'support@bideshgomon.com', 'group' => 'general', 'type' => 'email', 'description' => 'Contact email address', 'is_public' => true],
            ['key' => 'contact_phone', 'value' => '+880 1234567890', 'group' => 'general', 'type' => 'text', 'description' => 'Contact phone number', 'is_public' => true],
            ['key' => 'support_hours', 'value' => '9:00 AM - 6:00 PM (GMT+6)', 'group' => 'general', 'type' => 'text', 'description' => 'Support hours', 'is_public' => true],
            
            // Email Settings
            ['key' => 'email_from_name', 'value' => 'Bidesh Gomon', 'group' => 'email', 'type' => 'text', 'description' => 'Email sender name', 'is_public' => false],
            ['key' => 'email_from_address', 'value' => 'noreply@bideshgomon.com', 'group' => 'email', 'type' => 'email', 'description' => 'Email sender address', 'is_public' => false],
            ['key' => 'email_footer', 'value' => 'Thank you for using Bidesh Gomon!', 'group' => 'email', 'type' => 'text', 'description' => 'Email footer text', 'is_public' => false],
            
            // Job Settings
            ['key' => 'job_application_fee', 'value' => '500', 'group' => 'jobs', 'type' => 'number', 'description' => 'Default job application fee (BDT)', 'is_public' => true],
            ['key' => 'job_posting_duration', 'value' => '30', 'group' => 'jobs', 'type' => 'number', 'description' => 'Job posting duration in days', 'is_public' => false],
            ['key' => 'max_applications_per_user', 'value' => '10', 'group' => 'jobs', 'type' => 'number', 'description' => 'Maximum applications per user per day', 'is_public' => false],
            
            // Wallet Settings
            ['key' => 'min_withdrawal_amount', 'value' => '1000', 'group' => 'wallet', 'type' => 'number', 'description' => 'Minimum withdrawal amount (BDT)', 'is_public' => true],
            ['key' => 'referral_bonus', 'value' => '100', 'group' => 'wallet', 'type' => 'number', 'description' => 'Referral bonus amount (BDT)', 'is_public' => true],
            ['key' => 'cashback_percentage', 'value' => '5', 'group' => 'wallet', 'type' => 'number', 'description' => 'Cashback percentage', 'is_public' => true],
            
            // Feature Flags
            ['key' => 'enable_registrations', 'value' => '1', 'group' => 'features', 'type' => 'boolean', 'description' => 'Enable user registrations', 'is_public' => true],
            ['key' => 'enable_job_applications', 'value' => '1', 'group' => 'features', 'type' => 'boolean', 'description' => 'Enable job applications', 'is_public' => true],
            ['key' => 'enable_referrals', 'value' => '1', 'group' => 'features', 'type' => 'boolean', 'description' => 'Enable referral system', 'is_public' => true],
            ['key' => 'maintenance_mode', 'value' => '0', 'group' => 'features', 'type' => 'boolean', 'description' => 'Enable maintenance mode', 'is_public' => true],
            
            // Social Media
            ['key' => 'facebook_url', 'value' => 'https://facebook.com/bideshgomon', 'group' => 'social', 'type' => 'url', 'description' => 'Facebook page URL', 'is_public' => true],
            ['key' => 'twitter_url', 'value' => 'https://twitter.com/bideshgomon', 'group' => 'social', 'type' => 'url', 'description' => 'Twitter profile URL', 'is_public' => true],
            ['key' => 'linkedin_url', 'value' => 'https://linkedin.com/company/bideshgomon', 'group' => 'social', 'type' => 'url', 'description' => 'LinkedIn page URL', 'is_public' => true],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com/bideshgomon', 'group' => 'social', 'type' => 'url', 'description' => 'Instagram profile URL', 'is_public' => true],
        ];

        foreach ($defaultSettings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        SiteSetting::clearCache();

        return back()->with('success', 'Default settings seeded successfully!');
    }
}
