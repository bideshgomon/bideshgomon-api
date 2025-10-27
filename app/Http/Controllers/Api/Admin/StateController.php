<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\State;
use App\Models\Country; // Import Country model
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // <-- Use JsonResponse
use Illuminate\Validation\Rule;
use League\Csv\Reader; // <-- For CSV reading
use League\Csv\Statement;
use Illuminate\Support\Facades\DB; // <-- For database transactions
use Illuminate\Support\Facades\Log; // <-- For logging errors
use Illuminate\Support\Facades\Validator; // <-- For manual validation in bulk

class StateController extends Controller
{
    /**
     * Display a listing of the resource. (READ - API version)
     * Returns JSON for the Vue component.
     */
    public function index(Request $request): JsonResponse
    {
        // Build query for states, eager load country
        $query = State::query()->with('country')->orderBy('name', 'asc');

        // Filter by Country ID
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->input('country_id'));
        }

        // Filter by Search Term (State Name)
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'LIKE', "%{$searchTerm}%");
        }

        // Paginate results
        $states = $query->paginate(25)->withQueryString(); // Match PageController pagination

        return response()->json($states);
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
                // Unique state name *within the same country*
                Rule::unique('states')->where(function ($query) use ($request) {
                    return $query->where('country_id', $request->input('country_id'));
                }),
            ],
            'country_id' => 'required|exists:countries,id',
            // 'is_active' => 'sometimes|boolean', // Assuming default is true
        ]);

        // Add default is_active if needed
        $validated['is_active'] = $validated['is_active'] ?? true;

        $state = State::create($validated);

        return response()->json($state->load('country'), 201); // 201 Created
    }

    /**
     * Update the specified resource in storage. (UPDATE)
     */
    public function update(Request $request, State $state): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Unique state name *within the same country*, ignoring self
                Rule::unique('states')->where(function ($query) use ($request) {
                    return $query->where('country_id', $request->input('country_id'));
                })->ignore($state->id),
            ],
            'country_id' => 'required|exists:countries,id',
             // 'is_active' => 'sometimes|boolean',
        ]);

        // Add default is_active if needed
        $validated['is_active'] = $validated['is_active'] ?? $state->is_active;

        $state->update($validated);

        return response()->json($state->load('country')); // 200 OK (default)
    }

    /**
     * Remove the specified resource from storage. (DELETE)
     */
    public function destroy(State $state): JsonResponse
    {
        // Add check: Prevent deletion if related records exist? (e.g., cities)
        // if ($state->cities()->exists()) {
        //     return response()->json(['message' => 'Cannot delete state with associated cities.'], 409); // 409 Conflict
        // }

        try {
            $state->delete();
            return response()->json(null, 204); // 204 No Content
        } catch (\Exception $e) {
            Log::error('Error deleting state: '.$state->id, ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to delete state.'], 500);
        }
    }

    /**
     * Handle bulk upload of states via CSV. (BULK CREATE/UPDATE)
     */
    public function bulkUpload(Request $request): JsonResponse
    {
        $validatedRequest = $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120', // 5MB max CSV
            'country_id_context' => 'nullable|exists:countries,id', // Optional country ID context
        ]);

        $file = $validatedRequest['file'];
        $countryIdContext = $validatedRequest['country_id_context'] ?? null;
        $path = $file->getRealPath();

        try {
            $csv = Reader::createFromPath($path, 'r');
            $csv->setHeaderOffset(0); // Assumes first row is header

            // Determine required headers based on context
            $requiredHeaders = ['name'];
            if (!$countryIdContext) {
                $requiredHeaders[] = 'country_iso2'; // Require country code if no context
            }
            $actualHeaders = array_map('strtolower', array_map('trim', $csv->getHeader()));

            // Check if all required headers are present
            if (count(array_diff($requiredHeaders, $actualHeaders)) > 0) {
                 return response()->json(['message' => 'CSV file is missing required columns. Expected: ' . implode(', ', $requiredHeaders)], 422);
            }

            $records = Statement::create()->process($csv);
            $errors = [];
            $successCount = 0;
            $countriesCache = []; // Cache country lookups

            DB::beginTransaction();

            foreach ($records as $index => $record) {
                 // Normalize keys
                 $normalizedRecord = [];
                 foreach ($record as $key => $value) {
                     $normalizedRecord[strtolower(trim($key))] = trim($value);
                 }

                 $countryId = $countryIdContext; // Use context if provided

                 // If no context, find country by ISO2 code from CSV
                 if (!$countryId) {
                     $iso2 = $normalizedRecord['country_iso2'] ?? null;
                     if (empty($iso2)) {
                         $errors[] = "Row " . ($index + 1) . ": Missing 'country_iso2' column.";
                         continue;
                     }
                     // Cache country lookup for performance
                     if (!isset($countriesCache[$iso2])) {
                         $countriesCache[$iso2] = Country::where('iso2', strtoupper($iso2))->value('id');
                     }
                     $countryId = $countriesCache[$iso2];
                     if (!$countryId) {
                         $errors[] = "Row " . ($index + 1) . ": Country with ISO2 code '{$iso2}' not found.";
                         continue;
                     }
                 }

                 // Basic validation for state name
                 $validator = Validator::make($normalizedRecord, [
                    'name' => 'required|string|max:255',
                 ]);

                 if ($validator->fails()) {
                     $errors[] = "Row " . ($index + 1) . ": " . implode('; ', $validator->errors()->all());
                     continue;
                 }

                 // Use updateOrCreate: Find state by name *within the specific country*
                 State::updateOrCreate(
                    [
                        'country_id' => $countryId,
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
                'message' => "Successfully processed {$successCount} states.",
                'success_count' => $successCount,
            ]);

        } catch (\League\Csv\Exception $e) {
            Log::error('State CSV Processing Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error reading CSV file. Ensure it is correctly formatted.'], 422);
        } catch (\Exception $e) {
             DB::rollBack();
             Log::error('State Bulk Upload Error: ' . $e->getMessage());
             return response()->json(['message' => 'An unexpected error occurred during bulk upload.'], 500);
        }
    }
}