<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Traits\BulkUploadable;
use App\Models\Skill;
use App\Models\SkillCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SkillController extends Controller
{
    use BulkUploadable;

    protected $entityName = 'Skill';
    protected $entityNamePlural = 'Skills';
    protected $indexRoute = 'admin.data.skills.index';
    protected $bulkUploadView = 'Admin/DataManagement/Skills/BulkUpload';

    /**
     * Display a listing of skills
     */
    public function index(Request $request)
    {
        $query = Skill::with('skillCategory');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_bn', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('skill_category_id')) {
            $query->where('skill_category_id', $request->skill_category_id);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        // Sort
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $skills = $query->paginate($request->get('per_page', 15))
            ->withQueryString();

        // Get all categories for filter dropdown
        $skillCategories = SkillCategory::active()->ordered()->get();

        return Inertia::render('Admin/DataManagement/Skills/Index', [
            'skills' => $skills,
            'skillCategories' => $skillCategories,
            'filters' => $request->only(['search', 'skill_category_id', 'is_active']),
            'sort' => [
                'field' => $sortField,
                'direction' => $sortDirection,
            ],
        ]);
    }

    /**
     * Show the form for creating a new skill
     */
    public function create()
    {
        $skillCategories = SkillCategory::active()->ordered()->get();

        return Inertia::render('Admin/DataManagement/Skills/Create', [
            'skillCategories' => $skillCategories,
        ]);
    }

    /**
     * Store a newly created skill
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'skill_category_id' => 'nullable|exists:skill_categories,id',
            'name' => 'required|string|max:100|unique:skills,name',
            'name_bn' => 'nullable|string|max:100',
            'slug' => 'nullable|string|max:120|unique:skills,slug',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        try {
            Skill::create($validated);

            return redirect()
                ->route('admin.data.skills.index')
                ->with('success', 'Skill created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating skill: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create skill.');
        }
    }

    /**
     * Show the form for editing a skill
     */
    public function edit(Skill $skill)
    {
        $skillCategories = SkillCategory::active()->ordered()->get();

        return Inertia::render('Admin/DataManagement/Skills/Edit', [
            'skill' => $skill->load('skillCategory'),
            'skillCategories' => $skillCategories,
        ]);
    }

    /**
     * Update the specified skill
     */
    public function update(Request $request, Skill $skill)
    {
        $validated = $request->validate([
            'skill_category_id' => 'nullable|exists:skill_categories,id',
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('skills')->ignore($skill->id),
            ],
            'name_bn' => 'nullable|string|max:100',
            'slug' => [
                'nullable',
                'string',
                'max:120',
                Rule::unique('skills')->ignore($skill->id),
            ],
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        try {
            $skill->update($validated);

            return redirect()
                ->route('admin.data.skills.index')
                ->with('success', 'Skill updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating skill: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update skill.');
        }
    }

    /**
     * Remove the specified skill
     */
    public function destroy(Skill $skill)
    {
        try {
            // Check if skill is associated with users
            if ($skill->users()->count() > 0) {
                return back()->with('error', 'Cannot delete skill that is associated with users.');
            }

            $skill->delete();

            return redirect()
                ->route('admin.data.skills.index')
                ->with('success', 'Skill deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting skill: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete skill.');
        }
    }

    /**
     * Toggle the status of a skill
     */
    public function toggleStatus(Skill $skill)
    {
        try {
            $skill->update([
                'is_active' => !$skill->is_active
            ]);

            return back()->with('success', 'Skill status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error toggling skill status: ' . $e->getMessage());
            return back()->with('error', 'Failed to update status.');
        }
    }

    // Bulk Upload Implementation
    protected function getModelClass(): string
    {
        return Skill::class;
    }

    protected function getRequiredColumns(): array
    {
        return ['name'];
    }

    protected function getOptionalColumns(): array
    {
        return ['category_code', 'name_bn', 'slug', 'description', 'is_active'];
    }

    protected function getColumnDescriptions(): array
    {
        return [
            'name' => 'Skill name in English (e.g., "PHP Programming")',
            'category_code' => 'Skill category slug (e.g., "programming-languages")',
            'name_bn' => 'Skill name in Bengali',
            'slug' => 'URL-friendly identifier (auto-generated if empty)',
            'description' => 'Brief description of the skill',
            'is_active' => '1 for active, 0 for inactive (default: 1)',
        ];
    }

    protected function getSampleData(): array
    {
        return [
            [
                'category_code' => 'programming-languages',
                'name' => 'PHP Programming',
                'name_bn' => 'পিএইচপি প্রোগ্রামিং',
                'slug' => 'php-programming',
                'description' => 'PHP programming language skills',
                'is_active' => 1,
            ],
            [
                'category_code' => 'programming-languages',
                'name' => 'JavaScript',
                'name_bn' => 'জাভাস্ক্রিপ্ট',
                'slug' => 'javascript',
                'description' => 'JavaScript programming',
                'is_active' => 1,
            ],
            [
                'category_code' => 'frameworks-libraries',
                'name' => 'Laravel Framework',
                'name_bn' => 'লারাভেল ফ্রেমওয়ার্ক',
                'slug' => 'laravel-framework',
                'description' => 'Laravel PHP framework',
                'is_active' => 1,
            ],
            [
                'category_code' => 'soft-skills',
                'name' => 'Team Leadership',
                'name_bn' => 'দল নেতৃত্ব',
                'slug' => 'team-leadership',
                'description' => 'Leading and managing teams',
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

        // Validate category_code if provided
        if (!empty($row['category_code'])) {
            $category = SkillCategory::where('slug', $row['category_code'])->first();
            if (!$category) {
                return "Row {$rowNumber}: Skill category with slug '{$row['category_code']}' not found";
            }
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
            'is_active' => isset($row['is_active']) ? in_array($row['is_active'], [1, '1', true, 'true']) : true,
        ];

        // Map category_code (slug) to skill_category_id
        if (!empty($row['category_code'])) {
            $category = SkillCategory::where('slug', $row['category_code'])->first();
            $data['skill_category_id'] = $category ? $category->id : null;
        } else {
            $data['skill_category_id'] = null;
        }

        return $data;
    }

    protected function getTemplateColumns(): array
    {
        return ['category_code', 'name', 'name_bn', 'slug', 'description', 'is_active'];
    }

    protected function getValidationRules(): array
    {
        return [
            'category_code' => 'nullable|string|max:120',
            'name' => 'required|string|max:100',
            'name_bn' => 'nullable|string|max:100',
            'slug' => 'nullable|string|max:120',
            'description' => 'nullable|string',
            'is_active' => 'nullable|in:true,false,1,0,yes,no',
        ];
    }

    protected function saveRecord(array $data)
    {
        Skill::updateOrCreate(
            ['name' => $data['name']],
            $data
        );
    }

    protected function getExportQuery(Request $request)
    {
        $query = Skill::with('skillCategory');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_bn', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('skill_category_id')) {
            $query->where('skill_category_id', $request->skill_category_id);
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
            $record->skillCategory?->slug ?? '',
            $record->name,
            $record->name_bn,
            $record->slug,
            $record->description,
            $record->is_active ? 'true' : 'false',
        ];
    }
}
