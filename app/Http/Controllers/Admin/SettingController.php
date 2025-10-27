<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting; // <-- Ensure this model exists
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
// --- REMOVED unused UpdateSettingsRequest for now ---
// use App\Http\Requests\Admin\UpdateSettingsRequest;
use Illuminate\Support\Facades\Cache; // Import Cache facade
use Illuminate\Support\Facades\Log; // Import Log facade

class SettingController extends Controller
{
    /**
     * Display the settings management page.
     */
    public function index(): Response
    {
        // Fetch all settings, keyed by their 'key' for easy access in Vue
        // Use caching to avoid hitting the DB every time
        // We define a default structure in case settings are missing
        $settingsFromDb = Cache::rememberForever('app_settings', function () {
            // Ensure Setting model exists and uses 'key' as primary key or has 'key' column
             try {
                return Setting::all()->keyBy('key');
            } catch (\Exception $e) {
                // Log error if table/model doesn't exist
                Log::error("Error fetching settings: " . $e->getMessage());
                return collect(); // Return empty collection on error
            }
        });

        // Define the settings fields we want to manage in the form
        // Provide default values if not found in DB or cache
        $definedSettings = [
            'site_name' => [
                'label' => 'Site Name',
                'value' => $settingsFromDb->get('site_name')?->value ?? config('app.name', 'BideshGomon'), // Default to config then hardcoded
                'type' => 'text', // Input type for Vue
                'validation' => 'required|string|max:100', // Basic validation rule
            ],
            'contact_email' => [
                'label' => 'Contact Email',
                'value' => $settingsFromDb->get('contact_email')?->value ?? '',
                'type' => 'email',
                 'validation' => 'nullable|email|max:100',
            ],
            'contact_phone' => [
                'label' => 'Contact Phone',
                'value' => $settingsFromDb->get('contact_phone')?->value ?? '',
                'type' => 'text',
                 'validation' => 'nullable|string|max:50',
            ],
            // Example boolean setting (uncomment and adjust key if needed)
            // 'maintenance_mode' => [
            //     'label' => 'Maintenance Mode',
            //     'value' => filter_var($settingsFromDb->get('maintenance_mode')?->value ?? false, FILTER_VALIDATE_BOOLEAN), // Ensure boolean
            //     'type' => 'checkbox',
            //     'validation' => 'required|boolean',
            // ],
        ];

        // Ensure Admin/Settings/Index.vue exists
        return Inertia::render('Admin/Settings/Index', [
            'settings' => $definedSettings,
        ]);
    }

    /**
     * Update the specified settings in storage.
     * (We will implement the Request validation later)
     */
    public function update(Request $request): RedirectResponse // <-- Changed Request type
    {
        // Basic validation for now - replace with FormRequest later
        $rules = [
            'settings' => 'required|array',
            'settings.site_name.value' => 'required|string|max:100',
            'settings.contact_email.value' => 'nullable|email|max:100',
            'settings.contact_phone.value' => 'nullable|string|max:50',
            // 'settings.maintenance_mode.value' => 'required|boolean', // Example
        ];

        // Validate only the 'value' fields within the nested array
        $validatedData = $request->validate($rules)['settings'];

        foreach ($validatedData as $key => $data) {
             // Ensure Setting model exists and uses 'key' as primary key or has 'key' column
             try {
                Setting::updateOrCreate(
                    ['key' => $key],
                    // Only update the 'value'. Handle boolean correctly.
                    ['value' => is_bool($data['value']) ? ($data['value'] ? '1' : '0') : $data['value']]
                );
            } catch (\Exception $e) {
                 // Log error and redirect back with error
                 Log::error("Error updating setting '{$key}': " . $e->getMessage());
                 return redirect()->route('admin.settings.index')
                                  ->with('error', 'Failed to update settings. Please check the logs.');
            }
        }

        // Forget the cache so it rebuilds on next load
        Cache::forget('app_settings');

        // --- Use Inertia flash message ---
        return redirect()->route('admin.settings.index')
                         ->with('success', 'Settings updated successfully.'); // Use Inertia's built-in flash messaging
    }
}