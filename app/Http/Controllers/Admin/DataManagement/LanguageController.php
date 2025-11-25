<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Traits\BulkUploadable;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class LanguageController extends Controller
{
    use BulkUploadable;

    protected $entityName = 'Language';
    protected $entityNamePlural = 'Languages';
    protected $indexRoute = 'admin.data.languages.index';
    protected $bulkUploadView = 'Admin/DataManagement/Languages/BulkUpload';

    /**
     * Display a listing of languages
     */
    public function index(Request $request)
    {
        $query = Language::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_bn', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('native_name', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        // Sort
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $languages = $query->paginate($request->get('per_page', 15))
            ->withQueryString();

        return Inertia::render('Admin/DataManagement/Languages/Index', [
            'languages' => $languages,
            'filters' => $request->only(['search', 'is_active']),
            'sort' => [
                'field' => $sortField,
                'direction' => $sortDirection,
            ],
        ]);
    }

    /**
     * Show the form for creating a new language
     */
    public function create()
    {
        return Inertia::render('Admin/DataManagement/Languages/Create');
    }

    /**
     * Store a newly created language
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:languages,name',
            'name_bn' => 'nullable|string|max:100',
            'code' => 'required|string|max:10|unique:languages,code',
            'native_name' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        try {
            // Use direct DB insert to avoid the custom create method
            $validated['created_at'] = now();
            $validated['updated_at'] = now();
            $id = \DB::table('languages')->insertGetId($validated);

            return redirect()->route('admin.data.languages.index')
                ->with('success', 'Language created successfully.');
        } catch (\Exception $e) {
            Log::error('Language creation failed', [
                'error' => $e->getMessage(),
                'data' => $validated,
            ]);

            return back()->withInput()->with('error', 'Failed to create language: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified language
     */
    public function show(Language $language)
    {
        return Inertia::render('Admin/DataManagement/Languages/Show', [
            'language' => $language,
        ]);
    }

    /**
     * Show the form for editing the specified language
     */
    public function edit(Language $language)
    {
        return Inertia::render('Admin/DataManagement/Languages/Edit', [
            'language' => $language,
        ]);
    }

    /**
     * Update the specified language
     */
    public function update(Request $request, Language $language)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('languages')->ignore($language->id)],
            'name_bn' => 'nullable|string|max:100',
            'code' => ['required', 'string', 'max:10', Rule::unique('languages')->ignore($language->id)],
            'native_name' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        try {
            $language->update($validated);

            return redirect()->route('admin.data.languages.index')
                ->with('success', 'Language updated successfully.');
        } catch (\Exception $e) {
            Log::error('Language update failed', [
                'language_id' => $language->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'Failed to update language: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified language
     */
    public function destroy(Language $language)
    {
        try {
            $language->delete();

            return redirect()->route('admin.data.languages.index')
                ->with('success', 'Language deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Language deletion failed', [
                'language_id' => $language->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to delete language: ' . $e->getMessage());
        }
    }

    /**
     * Toggle language status
     */
    public function toggleStatus(Language $language)
    {
        try {
            $language->update(['is_active' => !$language->is_active]);

            return back()->with('success', 'Language status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Language status toggle failed', [
                'language_id' => $language->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to update language status.');
        }
    }

    // BulkUploadable trait implementations

    protected function getTemplateColumns(): array
    {
        return [
            'name',
            'name_bn',
            'code',
            'native_name',
            'is_active'
        ];
    }

    protected function getRequiredColumns(): array
    {
        return [
            'name',
            'code'
        ];
    }

    protected function getValidationRules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'name_bn' => 'nullable|string|max:100',
            'code' => 'required|string|max:10',
            'native_name' => 'nullable|string|max:100',
            'is_active' => 'nullable|in:true,false,1,0,yes,no',
        ];
    }

    protected function getSampleData(): array
    {
        return [
            ['English', 'ইংরেজি', 'en', 'English', 'true'],
            ['Bengali', 'বাংলা', 'bn', 'বাংলা', 'true'],
            ['Arabic', 'আরবি', 'ar', 'العربية', 'true'],
            ['Spanish', 'স্প্যানিশ', 'es', 'Español', 'true'],
        ];
    }

    protected function transformRowData(array $data): array
    {
        // Convert is_active to boolean
        if (isset($data['is_active'])) {
            $data['is_active'] = in_array(strtolower($data['is_active']), ['true', '1', 'yes']);
        }

        // Lowercase language code
        if (isset($data['code'])) {
            $data['code'] = strtolower($data['code']);
        }

        return $data;
    }

    protected function saveRecord(array $data)
    {
        // Use direct DB operation to avoid custom create method
        \DB::table('languages')->updateOrInsert(
            ['code' => $data['code']],
            array_merge($data, [
                'created_at' => now(),
                'updated_at' => now(),
            ])
        );
    }

    protected function getExportQuery(Request $request)
    {
        $query = Language::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_bn', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        return $query->orderBy('name');
    }

    protected function getExportColumns(): array
    {
        return $this->getTemplateColumns();
    }

    protected function prepareExportRow($record): array
    {
        return [
            $record->name,
            $record->name_bn,
            $record->code,
            $record->native_name,
            $record->is_active ? 'true' : 'false',
        ];
    }
}
