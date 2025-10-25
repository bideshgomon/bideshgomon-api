<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use Illuminate\Support\Facades\Cache; // Import Cache facade

class SettingController extends Controller
{
    /**
     * Display the settings management page.
     */
    public function index(): Response
    {
        // Fetch all settings, keyed by their 'key' for easy access in Vue
        // Use caching to avoid hitting the DB every time
        $settings = Cache::rememberForever('app_settings', function () {
            return Setting::all()->keyBy('key');
        });


        // Define the settings fields we want to manage in the form
        $definedSettings = [
            'site_name' => ['label' => 'Site Name', 'value' => $settings->get('site_name')?->value ?? config('app.name'), 'type' => 'string'],
            'contact_email' => ['label' => 'Contact Email', 'value' => $settings->get('contact_email')?->value ?? '', 'type' => 'string'],
            'contact_phone' => ['label' => 'Contact Phone', 'value' => $settings->get('contact_phone')?->value ?? '', 'type' => 'string'],
            // Add more settings here as needed
            // 'enable_feature_x' => ['label' => 'Enable Feature X', 'value' => $settings->get('enable_feature_x')?->value ?? false, 'type' => 'boolean'],
        ];

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $definedSettings,
        ]);
    }

    /**
     * Update the specified settings in storage.
     */
    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $validatedData = $request->validated()['settings'];

        foreach ($validatedData as $key => $value) {
            // Use updateOrCreate to add/update settings
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value] // The model mutator will handle type conversion if needed
            );
        }

        // Cache will be cleared automatically by the model event listener

        return redirect()->route('admin.settings.index')
                         ->with('success', 'Settings updated successfully.');
    }
}