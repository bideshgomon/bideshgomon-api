<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UniversityController extends Controller
{
    /**
     * Display a listing of the universities.
     */
    public function index()
    {
        // Eager load the 'country' relationship
        $universities = University::with('country')->latest()->paginate(10);

        // ✅ Return an Inertia view (not JSON)
        return Inertia::render('Admin/Universities/Index', [
            'universities' => $universities,
        ]);
    }

    /**
     * Store a new university.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:universities',
            'country_id' => 'required|exists:countries,id',
            'city' => 'required|string|max:255',
            'website_url' => 'nullable|url',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // 2MB max
            'intake_months' => 'nullable|array',
            'ranking' => 'nullable|integer',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            // Store in 'storage/app/public/university-logos'
            $logoPath = $request->file('logo')->store('university-logos', 'public');
        }

        University::create([
            'name' => $validated['name'],
            'country_id' => $validated['country_id'],
            'city' => $validated['city'],
            'website_url' => $validated['website_url'] ?? null,
            'logo_path' => $logoPath,
            'intake_months' => $validated['intake_months'] ?? [],
            'ranking' => $validated['ranking'] ?? null,
        ]);

        // ✅ Instead of JSON, return redirect for Inertia
        return redirect()
            ->route('admin.universities.index')
            ->with('success', 'University created successfully!');
    }

    /**
     * Display the specified university.
     */
    public function show(University $university)
    {
        // ✅ Return Inertia page instead of JSON
        return Inertia::render('Admin/Universities/Show', [
            'university' => $university->load('country', 'courses'),
        ]);
    }

    /**
     * Update the specified university.
     */
    public function update(Request $request, University $university)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('universities')->ignore($university->id)],
            'country_id' => 'required|exists:countries,id',
            'city' => 'required|string|max:255',
            'website_url' => 'nullable|url',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // 2MB max
            'intake_months' => 'nullable|array',
            'ranking' => 'nullable|integer',
        ]);

        $data = $validated;

        if ($request->hasFile('logo')) {
            // Delete the old logo if it exists
            if ($university->logo_path) {
                Storage::disk('public')->delete($university->logo_path);
            }
            // Store the new logo
            $data['logo_path'] = $request->file('logo')->store('university-logos', 'public');
        }

        $university->update($data);

        // ✅ Redirect for Inertia
        return redirect()
            ->route('admin.universities.index')
            ->with('success', 'University updated successfully!');
    }

    /**
     * Remove the specified university.
     */
    public function destroy(University $university)
    {
        // Delete the logo from storage
        if ($university->logo_path) {
            Storage::disk('public')->delete($university->logo_path);
        }

        $university->delete();

        // ✅ Redirect back instead of JSON
        return redirect()
            ->back()
            ->with('success', 'University deleted successfully!');
    }
}
