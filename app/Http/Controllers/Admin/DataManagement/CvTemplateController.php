<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Controllers\Controller;
use App\Models\CvTemplate;
use App\Traits\BulkUploadable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CvTemplateController extends Controller
{
    use BulkUploadable;

    public function index(Request $request)
    {
        $query = CvTemplate::query();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('slug', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('category', 'LIKE', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category') && $request->category !== '') {
            $query->where('category', $request->category);
        }

        // Filter by type (free/premium)
        if ($request->has('is_premium') && $request->is_premium !== '') {
            $query->where('is_premium', $request->is_premium);
        }

        // Filter by status
        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        // Sort
        $sortField = $request->get('sort', 'sort_order');
        $sortDirection = $request->get('direction', 'asc');

        if ($sortField === 'downloads') {
            $query->orderBy('download_count', $sortDirection);
        } elseif ($sortField === 'cvs') {
            $query->withCount('userCvs')->orderBy('user_cvs_count', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        $templates = $query->withCount('userCvs')->paginate(20);

        return Inertia::render('Admin/DataManagement/CvTemplates/Index', [
            'templates' => $templates,
            'filters' => $request->only(['search', 'category', 'is_premium', 'is_active', 'sort', 'direction']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/DataManagement/CvTemplates/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:cv_templates,name',
            'slug' => 'nullable|string|max:255|unique:cv_templates,slug',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|string|max:255',
            'category' => 'required|string|max:100',
            'is_premium' => 'boolean',
            'price' => 'required|numeric|min:0',
            'color_scheme' => 'nullable|json',
            'sections' => 'nullable|json',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if (isset($validated['color_scheme'])) {
            $validated['color_scheme'] = json_decode($validated['color_scheme'], true);
        }

        if (isset($validated['sections'])) {
            $validated['sections'] = json_decode($validated['sections'], true);
        }

        CvTemplate::create($validated);

        return redirect()
            ->route('admin.data.cv-templates.index')
            ->with('success', 'CV template created successfully.');
    }

    public function edit(CvTemplate $cvTemplate)
    {
        $cvTemplate->load('userCvs:id,user_id');
        
        return Inertia::render('Admin/DataManagement/CvTemplates/Edit', [
            'template' => $cvTemplate->append('user_cvs_count'),
        ]);
    }

    public function update(Request $request, CvTemplate $cvTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:cv_templates,name,' . $cvTemplate->id,
            'slug' => 'nullable|string|max:255|unique:cv_templates,slug,' . $cvTemplate->id,
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|string|max:255',
            'category' => 'required|string|max:100',
            'is_premium' => 'boolean',
            'price' => 'required|numeric|min:0',
            'color_scheme' => 'nullable|json',
            'sections' => 'nullable|json',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if (isset($validated['color_scheme'])) {
            $validated['color_scheme'] = json_decode($validated['color_scheme'], true);
        }

        if (isset($validated['sections'])) {
            $validated['sections'] = json_decode($validated['sections'], true);
        }

        $cvTemplate->update($validated);

        return redirect()
            ->route('admin.data.cv-templates.index')
            ->with('success', 'CV template updated successfully.');
    }

    public function destroy(CvTemplate $cvTemplate)
    {
        // Check if template has user CVs
        if ($cvTemplate->userCvs()->exists()) {
            return back()->with('error', 'Cannot delete template that is being used by users.');
        }

        $cvTemplate->delete();

        return redirect()
            ->route('admin.data.cv-templates.index')
            ->with('success', 'CV template deleted successfully.');
    }

    public function toggleStatus(CvTemplate $cvTemplate)
    {
        $cvTemplate->is_active = !$cvTemplate->is_active;
        $cvTemplate->save();

        return back();
    }

    // BulkUploadable trait implementation
    protected function getModelClass(): string
    {
        return CvTemplate::class;
    }

    protected function getRequiredColumns(): array
    {
        return ['name', 'category', 'price'];
    }

    protected function getOptionalColumns(): array
    {
        return ['slug', 'description', 'thumbnail', 'is_premium', 'color_scheme', 'sections', 'sort_order', 'is_active'];
    }

    protected function getTemplateColumns(): array
    {
        return array_merge($this->getRequiredColumns(), $this->getOptionalColumns());
    }

    protected function getValidationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|string|max:255',
            'category' => 'required|string|max:100',
            'is_premium' => 'nullable|boolean',
            'price' => 'required|numeric|min:0',
            'color_scheme' => 'nullable|string',
            'sections' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ];
    }

    protected function saveRecord(array $data)
    {
        return CvTemplate::create($data);
    }

    protected function getColumnDescriptions(): array
    {
        return [
            'name' => 'Template name (must be unique)',
            'slug' => 'URL-friendly identifier (auto-generated if empty)',
            'description' => 'Template description (optional)',
            'thumbnail' => 'Thumbnail image URL (optional)',
            'category' => 'Category (e.g., professional, creative, modern, traditional)',
            'is_premium' => '1 for premium, 0 for free (default: 0)',
            'price' => 'Price in BDT (0 for free templates)',
            'color_scheme' => 'Color scheme as JSON (optional)',
            'sections' => 'Template sections as JSON (optional)',
            'sort_order' => 'Display order (0-999, default: 0)',
            'is_active' => '1 for active, 0 for inactive (default: 1)',
        ];
    }

    protected function getSampleData(): array
    {
        return [
            [
                'name' => 'Professional Blue',
                'slug' => 'professional-blue',
                'description' => 'Clean and professional template with blue accents',
                'thumbnail' => '/images/templates/professional-blue.png',
                'category' => 'professional',
                'is_premium' => '0',
                'price' => '0',
                'color_scheme' => '{"primary":"#2563EB","secondary":"#64748B"}',
                'sections' => '["personal_info","education","experience","skills"]',
                'sort_order' => '1',
                'is_active' => '1',
            ],
            [
                'name' => 'Creative Designer',
                'slug' => 'creative-designer',
                'description' => 'Eye-catching template for creative professionals',
                'thumbnail' => '/images/templates/creative-designer.png',
                'category' => 'creative',
                'is_premium' => '1',
                'price' => '500',
                'color_scheme' => '{"primary":"#8B5CF6","secondary":"#EC4899"}',
                'sections' => '["personal_info","portfolio","education","experience","skills","certifications"]',
                'sort_order' => '2',
                'is_active' => '1',
            ],
            [
                'name' => 'Modern Minimal',
                'slug' => 'modern-minimal',
                'description' => 'Minimalist design with modern typography',
                'thumbnail' => '/images/templates/modern-minimal.png',
                'category' => 'modern',
                'is_premium' => '0',
                'price' => '0',
                'color_scheme' => '{"primary":"#000000","secondary":"#FFFFFF"}',
                'sections' => '["personal_info","summary","experience","education","skills"]',
                'sort_order' => '3',
                'is_active' => '1',
            ],
        ];
    }

    protected function validateRow(array $row, int $rowNumber): ?string
    {
        if (CvTemplate::where('name', $row['name'])->exists()) {
            return "Row {$rowNumber}: Template with name '{$row['name']}' already exists";
        }

        if (!empty($row['slug']) && CvTemplate::where('slug', $row['slug'])->exists()) {
            return "Row {$rowNumber}: Template with slug '{$row['slug']}' already exists";
        }

        // Validate price
        if (isset($row['price']) && (!is_numeric($row['price']) || $row['price'] < 0)) {
            return "Row {$rowNumber}: Price must be a non-negative number";
        }

        return null;
    }

    protected function transformRowData(array $row): array
    {
        $data = [
            'name' => trim($row['name']),
            'slug' => !empty($row['slug']) ? Str::slug(trim($row['slug'])) : Str::slug(trim($row['name'])),
            'description' => !empty($row['description']) ? trim($row['description']) : null,
            'thumbnail' => !empty($row['thumbnail']) ? trim($row['thumbnail']) : null,
            'category' => trim($row['category']),
            'is_premium' => isset($row['is_premium']) ? (bool)$row['is_premium'] : false,
            'price' => isset($row['price']) ? (float)$row['price'] : 0,
            'color_scheme' => !empty($row['color_scheme']) ? json_decode($row['color_scheme'], true) : null,
            'sections' => !empty($row['sections']) ? json_decode($row['sections'], true) : null,
            'sort_order' => isset($row['sort_order']) ? (int)$row['sort_order'] : 0,
            'is_active' => isset($row['is_active']) ? (bool)$row['is_active'] : true,
        ];

        return $data;
    }

    protected function getExportQuery(Request $request)
    {
        return CvTemplate::query()->orderBy('sort_order')->orderBy('name');
    }

    protected function getExportColumns(): array
    {
        return ['name', 'slug', 'description', 'thumbnail', 'category', 'is_premium', 'price', 'color_scheme', 'sections', 'sort_order', 'is_active'];
    }

    protected function prepareExportRow($model): array
    {
        return [
            'name' => $model->name,
            'slug' => $model->slug,
            'description' => $model->description ?? '',
            'thumbnail' => $model->thumbnail ?? '',
            'category' => $model->category,
            'is_premium' => $model->is_premium ? '1' : '0',
            'price' => $model->price,
            'color_scheme' => !empty($model->color_scheme) ? json_encode($model->color_scheme) : '',
            'sections' => !empty($model->sections) ? json_encode($model->sections) : '',
            'sort_order' => $model->sort_order,
            'is_active' => $model->is_active ? '1' : '0',
        ];
    }
}
