<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Traits\BulkUploadable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ServiceCategoryController extends Controller
{
    use BulkUploadable;

    public function index(Request $request)
    {
        $query = ServiceCategory::query()->withCount('modules');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Sort
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'name':
                    $query->orderBy('name');
                    break;
                case 'modules':
                    $query->orderBy('modules_count', 'desc');
                    break;
                case 'latest':
                    $query->latest();
                    break;
                default:
                    $query->orderBy('sort_order')->orderBy('name');
            }
        } else {
            $query->orderBy('sort_order')->orderBy('name');
        }

        $serviceCategories = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/DataManagement/ServiceCategories/Index', [
            'serviceCategories' => $serviceCategories,
            'filters' => $request->only(['search', 'status', 'sort']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/DataManagement/ServiceCategories/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:service_categories,name',
            'slug' => 'nullable|string|max:255|unique:service_categories,slug',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        ServiceCategory::create($validated);

        return redirect()
            ->route('admin.data.service-categories.index')
            ->with('success', 'Service category created successfully.');
    }

    public function edit(ServiceCategory $serviceCategory)
    {
        $serviceCategory->loadCount('modules');
        
        return Inertia::render('Admin/DataManagement/ServiceCategories/Edit', [
            'serviceCategory' => $serviceCategory,
        ]);
    }

    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:service_categories,name,' . $serviceCategory->id,
            'slug' => 'nullable|string|max:255|unique:service_categories,slug,' . $serviceCategory->id,
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $serviceCategory->update($validated);

        return redirect()
            ->route('admin.data.service-categories.index')
            ->with('success', 'Service category updated successfully.');
    }

    public function destroy(ServiceCategory $serviceCategory)
    {
        // Check if category has modules
        if ($serviceCategory->modules()->exists()) {
            return back()->with('error', 'Cannot delete service category with existing modules.');
        }

        $serviceCategory->delete();

        return redirect()
            ->route('admin.data.service-categories.index')
            ->with('success', 'Service category deleted successfully.');
    }

    public function toggleStatus(ServiceCategory $serviceCategory)
    {
        $serviceCategory->update([
            'is_active' => !$serviceCategory->is_active,
        ]);

        return back();
    }

    // BulkUploadable trait implementation
    protected function getModelClass(): string
    {
        return ServiceCategory::class;
    }

    protected function getRequiredColumns(): array
    {
        return ['name'];
    }

    protected function getOptionalColumns(): array
    {
        return ['slug', 'icon', 'description', 'color', 'sort_order', 'is_active'];
    }

    protected function getValidationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'color' => 'nullable|string|regex:/^#[0-9A-F]{6}$/i',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ];
    }

    protected function saveRecord(array $data)
    {
        return ServiceCategory::create($data);
    }

    protected function getColumnDescriptions(): array
    {
        return [
            'name' => 'Service category name (must be unique)',
            'slug' => 'URL-friendly identifier (auto-generated if empty)',
            'icon' => 'Icon class (e.g., fa-plane, heroicons)',
            'description' => 'Brief description of the category',
            'color' => 'Hex color code (e.g., #3B82F6)',
            'sort_order' => 'Display order (0-999)',
            'is_active' => '1 for active, 0 for inactive (default: 1)',
        ];
    }

    protected function getSampleData(): array
    {
        return [
            [
                'name' => 'Visa Services',
                'slug' => 'visa-services',
                'icon' => 'DocumentTextIcon',
                'description' => 'Complete visa application and processing services',
                'color' => '#3B82F6',
                'sort_order' => 1,
                'is_active' => 1,
            ],
            [
                'name' => 'Travel Services',
                'slug' => 'travel-services',
                'icon' => 'GlobeAltIcon',
                'description' => 'Flight, hotel, and travel insurance booking',
                'color' => '#10B981',
                'sort_order' => 2,
                'is_active' => 1,
            ],
            [
                'name' => 'Documentation',
                'slug' => 'documentation',
                'icon' => 'ClipboardDocumentListIcon',
                'description' => 'Document translation, attestation, and legalization',
                'color' => '#F59E0B',
                'sort_order' => 3,
                'is_active' => 1,
            ],
        ];
    }

    protected function validateRow(array $row, int $rowNumber): ?string
    {
        // Check required fields
        if (empty($row['name'])) {
            return "Row {$rowNumber}: Name is required";
        }

        // Validate color format if provided
        if (!empty($row['color']) && !preg_match('/^#[0-9A-F]{6}$/i', $row['color'])) {
            return "Row {$rowNumber}: Color must be a valid hex code (e.g., #3B82F6)";
        }

        // Validate sort_order
        if (isset($row['sort_order']) && (!is_numeric($row['sort_order']) || $row['sort_order'] < 0)) {
            return "Row {$rowNumber}: Sort order must be a positive number";
        }

        return null;
    }

    protected function transformRowData(array $row): array
    {
        $data = [
            'name' => trim($row['name']),
            'slug' => !empty($row['slug']) ? Str::slug(trim($row['slug'])) : Str::slug(trim($row['name'])),
            'icon' => trim($row['icon'] ?? ''),
            'description' => trim($row['description'] ?? ''),
            'color' => !empty($row['color']) ? strtoupper(trim($row['color'])) : '#3B82F6',
            'sort_order' => isset($row['sort_order']) ? (int)$row['sort_order'] : 0,
            'is_active' => isset($row['is_active']) ? (bool)$row['is_active'] : true,
        ];

        return $data;
    }

    protected function getTemplateColumns(): array
    {
        return array_merge($this->getRequiredColumns(), $this->getOptionalColumns());
    }

    protected function getExportQuery(Request $request)
    {
        return ServiceCategory::query()->orderBy('sort_order')->orderBy('name');
    }

    protected function getExportColumns(): array
    {
        return ['name', 'slug', 'icon', 'description', 'color', 'sort_order', 'is_active'];
    }

    protected function prepareExportRow($model): array
    {
        return [
            'name' => $model->name,
            'slug' => $model->slug,
            'icon' => $model->icon ?? '',
            'description' => $model->description ?? '',
            'color' => $model->color,
            'sort_order' => $model->sort_order,
            'is_active' => $model->is_active ? 1 : 0,
        ];
    }
}
