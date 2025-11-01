<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
// <-- Add this
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator; // <-- Add this
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     * Also handles fetching a single resource for modal editing.
     */
    public function index(Request $request): Response
    {
        $query = Country::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('iso_code', 'like', '%'.$search.'%')
                    ->orWhere('iso_code_3', 'like', '%'.$search.'%');
            });
        }

        // --- NEW LOGIC FOR EDIT MODAL ---
        // Only load the countryToEdit prop when explicitly requested
        $countryToEdit = null;
        if ($request->has('edit_id')) {
            $countryToEdit = Country::find($request->edit_id);
        }

        $countries = $query->orderBy('name')->paginate(10)->withQueryString();

        return Inertia::render('Admin/Countries/Index', [
            'countries' => $countries,
            'filters' => $request->only(['search']),
            'countryToEdit' => $countryToEdit, // <-- ADD THIS PROP
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * This method is no longer used for page rendering,
     * but is kept for route:resource compliance.
     */
    public function create(): Response
    {
        // We now use a modal on the index page
        return $this->index(new Request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'iso_code' => 'required|string|size:2|unique:countries',
            'iso_code_3' => 'required|string|size:3|unique:countries',
            'country_code' => 'nullable|string|max:10',
            'capital' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:255',
            'continent' => 'nullable|string|max:255',
            'subregion' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        Country::create($validated);

        // Redirect back to index
        return Redirect::route('admin.countries.index')->with('success', 'Country created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     * This method is no longer used for page rendering.
     * Data is now fetched by the index() method.
     */
    public function edit(Country $country): Response
    {
        // We now use a modal on the index page
        return $this->index(new Request);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'iso_code' => ['required', 'string', 'size:2', Rule::unique('countries')->ignore($country->id)],
            'iso_code_3' => ['required', 'string', 'size:3', Rule::unique('countries')->ignore($country->id)],
            'country_code' => 'nullable|string|max:10',
            'capital' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:255',
            'continent' => 'nullable|string|max:255',
            'subregion' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $country->update($validated);

        // Redirect back to index
        return Redirect::route('admin.countries.index')->with('success', 'Country updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country)
    {
        $country->delete();

        return Redirect::route('admin.countries.index')->with('success', 'Country deleted successfully.');
    }

    /**
     * Handle bulk CSV upload for countries.
     */
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        // Read the file
        $fileHandle = fopen($path, 'r');

        // Skip header row
        $header = fgetcsv($fileHandle);
        $headerMap = array_flip($header); // Map header names to indices

        $countriesToInsert = [];
        $validationErrors = [];
        $rowNumber = 1; // Start from 1 for user-facing error

        // Define expected headers
        $requiredHeaders = ['name', 'iso_code', 'iso_code_3'];
        foreach ($requiredHeaders as $requiredHeader) {
            if (! in_array($requiredHeader, $header)) {
                return Redirect::route('admin.countries.index')
                    ->with('error', "CSV file is missing required header: $requiredHeader");
            }
        }

        while (($row = fgetcsv($fileHandle)) !== false) {
            $rowNumber++;
            $data = [];
            foreach ($headerMap as $name => $index) {
                if (isset($row[$index])) {
                    $data[$name] = $row[$index];
                }
            }

            // Map CSV columns to model fields
            $mappedData = [
                'name' => $data['name'] ?? null,
                'iso_code' => $data['iso_code'] ?? null,
                'iso_code_3' => $data['iso_code_3'] ?? null,
                'country_code' => $data['country_code'] ?? null,
                'capital' => $data['capital'] ?? null,
                'currency' => $data['currency'] ?? null,
                'continent' => $data['continent'] ?? ($data['region'] ?? null), // Accept 'region' as 'continent'
                'subregion' => $data['subregion'] ?? null,
                'nationality' => $data['nationality'] ?? null,
                'is_active' => isset($data['is_active']) ? filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN) : true,
            ];

            // Validate the row data
            $validator = Validator::make($mappedData, [
                'name' => 'required|string|max:255',
                'iso_code' => 'required|string|size:2',
                'iso_code_3' => 'required|string|size:3',
                'is_active' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                $validationErrors[] = "Row $rowNumber: ".implode(', ', $validator->errors()->all());
            } else {
                $countriesToInsert[] = $validator->validated();
            }
        }

        fclose($fileHandle);

        if (count($validationErrors) > 0) {
            // If there are validation errors, return them
            return Redirect::route('admin.countries.index')
                ->with('error', 'CSV import failed. Please check these errors:')
                ->with('validation_errors', $validationErrors);
        }

        if (count($countriesToInsert) > 0) {
            try {
                // Use upsert to insert or update on duplicate 'iso_code'
                Country::upsert(
                    $countriesToInsert,
                    ['iso_code'], // Unique identifier(s)
                    [ // Columns to update if duplicate is found
                        'name', 'iso_code_3', 'country_code', 'capital',
                        'currency', 'continent', 'subregion', 'nationality', 'is_active',
                    ]
                );

                return Redirect::route('admin.countries.index')
                    ->with('success', count($countriesToInsert).' countries imported successfully.');
            } catch (\Exception $e) {
                return Redirect::route('admin.countries.index')
                    ->with('error', 'An error occurred during import: '.$e->getMessage());
            }
        }

        return Redirect::route('admin.countries.index')->with('success', 'No new countries to import.');
    }
}
