<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Traits\BulkUploadable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BlogCategoryController extends Controller
{
    use BulkUploadable;

    public function index(Request $request)
    {
        $query = BlogCategory::query()->withCount('posts');

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
                case 'posts':
                    $query->orderBy('posts_count', 'desc');
                    break;
                case 'latest':
                    $query->latest();
                    break;
                default:
                    $query->orderBy('order')->orderBy('name');
            }
        } else {
            $query->orderBy('order')->orderBy('name');
        }

        $blogCategories = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/DataManagement/BlogCategories/Index', [
            'blogCategories' => $blogCategories,
            'filters' => $request->only(['search', 'status', 'sort']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/DataManagement/BlogCategories/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name',
            'slug' => 'nullable|string|max:255|unique:blog_categories,slug',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        BlogCategory::create($validated);

        return redirect()
            ->route('admin.data.blog-categories.index')
            ->with('success', 'Blog category created successfully.');
    }

    public function edit(BlogCategory $blogCategory)
    {
        $blogCategory->loadCount('posts');
        
        return Inertia::render('Admin/DataManagement/BlogCategories/Edit', [
            'blogCategory' => $blogCategory,
        ]);
    }

    public function update(Request $request, BlogCategory $blogCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name,' . $blogCategory->id,
            'slug' => 'nullable|string|max:255|unique:blog_categories,slug,' . $blogCategory->id,
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $blogCategory->update($validated);

        return redirect()
            ->route('admin.data.blog-categories.index')
            ->with('success', 'Blog category updated successfully.');
    }

    public function destroy(BlogCategory $blogCategory)
    {
        if ($blogCategory->posts()->exists()) {
            return back()->with('error', 'Cannot delete blog category with existing posts.');
        }

        $blogCategory->delete();

        return redirect()
            ->route('admin.data.blog-categories.index')
            ->with('success', 'Blog category deleted successfully.');
    }

    public function toggleStatus(BlogCategory $blogCategory)
    {
        $blogCategory->update([
            'is_active' => !$blogCategory->is_active,
        ]);

        return back();
    }

    // BulkUploadable trait implementation
    protected function getModelClass(): string
    {
        return BlogCategory::class;
    }

    protected function getRequiredColumns(): array
    {
        return ['name'];
    }

    protected function getOptionalColumns(): array
    {
        return ['slug', 'icon', 'description', 'color', 'order', 'is_active'];
    }

    protected function getValidationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'color' => 'nullable|string|regex:/^#[0-9A-F]{6}$/i',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ];
    }

    protected function saveRecord(array $data)
    {
        return BlogCategory::create($data);
    }

    protected function getColumnDescriptions(): array
    {
        return [
            'name' => 'Category name (must be unique)',
            'slug' => 'URL-friendly identifier (auto-generated if empty)',
            'icon' => 'Icon class name',
            'description' => 'Brief description',
            'color' => 'Hex color code (e.g., #3B82F6)',
            'order' => 'Display order (0-999)',
            'is_active' => '1 for active, 0 for inactive (default: 1)',
        ];
    }

    protected function getSampleData(): array
    {
        return [
            [
                'name' => 'Visa Guides',
                'slug' => 'visa-guides',
                'icon' => 'DocumentTextIcon',
                'description' => 'Complete guides for visa applications',
                'color' => '#3B82F6',
                'order' => 1,
                'is_active' => 1,
            ],
            [
                'name' => 'Travel Tips',
                'slug' => 'travel-tips',
                'icon' => 'GlobeAltIcon',
                'description' => 'Tips for international travelers',
                'color' => '#10B981',
                'order' => 2,
                'is_active' => 1,
            ],
            [
                'name' => 'News & Updates',
                'slug' => 'news-updates',
                'icon' => 'NewspaperIcon',
                'description' => 'Latest immigration and travel news',
                'color' => '#F59E0B',
                'order' => 3,
                'is_active' => 1,
            ],
        ];
    }

    protected function validateRow(array $row, int $rowNumber): ?string
    {
        if (empty($row['name'])) {
            return "Row {$rowNumber}: Name is required";
        }

        if (!empty($row['color']) && !preg_match('/^#[0-9A-F]{6}$/i', $row['color'])) {
            return "Row {$rowNumber}: Color must be a valid hex code (e.g., #3B82F6)";
        }

        if (isset($row['order']) && (!is_numeric($row['order']) || $row['order'] < 0)) {
            return "Row {$rowNumber}: Order must be a positive number";
        }

        return null;
    }

    protected function transformRowData(array $row): array
    {
        return [
            'name' => trim($row['name']),
            'slug' => !empty($row['slug']) ? Str::slug(trim($row['slug'])) : Str::slug(trim($row['name'])),
            'icon' => trim($row['icon'] ?? ''),
            'description' => trim($row['description'] ?? ''),
            'color' => !empty($row['color']) ? strtoupper(trim($row['color'])) : '#3B82F6',
            'order' => isset($row['order']) ? (int)$row['order'] : 0,
            'is_active' => isset($row['is_active']) ? (bool)$row['is_active'] : true,
        ];
    }

    protected function getTemplateColumns(): array
    {
        return array_merge($this->getRequiredColumns(), $this->getOptionalColumns());
    }

    protected function getExportQuery(Request $request)
    {
        return BlogCategory::query()->orderBy('order')->orderBy('name');
    }

    protected function getExportColumns(): array
    {
        return ['name', 'slug', 'icon', 'description', 'color', 'order', 'is_active'];
    }

    protected function prepareExportRow($model): array
    {
        return [
            'name' => $model->name,
            'slug' => $model->slug,
            'icon' => $model->icon ?? '',
            'description' => $model->description ?? '',
            'color' => $model->color,
            'order' => $model->order,
            'is_active' => $model->is_active ? 1 : 0,
        ];
    }
}
