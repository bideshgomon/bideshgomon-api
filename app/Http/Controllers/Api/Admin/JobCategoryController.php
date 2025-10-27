<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // <-- Import JsonResponse
use Illuminate\Validation\Rule;
use Illuminate\Support\Str; // <-- Import Str facade
use League\Csv\Reader; // <-- For CSV reading
use League\Csv\Statement;
use Illuminate\Support\Facades\DB; // <-- For database transactions
use Illuminate\Support\Facades\Log; // <-- For logging errors
use Illuminate\Support\Facades\Validator; // <-- For manual validation in bulk

class JobCategoryController extends Controller
{
    /**
     * Display a listing of the resource. (API version)
     */
    public function index(Request $request): JsonResponse
    {
        $query = JobCategory::query()->orderBy('name', 'asc');

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'LIKE', "%{$searchTerm}%");
        }

        $jobCategories = $query->paginate(25)->withQueryString();

        return response()->json($jobCategories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse // <-- Return JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:job_categories,name',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean', // Get from form
        ]);

        // Generate slug from name
        $validated['slug'] = $this->generateUniqueSlug($validated['name']);

        $category = JobCategory::create($validated);

        return response()->json($category, 201); // 201 Created
    }

    /**
     * Display the specified resource.
     */
    public function show(JobCategory $jobCategory): JsonResponse // <-- Return JsonResponse
    {
        return response()->json($jobCategory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JobCategory $jobCategory): JsonResponse // <-- Return JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255', Rule::unique('job_categories')->ignore($jobCategory->id)],
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        // Re-generate slug if name changed
        if ($jobCategory->name !== $validated['name']) {
            $validated['slug'] = $this->generateUniqueSlug($validated['name'], $jobCategory->id);
        }

        $jobCategory->update($validated);

        return response()->json($jobCategory);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobCategory $jobCategory): JsonResponse // <-- Return JsonResponse
    {
        // Check if category is in use by job postings
        if ($jobCategory->jobPostings()->exists()) {
             return response()->json(['message' => 'Cannot delete category. It is in use by job postings.'], 409); // 409 Conflict
        }

        try {
            $jobCategory->delete();
            return response()->json(null, 204); // 204 No Content
        } catch (\Exception $e) {
            Log::error('Error deleting job category: '.$jobCategory->id, ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to delete job category.'], 500);
        }
    }

    /**
     * Handle bulk upload of job categories via CSV.
     */
    public function bulkUpload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120', // 5MB max CSV
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        try {
            $csv = Reader::createFromPath($path, 'r');
            $csv->setHeaderOffset(0); // Assumes first row is header

            $requiredHeaders = ['name'];
            $optionalHeaders = ['description'];
            $actualHeaders = array_map('strtolower', array_map('trim', $csv->getHeader()));

            if (!in_array('name', $actualHeaders)) {
                 return response()->json(['message' => 'CSV file is missing required column: name'], 422);
            }

            $records = Statement::create()->process($csv);
            $errors = [];
            $successCount = 0;

            DB::beginTransaction();

            foreach ($records as $index => $record) {
                 // Normalize keys
                 $normalizedRecord = [];
                 foreach ($record as $key => $value) {
                     $normalizedRecord[strtolower(trim($key))] = trim($value);
                 }

                 $name = $normalizedRecord['name'] ?? null;
                 $description = $normalizedRecord['description'] ?? null;

                 if (empty($name)) {
                     $errors[] = "Row ".($index + 1).": 'name' column is empty.";
                     continue;
                 }

                 $slug = $this->generateUniqueSlug($name);

                 // Use updateOrCreate: Find category by name (case-insensitive)
                 JobCategory::updateOrCreate(
                    [
                        // Use a case-insensitive check if your DB supports it, or normalize before check
                        // 'name' => $name // Simple check
                         DB::raw('LOWER(name)') => strtolower($name) // Case-insensitive check (example for MySQL/PostgreSQL)
                    ],
                    [
                        'name' => $name, // Store original case
                        'slug' => $slug,
                        'description' => $description,
                        'is_active' => true,
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
                'message' => "Successfully processed {$successCount} categories.",
                'success_count' => $successCount,
            ]);

        } catch (\League\Csv\Exception $e) {
            Log::error('Job Category CSV Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error reading CSV file. Ensure it is correctly formatted.'], 422);
        } catch (\Exception $e) {
             DB::rollBack();
             Log::error('Job Category Bulk Upload Error: ' . $e->getMessage());
             return response()->json(['message' => 'An unexpected error occurred during bulk upload.'], 500);
        }
    }

    /**
     * Helper function to generate a unique slug.
     */
    private function generateUniqueSlug(string $name, int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        // Base query
        $query = JobCategory::where('slug', $slug);

        // If updating, ignore the current model's ID
        if ($ignoreId !== null) {
            $query->where('id', '!=', $ignoreId);
        }

        // Check if slug exists
        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter++;
            // Re-build query for the next check
            $query = JobCategory::where('slug', $slug);
             if ($ignoreId !== null) {
                $query->where('id', '!=', $ignoreId);
            }
        }

        return $slug;
    }
}