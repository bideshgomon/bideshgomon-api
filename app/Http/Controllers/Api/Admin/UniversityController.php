<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UniversityController extends Controller
{
    /**
     * Display a listing of the universities.
     */
    public function index()
    {
        // Eager load the 'country' relationship
        return University::with('country')->latest()->paginate(10);
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

        $university = University::create([
            'name' => $validated['name'],
            'country_id' => $validated['country_id'],
            'city' => $validated['city'],
            'website_url' => $validated['website_url'] ?? null,
            'logo_path' => $logoPath,
            'intake_months' => $validated['intake_months'] ?? [],
            'ranking' => $validated['ranking'] ?? null,
        ]);

        return response()->json($university->load('country'), 201);
    }

    /**
     * Display the specified university.
     */
    public function show(University $university)
    {
        // Load relationships for detailed view
        return $university->load('country', 'courses');
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

        return response()->json($university->load('country'), 200);
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

        // Delete the university record (courses will cascade if DB is set up)
        $university->delete();

        return response()->json(null, 204); // No Content
    }
}
