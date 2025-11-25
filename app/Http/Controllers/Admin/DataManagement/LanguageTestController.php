<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Traits\BulkUploadable;
use App\Models\Language;
use App\Models\LanguageTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class LanguageTestController extends Controller
{
    use BulkUploadable;

    protected $entityName = 'Language Test';
    protected $entityNamePlural = 'Language Tests';
    protected $indexRoute = 'admin.data.language-tests.index';
    protected $bulkUploadView = 'Admin/DataManagement/LanguageTests/BulkUpload';

    /**
     * Display a listing of language tests
     */
    public function index(Request $request)
    {
        $query = LanguageTest::with('language');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_bn', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('language', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by language
        if ($request->filled('language_id')) {
            $query->where('language_id', $request->language_id);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        // Sort
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $languageTests = $query->paginate($request->get('per_page', 15))
            ->withQueryString();

        // Get all active languages for filter dropdown
        $languages = Language::active()->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/DataManagement/LanguageTests/Index', [
            'languageTests' => $languageTests,
            'languages' => $languages,
            'filters' => $request->only(['search', 'language_id', 'is_active']),
            'sort' => [
                'field' => $sortField,
                'direction' => $sortDirection,
            ],
        ]);
    }

    /**
     * Show the form for creating a new language test
     */
    public function create()
    {
        $languages = Language::active()->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/DataManagement/LanguageTests/Create', [
            'languages' => $languages,
        ]);
    }

    /**
     * Store a newly created language test
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'language_id' => 'nullable|exists:languages,id',
            'name' => 'required|string|max:100',
            'name_bn' => 'nullable|string|max:100',
            'code' => 'required|string|max:50|unique:language_tests,code',
            'min_score' => 'nullable|numeric|min:0',
            'max_score' => 'nullable|numeric|min:0|gte:min_score',
            'score_type' => 'required|string|in:band,percentage,points,grade',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        try {
            LanguageTest::create($validated);

            return redirect()->route('admin.data.language-tests.index')
                ->with('success', 'Language Test created successfully.');
        } catch (\Exception $e) {
            Log::error('Language Test creation failed', [
                'error' => $e->getMessage(),
                'data' => $validated,
            ]);

            return back()->withInput()->with('error', 'Failed to create language test: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified language test
     */
    public function edit(LanguageTest $languageTest)
    {
        $languages = Language::active()->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/DataManagement/LanguageTests/Edit', [
            'languageTest' => $languageTest->load('language'),
            'languages' => $languages,
        ]);
    }

    /**
     * Update the specified language test
     */
    public function update(Request $request, LanguageTest $languageTest)
    {
        $validated = $request->validate([
            'language_id' => 'nullable|exists:languages,id',
            'name' => 'required|string|max:100',
            'name_bn' => 'nullable|string|max:100',
            'code' => ['required', 'string', 'max:50', Rule::unique('language_tests')->ignore($languageTest->id)],
            'min_score' => 'nullable|numeric|min:0',
            'max_score' => 'nullable|numeric|min:0|gte:min_score',
            'score_type' => 'required|string|in:band,percentage,points,grade',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        try {
            $languageTest->update($validated);

            return redirect()->route('admin.data.language-tests.index')
                ->with('success', 'Language Test updated successfully.');
        } catch (\Exception $e) {
            Log::error('Language Test update failed', [
                'error' => $e->getMessage(),
                'id' => $languageTest->id,
                'data' => $validated,
            ]);

            return back()->withInput()->with('error', 'Failed to update language test: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified language test
     */
    public function destroy(LanguageTest $languageTest)
    {
        try {
            $languageTest->delete();

            return redirect()->route('admin.data.language-tests.index')
                ->with('success', 'Language Test deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Language Test deletion failed', [
                'error' => $e->getMessage(),
                'id' => $languageTest->id,
            ]);

            return back()->with('error', 'Failed to delete language test: ' . $e->getMessage());
        }
    }

    /**
     * Toggle the status of a language test
     */
    public function toggleStatus(LanguageTest $languageTest)
    {
        try {
            $languageTest->update([
                'is_active' => !$languageTest->is_active
            ]);

            return back()->with('success', 'Status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Language Test status toggle failed', [
                'error' => $e->getMessage(),
                'id' => $languageTest->id,
            ]);

            return back()->with('error', 'Failed to update status.');
        }
    }

    /**
     * Get the template columns for CSV upload
     */
    protected function getTemplateColumns(): array
    {
        return [
            'language_code',
            'name',
            'name_bn',
            'code',
            'min_score',
            'max_score',
            'score_type',
            'description',
            'is_active',
        ];
    }

    /**
     * Get the required columns for CSV upload
     */
    protected function getRequiredColumns(): array
    {
        return ['name', 'code', 'score_type'];
    }

    /**
     * Get sample data for CSV template
     */
    protected function getSampleData(): array
    {
        return [
            [
                'language_code' => 'en',
                'name' => 'IELTS',
                'name_bn' => 'আইইএলটিএস',
                'code' => 'ielts',
                'min_score' => '0',
                'max_score' => '9',
                'score_type' => 'band',
                'description' => 'International English Language Testing System',
                'is_active' => 'true',
            ],
            [
                'language_code' => 'en',
                'name' => 'TOEFL',
                'name_bn' => 'টোফেল',
                'code' => 'toefl',
                'min_score' => '0',
                'max_score' => '120',
                'score_type' => 'points',
                'description' => 'Test of English as a Foreign Language',
                'is_active' => 'true',
            ],
            [
                'language_code' => 'en',
                'name' => 'PTE Academic',
                'name_bn' => 'পিটিই একাডেমিক',
                'code' => 'pte',
                'min_score' => '10',
                'max_score' => '90',
                'score_type' => 'points',
                'description' => 'Pearson Test of English Academic',
                'is_active' => 'true',
            ],
            [
                'language_code' => 'bn',
                'name' => 'Bengali Language Test',
                'name_bn' => 'বাংলা ভাষা পরীক্ষা',
                'code' => 'blt',
                'min_score' => '0',
                'max_score' => '100',
                'score_type' => 'percentage',
                'description' => 'Standard Bengali proficiency test',
                'is_active' => 'true',
            ],
        ];
    }

    /**
     * Get validation rules for bulk upload
     */
    protected function getValidationRules(): array
    {
        return [
            'language_id' => 'nullable|exists:languages,id',
            'name' => 'required|string|max:100',
            'name_bn' => 'nullable|string|max:100',
            'code' => 'required|string|max:50|unique:language_tests,code',
            'min_score' => 'nullable|numeric|min:0',
            'max_score' => 'nullable|numeric|min:0',
            'score_type' => 'required|string|in:band,percentage,points,grade',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Validate a single row from CSV
     */
    protected function validateRow(array $row, int $rowNumber): array
    {
        $errors = [];

        // Required fields
        if (empty($row['name'])) {
            $errors[] = "Name is required";
        }
        if (empty($row['code'])) {
            $errors[] = "Code is required";
        }
        if (empty($row['score_type'])) {
            $errors[] = "Score type is required";
        } elseif (!in_array($row['score_type'], ['band', 'percentage', 'points', 'grade'])) {
            $errors[] = "Score type must be one of: band, percentage, points, grade";
        }

        // Language code validation (if provided)
        if (!empty($row['language_code'])) {
            $language = Language::where('code', $row['language_code'])->first();
            if (!$language) {
                $errors[] = "Language with code '{$row['language_code']}' not found";
            }
        }

        // Numeric validations
        if (!empty($row['min_score']) && !is_numeric($row['min_score'])) {
            $errors[] = "Min score must be a number";
        }
        if (!empty($row['max_score']) && !is_numeric($row['max_score'])) {
            $errors[] = "Max score must be a number";
        }
        if (!empty($row['min_score']) && !empty($row['max_score']) && 
            is_numeric($row['min_score']) && is_numeric($row['max_score']) &&
            (float)$row['max_score'] < (float)$row['min_score']) {
            $errors[] = "Max score must be greater than or equal to min score";
        }

        // Duplicate code check
        if (!empty($row['code'])) {
            $exists = LanguageTest::where('code', $row['code'])->exists();
            if ($exists) {
                $errors[] = "Language test with code '{$row['code']}' already exists";
            }
        }

        return $errors;
    }

    /**
     * Transform a row before saving
     */
    protected function transformRowData(array $row): array
    {
        $data = [
            'name' => $row['name'],
            'name_bn' => $row['name_bn'] ?? null,
            'code' => strtolower(trim($row['code'])),
            'min_score' => !empty($row['min_score']) ? (float)$row['min_score'] : null,
            'max_score' => !empty($row['max_score']) ? (float)$row['max_score'] : null,
            'score_type' => $row['score_type'],
            'description' => $row['description'] ?? null,
            'is_active' => isset($row['is_active']) ? 
                (strtolower($row['is_active']) === 'true' || $row['is_active'] === '1') : true,
        ];

        // Get language_id from language_code if provided
        if (!empty($row['language_code'])) {
            $language = Language::where('code', $row['language_code'])->first();
            $data['language_id'] = $language ? $language->id : null;
        } else {
            $data['language_id'] = null;
        }

        return $data;
    }

    /**
     * Save a record to the database
     */
    protected function saveRecord(array $data): void
    {
        LanguageTest::updateOrInsert(
            ['code' => $data['code']],
            $data
        );
    }

    /**
     * Get the export query
     */
    protected function getExportQuery(Request $request)
    {
        $query = LanguageTest::with('language');

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('language_id')) {
            $query->where('language_id', $request->language_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        return $query->orderBy('name');
    }

    /**
     * Get export columns
     */
    protected function getExportColumns(): array
    {
        return $this->getTemplateColumns();
    }

    /**
     * Prepare a row for export
     */
    protected function prepareExportRow($record): array
    {
        return [
            $record->language?->code ?? '',
            $record->name,
            $record->name_bn ?? '',
            $record->code,
            $record->min_score ?? '',
            $record->max_score ?? '',
            $record->score_type,
            $record->description ?? '',
            $record->is_active ? 'true' : 'false',
        ];
    }
}
