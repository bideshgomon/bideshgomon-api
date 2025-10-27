<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // <-- Use JsonResponse
use Illuminate\Validation\Rule;
use League\Csv\Reader; // <-- For CSV reading
use League\Csv\Statement;
use Illuminate\Support\Facades\DB; // <-- For database transactions
use Illuminate\Support\Facades\Log; // <-- For logging errors
use Illuminate\Support\Facades\Validator; // <-- For manual validation in bulk

class CountryController extends Controller
{
    /**
     * Display a listing of the resource. (READ - API version)
     * Reusing logic from PageController for consistency, returns JSON.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Country::query()->orderBy('name', 'asc');

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('iso2', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('iso3', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('phone_code', 'LIKE', "%{$searchTerm}%");
            });
        }

        $countries = $query->paginate(25)->withQueryString(); // Match PageController pagination

        return response()->json($countries);
    }

    /**
     * Store a newly created resource in storage. (CREATE)
     */
    public function store(Request $request): JsonResponse // <-- Changed return type
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:countries',
            'iso2' => 'required|string|size:2|unique:countries',
            'iso3' => 'required|string|size:3|unique:countries',
            'phone_code' => 'nullable|string|max:20', // Increased size
            'capital' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:50', // Increased size
            'region' => 'nullable|string|max:255',
            // 'is_active' => 'required|boolean', // Assuming is_active is handled by default in migration/model
        ]);

        // Add default is_active if not present
        $validated['is_active'] = $validated['is_active'] ?? true;

        $country = Country::create($validated);

        return response()->json($country, 201); // 201 Created
    }

    /**
     * Display the specified resource. (READ - Not typically needed if handled by edit page)
     * Kept for API completeness if direct fetch by ID is needed.
     */
    public function show(Country $country): JsonResponse // <-- Changed return type
    {
        return response()->json($country);
    }

    /**
     * Update the specified resource in storage. (UPDATE)
     */
    public function update(Request $request, Country $country): JsonResponse // <-- Changed return type
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('countries')->ignore($country->id)],
            'iso2' => ['required', 'string', 'size:2', Rule::unique('countries')->ignore($country->id)],
            'iso3' => ['required', 'string', 'size:3', Rule::unique('countries')->ignore($country->id)],
            'phone_code' => 'nullable|string|max:20',
            'capital' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:50',
            'region' => 'nullable|string|max:255',
             // 'is_active' => 'required|boolean',
        ]);

         // Add default is_active if not present
        $validated['is_active'] = $validated['is_active'] ?? $country->is_active; // Keep existing if not sent

        $country->update($validated);

        return response()->json($country); // 200 OK (default)
    }

    /**
     * Remove the specified resource from storage. (DELETE)
     */
    public function destroy(Country $country): JsonResponse // <-- Changed return type
    {
         // Add check: Prevent deletion if related records exist? (e.g., universities)
         // if ($country->universities()->exists()) {
         //     return response()->json(['message' => 'Cannot delete country with associated universities.'], 409); // 409 Conflict
         // }

        try {
            $country->delete();
            // Invalidate cache if you implement caching later
            return response()->json(null, 204); // 204 No Content
        } catch (\Exception $e) {
            Log::error('Error deleting country: '.$country->id, ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to delete country.'], 500);
        }
    }

    /**
     * Handle bulk upload of countries via CSV. (BULK CREATE/UPDATE)
     */
    public function bulkUpload(Request $request): JsonResponse // <-- Implemented method
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120', // 5MB max CSV
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        try {
            $csv = Reader::createFromPath($path, 'r');
            $csv->setHeaderOffset(0); // Assumes first row is header

            // Define expected headers (adjust case/names as needed)
            $expectedHeaders = ['name', 'iso2', 'iso3', 'phone_code', 'capital', 'currency', 'region'];
            $actualHeaders = array_map('strtolower', array_map('trim', $csv->getHeader())); // Normalize headers

            // Check if all expected headers are present
            if (count(array_diff($expectedHeaders, $actualHeaders)) > 0) {
                 return response()->json(['message' => 'CSV file is missing required columns. Expected: ' . implode(', ', $expectedHeaders)], 422);
            }

            $records = Statement::create()->process($csv);
            $errors = [];
            $successCount = 0;

            DB::beginTransaction();

            foreach ($records as $index => $record) {
                // Normalize keys in the record
                 $normalizedRecord = [];
                 foreach ($record as $key => $value) {
                     $normalizedRecord[strtolower(trim($key))] = trim($value);
                 }

                 // Basic validation rules for each row
                 $validator = Validator::make($normalizedRecord, [
                    'name' => 'required|string|max:255',
                    'iso2' => 'required|string|size:2',
                    'iso3' => 'required|string|size:3',
                    'phone_code' => 'nullable|string|max:20',
                    'capital' => 'nullable|string|max:255',
                    'currency' => 'nullable|string|max:50',
                    'region' => 'nullable|string|max:255',
                 ]);

                 if ($validator->fails()) {
                     $errors[] = "Row " . ($index + 1) . ": " . implode('; ', $validator->errors()->all());
                     continue; // Skip this row
                 }

                 // Use updateOrCreate based on iso2 or iso3 code to handle duplicates/updates
                 Country::updateOrCreate(
                    [
                        'iso2' => $normalizedRecord['iso2'] // Use a unique identifier
                    ],
                    [
                        'name' => $normalizedRecord['name'],
                        'iso3' => $normalizedRecord['iso3'],
                        'phone_code' => $normalizedRecord['phone_code'] ?: null,
                        'capital' => $normalizedRecord['capital'] ?: null,
                        'currency' => $normalizedRecord['currency'] ?: null,
                        'region' => $normalizedRecord['region'] ?: null,
                        'is_active' => true, // Default to active on upload
                    ]
                );
                $successCount++;
            }

            if (!empty($errors)) {
                 DB::rollBack(); // Rollback if any row had errors
                 return response()->json([
                     'message' => 'Upload failed due to validation errors in some rows.',
                     'errors' => $errors,
                     'processed_count' => $successCount + count($errors),
                     'success_count' => $successCount,
                 ], 422);
            }

            DB::commit(); // Commit if all rows are valid

            // Invalidate cache if implemented
            // Cache::forget('all_countries');

            return response()->json([
                'message' => "Successfully processed {$successCount} countries.",
                'success_count' => $successCount,
            ]);

        } catch (\League\Csv\Exception $e) {
            Log::error('CSV Processing Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error reading CSV file. Ensure it is correctly formatted.'], 422);
        } catch (\Exception $e) {
             DB::rollBack();
             Log::error('Country Bulk Upload Error: ' . $e->getMessage());
             return response()->json(['message' => 'An unexpected error occurred during bulk upload.'], 500);
        }
    }
}