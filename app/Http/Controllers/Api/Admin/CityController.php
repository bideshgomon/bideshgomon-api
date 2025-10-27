<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\State; // Import State model
use App\Models\Country; // Import Country model for lookups
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // <-- Use JsonResponse
use Illuminate\Validation\Rule;
use League\Csv\Reader; // <-- For CSV reading
use League\Csv\Statement;
use Illuminate\Support\Facades\DB; // <-- For database transactions
use Illuminate\Support\Facades\Log; // <-- For logging errors
use Illuminate\Support\Facades\Validator; // <-- For manual validation in bulk

class CityController extends Controller
{
    /**
     * Display a listing of the resource. (READ - API version)
     * Filterable by state_id and country_id. Returns JSON.
     */
    public function index(Request $request): JsonResponse
    {
        // Build query for cities, eager load state and state's country
        $query = City::query()->with('state.country')->orderBy('name', 'asc');

        // Filter by State ID
        if ($request->filled('state_id')) {
            $query->where('state_id', $request->input('state_id'));
        }

        // Filter by Country ID (via state relationship)
        if ($request->filled('country_id')) {
            $query->whereHas('state', function ($q) use ($request) {
                $q->where('country_id', $request->input('country_id'));
            });
        }

        // Filter by Search Term (City Name)
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'LIKE', "%{$searchTerm}%");
        }

        // Paginate results
        $cities = $query->paginate(25)->withQueryString(); // Match PageController pagination

        return response()->json($cities);
    }

    /**
     * Store a newly created resource in storage. (CREATE)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Unique city name *within the same state*
                Rule::unique('cities')->where(function ($query) use ($request) {
                    return $query->where('state_id', $request->input('state_id'));
                }),
            ],
            'state_id' => 'required|exists:states,id',
            // 'is_active' => 'sometimes|boolean', // Assuming default is true
        ]);

        // Add default is_active if needed
        $validated['is_active'] = $validated['is_active'] ?? true;

        $city = City::create($validated);

        return response()->json($city->load('state.country'), 201); // Load relationships
    }

    /**
     * Update the specified resource in storage. (UPDATE)
     */
    public function update(Request $request, City $city): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Unique city name *within the same state*, ignoring self
                Rule::unique('cities')->where(function ($query) use ($request) {
                    return $query->where('state_id', $request->input('state_id'));
                })->ignore($city->id),
            ],
            'state_id' => 'required|exists:states,id',
            // 'is_active' => 'sometimes|boolean',
        ]);

        // Add default is_active if needed
        $validated['is_active'] = $validated['is_active'] ?? $city->is_active;

        $city->update($validated);

        return response()->json($city->load('state.country')); // Load relationships
    }

    /**
     * Remove the specified resource from storage. (DELETE)
     */
    public function destroy(City $city): JsonResponse
    {
        // Add check: Prevent deletion if related records exist? (e.g., universities, user profiles)
        // if ($city->universities()->exists() || $city->userProfiles()->exists()) {
        //     return response()->json(['message' => 'Cannot delete city with associated records.'], 409); // 409 Conflict
        // }

        try {
            $city->delete();
            return response()->json(null, 204); // 204 No Content
        } catch (\Exception $e) {
            Log::error('Error deleting city: '.$city->id, ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to delete city.'], 500);
        }
    }

    /**
     * Handle bulk upload of cities via CSV. (BULK CREATE/UPDATE)
     */
    public function bulkUpload(Request $request): JsonResponse
    {
        $validatedRequest = $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120', // 5MB max CSV
            'state_id_context' => 'nullable|exists:states,id', // Optional state ID context
            'country_id_context' => 'nullable|exists:countries,id', // Optional country ID context (if state context not used)
        ]);

        $file = $validatedRequest['file'];
        $stateIdContext = $validatedRequest['state_id_context'] ?? null;
        $countryIdContext = $validatedRequest['country_id_context'] ?? null; // For state lookup if state context is absent
        $path = $file->getRealPath();

        try {
            $csv = Reader::createFromPath($path, 'r');
            $csv->setHeaderOffset(0); // Assumes first row is header

            // Determine required headers based on context
            $requiredHeaders = ['name'];
            if (!$stateIdContext) {
                $requiredHeaders[] = 'state_name'; // Require state name if no state context
                if (!$countryIdContext) {
                    $requiredHeaders[] = 'country_iso2'; // Require country code if no state/country context
                }
            }
            $actualHeaders = array_map('strtolower', array_map('trim', $csv->getHeader()));

            // Check if all required headers are present
            if (count(array_diff($requiredHeaders, $actualHeaders)) > 0) {
                 return response()->json(['message' => 'CSV file is missing required columns. Expected: ' . implode(', ', $requiredHeaders)], 422);
            }

            $records = Statement::create()->process($csv);
            $errors = [];
            $successCount = 0;
            $statesCache = []; // Cache state lookups if needed

            DB::beginTransaction();

            foreach ($records as $index => $record) {
                 // Normalize keys
                 $normalizedRecord = [];
                 foreach ($record as $key => $value) {
                     $normalizedRecord[strtolower(trim($key))] = trim($value);
                 }

                 $stateId = $stateIdContext; // Use context if provided

                 // If no state context, find state by name + country_iso2/context
                 if (!$stateId) {
                     $stateName = $normalizedRecord['state_name'] ?? null;
                     if (empty($stateName)) {
                         $errors[] = "Row " . ($index + 1) . ": Missing 'state_name' column.";
                         continue;
                     }

                     $countryIdForLookup = $countryIdContext;
                     if (!$countryIdForLookup) {
                         $iso2 = $normalizedRecord['country_iso2'] ?? null;
                         if (empty($iso2)) {
                             $errors[] = "Row " . ($index + 1) . ": Missing 'country_iso2' column (required when State and Country context are not set).";
                             continue;
                         }
                         $country = Country::where('iso2', strtoupper($iso2))->first(['id']);
                         if (!$country) {
                             $errors[] = "Row " . ($index + 1) . ": Country with ISO2 code '{$iso2}' not found.";
                             continue;
                         }
                         $countryIdForLookup = $country->id;
                     }

                     // Cache state lookup (composite key: countryId_stateName)
                     $cacheKey = $countryIdForLookup . '_' . $stateName;
                     if (!isset($statesCache[$cacheKey])) {
                         $statesCache[$cacheKey] = State::where('country_id', $countryIdForLookup)
                                                        ->where('name', $stateName)
                                                        ->value('id');
                     }
                     $stateId = $statesCache[$cacheKey];

                     if (!$stateId) {
                         $errors[] = "Row " . ($index + 1) . ": State '{$stateName}' not found in the specified country.";
                         continue;
                     }
                 }

                 // Basic validation for city name
                 $validator = Validator::make($normalizedRecord, [
                    'name' => 'required|string|max:255',
                 ]);

                 if ($validator->fails()) {
                     $errors[] = "Row " . ($index + 1) . ": " . implode('; ', $validator->errors()->all());
                     continue;
                 }

                 // Use updateOrCreate: Find city by name *within the specific state*
                 City::updateOrCreate(
                    [
                        'state_id' => $stateId,
                        'name' => $normalizedRecord['name'] // Check combination
                    ],
                    [
                        'is_active' => true, // Default to active on upload
                    ]
                );
                $successCount++;
            }

            if (!empty($errors)) {
                 DB::rollBack();
                 return response()->json([
                     'message' => 'Upload failed due to errors in some rows.',
                     'errors' => $errors,
                     'processed_count' => $successCount + count($errors),
                     'success_count' => $successCount,
                 ], 422);
            }

            DB::commit();

            return response()->json([
                'message' => "Successfully processed {$successCount} cities.",
                'success_count' => $successCount,
            ]);

        } catch (\League\Csv\Exception $e) {
            Log::error('City CSV Processing Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error reading CSV file. Ensure it is correctly formatted.'], 422);
        } catch (\Exception $e) {
             DB::rollBack();
             Log::error('City Bulk Upload Error: ' . $e->getMessage());
             return response()->json(['message' => 'An unexpected error occurred during bulk upload.'], 500);
        }
    }
}