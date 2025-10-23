<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource. (READ)
     */
    public function index(Request $request)
    {
        // Add pagination and search
        $query = Country::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('iso_code', 'like', '%' . $request->search . '%');
        }

        return $query->orderBy('name')->paginate(10);
    }

    /**
     * Store a newly created resource in storage. (CREATE)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'iso_code' => 'required|string|size:2|unique:countries',
            'iso_code_3' => 'required|string|size:3|unique:countries',
            'country_code' => 'nullable|string|max:10',
            'continent' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $country = Country::create($validated);

        return response()->json($country, 201);
    }

    /**
     * Display the specified resource. (READ)
     */
    public function show(Country $country)
    {
        return $country;
    }

    /**
     * Update the specified resource in storage. (UPDATE)
     */
    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'iso_code' => ['required', 'string', 'size:2', Rule::unique('countries')->ignore($country->id)],
            'iso_code_3' => ['required', 'string', 'size:3', Rule::unique('countries')->ignore($country->id)],
            'country_code' => 'nullable|string|max:10',
            'continent' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $country->update($validated);

        return response()->json($country);
    }

    /**
     * Remove the specified resource from storage. (DELETE)
     */
    public function destroy(Country $country)
    {
        $country->delete();

        return response()->json(null, 204); // 204 No Content
    }

    /**
     * Placeholder for bulk upload feature.
     */
    public function bulkUpload(Request $request)
    {
        // We will implement this in a future step
        // This will involve installing a package like Maatwebsite/Excel
        // and processing a CSV or Excel file.
        
        return response()->json(['message' => 'Bulk upload endpoint is ready.']);
    }
}