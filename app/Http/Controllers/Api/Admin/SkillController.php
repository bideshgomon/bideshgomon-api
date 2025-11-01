<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use League\Csv\Reader;
use League\Csv\Statement; // Import Str facade

class SkillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Skill::query()->orderBy('name', 'asc');

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('category', 'LIKE', "%{$searchTerm}%");
            });
        }

        $skills = $query->paginate(25)->withQueryString();

        return response()->json($skills);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:skills,name',
            'category' => 'nullable|string|max:255',
        ]);

        // Generate slug from name
        $validated['slug'] = Str::slug($validated['name']);

        // Check if slug is unique, if not, append a hash
        if (Skill::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $validated['slug'].'-'.strtolower(Str::random(4));
        }

        $skill = Skill::create($validated);

        return response()->json($skill, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Skill $skill): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('skills')->ignore($skill->id)],
            'category' => 'nullable|string|max:255',
        ]);

        // Re-generate slug if name changed
        if ($skill->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
            if (Skill::where('slug', $validated['slug'])->where('id', '!=', $skill->id)->exists()) {
                $validated['slug'] = $validated['slug'].'-'.strtolower(Str::random(4));
            }
        }

        $skill->update($validated);

        return response()->json($skill);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Skill $skill): JsonResponse
    {
        // Add check: Prevent deletion if related records exist (e.g., user_skill pivot)
        if ($skill->users()->exists()) {
            return response()->json(['message' => 'Cannot delete skill. It is currently in use by one or more users.'], 409); // 409 Conflict
        }

        try {
            $skill->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error('Error deleting skill: '.$skill->id, ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Failed to delete skill.'], 500);
        }
    }

    /**
     * Handle bulk upload of skills via CSV.
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
            $optionalHeaders = ['category'];
            $actualHeaders = array_map('strtolower', array_map('trim', $csv->getHeader()));

            if (! in_array('name', $actualHeaders)) {
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
                $category = $normalizedRecord['category'] ?? null;

                if (empty($name)) {
                    $errors[] = 'Row '.($index + 1).": 'name' column is empty.";

                    continue;
                }

                $slug = Str::slug($name);
                $originalSlug = $slug;
                $counter = 1;

                // Ensure slug is unique
                while (Skill::where('slug', $slug)->exists()) {
                    $slug = $originalSlug.'-'.$counter++;
                }

                // Use updateOrCreate: Find skill by name (case-insensitive)
                Skill::updateOrCreate(
                    [
                        'name' => $name,
                    ],
                    [
                        'slug' => $slug, // Set the generated unique slug
                        'category' => $category,
                    ]
                );
                $successCount++;
            }

            if (! empty($errors)) {
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
                'message' => "Successfully processed {$successCount} skills.",
                'success_count' => $successCount,
            ]);

        } catch (\League\Csv\Exception $e) {
            Log::error('Skill CSV Processing Error: '.$e->getMessage());

            return response()->json(['message' => 'Error reading CSV file. Ensure it is correctly formatted.'], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Skill Bulk Upload Error: '.$e->getMessage());

            return response()->json(['message' => 'An unexpected error occurred during bulk upload.'], 500);
        }
    }
}
