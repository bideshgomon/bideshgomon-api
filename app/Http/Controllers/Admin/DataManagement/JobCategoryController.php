<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Traits\BulkUploadable;
use App\Models\JobCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class JobCategoryController extends Controller
{
    use BulkUploadable;

    protected $entityName = 'Job Category';
    protected $entityNamePlural = 'Job Categories';
    protected $indexRoute = 'admin.data.job-categories.index';
    protected $bulkUploadView = 'Admin/DataManagement/JobCategories/BulkUpload';

    /**
     * Display a listing of job categories
     */
    public function index(Request $request)
    {
        $query = JobCategory::with('parent');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_bn', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Filter by parent
        if ($request->filled('parent_id')) {
            if ($request->parent_id === 'root') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $request->parent_id);
            }
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        // Sort
        $sortField = $request->get('sort', 'order');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $jobCategories = $query->paginate($request->get('per_page', 15))
            ->withQueryString();

        // Get all categories for parent filter dropdown (hierarchical)
        $allCategories = JobCategory::with('parent')
            ->orderBy('order')
            ->get()
            ->map(function($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->full_path,
                    'depth' => $cat->depth,
                ];
            });

        return Inertia::render('Admin/DataManagement/JobCategories/Index', [
            'jobCategories' => $jobCategories,
            'allCategories' => $allCategories,
            'filters' => $request->only(['search', 'parent_id', 'is_active']),
            'sort' => [
                'field' => $sortField,
                'direction' => $sortDirection,
            ],
        ]);
    }

    /**
     * Show the form for creating a new job category
     */
    public function create()
    {
        $parentCategories = JobCategory::with('parent')
            ->orderBy('order')
            ->get()
            ->map(function($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->full_path,
                    'depth' => $cat->depth,
                ];
            });

        return Inertia::render('Admin/DataManagement/JobCategories/Create', [
            'parentCategories' => $parentCategories,
        ]);
    }

    /**
     * Store a newly created job category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:job_categories,id',
            'name' => 'required|string|max:100',
            'name_bn' => 'nullable|string|max:100',
            'slug' => 'nullable|string|max:120|unique:job_categories,slug',
            'description' => 'nullable|string|max:1000',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Prevent circular reference
        if (!empty($validated['parent_id'])) {
            $parent = JobCategory::find($validated['parent_id']);
            // Check if trying to set self as parent (will fail on create, but good practice)
        }

        try {
            JobCategory::create($validated);

            return redirect()->route('admin.data.job-categories.index')
                ->with('success', 'Job Category created successfully.');
        } catch (\Exception $e) {
            Log::error('Job Category creation failed', [
                'error' => $e->getMessage(),
                'data' => $validated,
            ]);

            return back()->withInput()->with('error', 'Failed to create job category: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified job category
     */
    public function edit(JobCategory $jobCategory)
    {
        $parentCategories = JobCategory::with('parent')
            ->where('id', '!=', $jobCategory->id) // Exclude self
            ->orderBy('order')
            ->get()
            ->filter(function($cat) use ($jobCategory) {
                // Exclude descendants to prevent circular references
                return !$cat->isDescendantOf($jobCategory) && $cat->id !== $jobCategory->id;
            })
            ->map(function($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->full_path,
                    'depth' => $cat->depth,
                ];
            })
            ->values();

        return Inertia::render('Admin/DataManagement/JobCategories/Edit', [
            'jobCategory' => $jobCategory->load('parent'),
            'parentCategories' => $parentCategories,
        ]);
    }

    /**
     * Update the specified job category
     */
    public function update(Request $request, JobCategory $jobCategory)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:job_categories,id',
            'name' => 'required|string|max:100',
            'name_bn' => 'nullable|string|max:100',
            'slug' => ['nullable', 'string', 'max:120', Rule::unique('job_categories')->ignore($jobCategory->id)],
            'description' => 'nullable|string|max:1000',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Prevent circular reference
        if (!empty($validated['parent_id'])) {
            if ($validated['parent_id'] == $jobCategory->id) {
                return back()->withInput()->with('error', 'A category cannot be its own parent.');
            }

            $parent = JobCategory::find($validated['parent_id']);
            if ($parent && $parent->isDescendantOf($jobCategory)) {
                return back()->withInput()->with('error', 'Cannot set a descendant as parent (circular reference).');
            }
        }

        try {
            $jobCategory->update($validated);

            return redirect()->route('admin.data.job-categories.index')
                ->with('success', 'Job Category updated successfully.');
        } catch (\Exception $e) {
            Log::error('Job Category update failed', [
                'error' => $e->getMessage(),
                'id' => $jobCategory->id,
                'data' => $validated,
            ]);

            return back()->withInput()->with('error', 'Failed to update job category: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified job category
     */
    public function destroy(JobCategory $jobCategory)
    {
        try {
            // Check if has children
            if ($jobCategory->children()->count() > 0) {
                return back()->with('error', 'Cannot delete category with sub-categories. Delete sub-categories first.');
            }

            // Check if has job postings
            if ($jobCategory->jobPostings()->count() > 0) {
                return back()->with('error', 'Cannot delete category with associated job postings.');
            }

            $jobCategory->delete();

            return redirect()->route('admin.data.job-categories.index')
                ->with('success', 'Job Category deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Job Category deletion failed', [
                'error' => $e->getMessage(),
                'id' => $jobCategory->id,
            ]);

            return back()->with('error', 'Failed to delete job category: ' . $e->getMessage());
        }
    }

    /**
     * Toggle the status of a job category
     */
    public function toggleStatus(JobCategory $jobCategory)
    {
        try {
            $jobCategory->update([
                'is_active' => !$jobCategory->is_active
            ]);

            return back()->with('success', 'Status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Job Category status toggle failed', [
                'error' => $e->getMessage(),
                'id' => $jobCategory->id,
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
            'parent_code',
            'name',
            'name_bn',
            'slug',
            'description',
            'order',
            'is_active',
        ];
    }

    /**
     * Get the required columns for CSV upload
     */
    protected function getRequiredColumns(): array
    {
        return ['name'];
    }

    /**
     * Get validation rules for bulk upload
     */
    protected function getValidationRules(): array
    {
        return [
            'parent_id' => 'nullable|exists:job_categories,id',
            'name' => 'required|string|max:100',
            'name_bn' => 'nullable|string|max:100',
            'slug' => 'nullable|string|max:120|unique:job_categories,slug',
            'description' => 'nullable|string|max:1000',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get sample data for CSV template
     */
    protected function getSampleData(): array
    {
        return [
            [
                'parent_code' => '',
                'name' => 'Information Technology',
                'name_bn' => 'তথ্য প্রযুক্তি',
                'slug' => 'information-technology',
                'description' => 'IT and Technology related jobs',
                'order' => '1',
                'is_active' => 'true',
            ],
            [
                'parent_code' => 'information-technology',
                'name' => 'Software Development',
                'name_bn' => 'সফটওয়্যার উন্নয়ন',
                'slug' => 'software-development',
                'description' => 'Software engineering and development',
                'order' => '1',
                'is_active' => 'true',
            ],
            [
                'parent_code' => 'information-technology',
                'name' => 'Network Administration',
                'name_bn' => 'নেটওয়ার্ক প্রশাসন',
                'slug' => 'network-administration',
                'description' => 'Network and system administration',
                'order' => '2',
                'is_active' => 'true',
            ],
            [
                'parent_code' => '',
                'name' => 'Healthcare',
                'name_bn' => 'স্বাস্থ্যসেবা',
                'slug' => 'healthcare',
                'description' => 'Medical and healthcare professions',
                'order' => '2',
                'is_active' => 'true',
            ],
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

        // Parent code validation (if provided)
        if (!empty($row['parent_code'])) {
            $parent = JobCategory::where('slug', $row['parent_code'])->first();
            if (!$parent) {
                $errors[] = "Parent category with slug '{$row['parent_code']}' not found";
            }
        }

        // Numeric validations
        if (!empty($row['order']) && !is_numeric($row['order'])) {
            $errors[] = "Order must be a number";
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
            'slug' => !empty($row['slug']) ? $row['slug'] : null,
            'description' => $row['description'] ?? null,
            'order' => !empty($row['order']) ? (int)$row['order'] : 0,
            'is_active' => isset($row['is_active']) ? 
                (strtolower($row['is_active']) === 'true' || $row['is_active'] === '1') : true,
        ];

        // Get parent_id from parent_code if provided
        if (!empty($row['parent_code'])) {
            $parent = JobCategory::where('slug', $row['parent_code'])->first();
            $data['parent_id'] = $parent ? $parent->id : null;
        } else {
            $data['parent_id'] = null;
        }

        return $data;
    }

    /**
     * Save a record to the database
     */
    protected function saveRecord(array $data): void
    {
        JobCategory::updateOrInsert(
            ['slug' => $data['slug'] ?? \Illuminate\Support\Str::slug($data['name'])],
            $data
        );
    }

    /**
     * Get the export query
     */
    protected function getExportQuery(Request $request)
    {
        $query = JobCategory::with('parent');

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('parent_id')) {
            if ($request->parent_id === 'root') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $request->parent_id);
            }
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        return $query->orderBy('order');
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
            $record->parent?->slug ?? '',
            $record->name,
            $record->name_bn ?? '',
            $record->slug,
            $record->description ?? '',
            $record->order,
            $record->is_active ? 'true' : 'false',
        ];
    }
}
