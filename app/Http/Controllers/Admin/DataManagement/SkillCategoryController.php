<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Traits\BulkUploadable;
use App\Models\SkillCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SkillCategoryController extends Controller
{
    use BulkUploadable;

    protected $entityName = 'Skill Category';
    protected $entityNamePlural = 'Skill Categories';
    protected $indexRoute = 'admin.data.skill-categories.index';
    protected $bulkUploadView = 'Admin/DataManagement/SkillCategories/BulkUpload';

    /**
     * Display a listing of skill categories
     */
    public function index(Request $request)
    {
        $query = SkillCategory::withCount('skills');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_bn', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        // Sort
        $sortField = $request->get('sort', 'order');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $skillCategories = $query->paginate($request->get('per_page', 15))
            ->withQueryString();

        return Inertia::render('Admin/DataManagement/SkillCategories/Index', [
            'skillCategories' => $skillCategories,
            'filters' => $request->only(['search', 'is_active']),
            'sort' => [
                'field' => $sortField,
                'direction' => $sortDirection,
            ],
        ]);
    }

    /**
     * Show the form for creating a new skill category
     */
    public function create()
    {
        return Inertia::render('Admin/DataManagement/SkillCategories/Create');
    }

    /**
     * Store a newly created skill category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:skill_categories,name',
            'name_bn' => 'nullable|string|max:100',
            'slug' => 'nullable|string|max:120|unique:skill_categories,slug',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            SkillCategory::create($validated);

            return redirect()
                ->route('admin.data.skill-categories.index')
                ->with('success', 'Skill category created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating skill category: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create skill category.');
        }
    }

    /**
     * Show the form for editing a skill category
     */
    public function edit(SkillCategory $skillCategory)
    {
        return Inertia::render('Admin/DataManagement/SkillCategories/Edit', [
            'skillCategory' => $skillCategory,
        ]);
    }

    /**
     * Update the specified skill category
     */
    public function update(Request $request, SkillCategory $skillCategory)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('skill_categories')->ignore($skillCategory->id),
            ],
            'name_bn' => 'nullable|string|max:100',
            'slug' => [
                'nullable',
                'string',
                'max:120',
                Rule::unique('skill_categories')->ignore($skillCategory->id),
            ],
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            $skillCategory->update($validated);

            return redirect()
                ->route('admin.data.skill-categories.index')
                ->with('success', 'Skill category updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating skill category: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update skill category.');
        }
    }

    /**
     * Remove the specified skill category
     */
    public function destroy(SkillCategory $skillCategory)
    {
        try {
            // Check if category has skills
            if ($skillCategory->skills()->count() > 0) {
                return back()->with('error', 'Cannot delete skill category with associated skills. Please reassign or delete the skills first.');
            }

            $skillCategory->delete();

            return redirect()
                ->route('admin.data.skill-categories.index')
                ->with('success', 'Skill category deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting skill category: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete skill category.');
        }
    }

    /**
     * Toggle the status of a skill category
     */
    public function toggleStatus(SkillCategory $skillCategory)
    {
        try {
            $skillCategory->update([
                'is_active' => !$skillCategory->is_active
            ]);

            return back()->with('success', 'Skill category status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error toggling skill category status: ' . $e->getMessage());
            return back()->with('error', 'Failed to update status.');
        }
    }

    // Bulk Upload Implementation
    protected function getModelClass(): string
    {
        return SkillCategory::class;
    }

    protected function getRequiredColumns(): array
    {
        return ['name'];
    }

    protected function getOptionalColumns(): array
    {
        return ['name_bn', 'slug', 'description', 'order', 'is_active'];
    }

    protected function getColumnDescriptions(): array
    {
        return [
            'name' => 'Category name in English (e.g., "Programming Languages")',
            'name_bn' => 'Category name in Bengali',
            'slug' => 'URL-friendly identifier (auto-generated if empty)',
            'description' => 'Brief description of the category',
            'order' => 'Display order (default: 0)',
            'is_active' => '1 for active, 0 for inactive (default: 1)',
        ];
    }

    protected function getSampleData(): array
    {
        return [
            [
                'name' => 'Programming Languages',
                'name_bn' => 'প্রোগ্রামিং ভাষা',
                'slug' => 'programming-languages',
                'description' => 'Programming and scripting languages',
                'order' => 1,
                'is_active' => 1,
            ],
            [
                'name' => 'Frameworks & Libraries',
                'name_bn' => 'ফ্রেমওয়ার্ক এবং লাইব্রেরি',
                'slug' => 'frameworks-libraries',
                'description' => 'Development frameworks and libraries',
                'order' => 2,
                'is_active' => 1,
            ],
            [
                'name' => 'Soft Skills',
                'name_bn' => 'নরম দক্ষতা',
                'slug' => 'soft-skills',
                'description' => 'Communication, leadership, and interpersonal skills',
                'order' => 3,
                'is_active' => 1,
            ],
            [
                'name' => 'Tools & Technologies',
                'name_bn' => 'সরঞ্জাম এবং প্রযুক্তি',
                'slug' => 'tools-technologies',
                'description' => 'Development tools and technologies',
                'order' => 4,
                'is_active' => 1,
            ],
        ];
    }

    protected function validateRow(array $row, int $rowNumber): ?string
    {
        // Name is required
        if (empty($row['name'])) {
            return "Row {$rowNumber}: Name is required";
        }

        // Validate order if provided
        if (isset($row['order']) && !is_numeric($row['order'])) {
            return "Row {$rowNumber}: Order must be a number";
        }

        // Validate is_active if provided
        if (isset($row['is_active']) && !in_array($row['is_active'], ['0', '1', 0, 1, true, false, 'true', 'false'])) {
            return "Row {$rowNumber}: is_active must be 1 or 0";
        }

        return null;
    }

    protected function transformRowData(array $row): array
    {
        $data = [
            'name' => $row['name'],
            'name_bn' => $row['name_bn'] ?? null,
            'slug' => $row['slug'] ?? null,
            'description' => $row['description'] ?? null,
            'order' => isset($row['order']) ? (int)$row['order'] : 0,
            'is_active' => isset($row['is_active']) ? in_array($row['is_active'], [1, '1', true, 'true']) : true,
        ];

        return $data;
    }

    protected function getTemplateColumns(): array
    {
        return ['name', 'name_bn', 'slug', 'description', 'order', 'is_active'];
    }

    protected function getValidationRules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'name_bn' => 'nullable|string|max:100',
            'slug' => 'nullable|string|max:120',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|in:true,false,1,0,yes,no',
        ];
    }

    protected function saveRecord(array $data)
    {
        SkillCategory::updateOrCreate(
            ['name' => $data['name']],
            $data
        );
    }

    protected function getExportQuery(Request $request)
    {
        $query = SkillCategory::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_bn', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        return $query->orderBy('order');
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
            $record->slug,
            $record->description,
            $record->order,
            $record->is_active ? 'true' : 'false',
        ];
    }
}
