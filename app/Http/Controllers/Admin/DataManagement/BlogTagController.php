<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Controllers\Controller;
use App\Models\BlogTag;
use App\Traits\BulkUploadable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BlogTagController extends Controller
{
    use BulkUploadable;

    public function index(Request $request)
    {
        $query = BlogTag::query()->withCount('posts');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
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
                    $query->orderBy('name');
            }
        } else {
            $query->orderBy('name');
        }

        $blogTags = $query->paginate(20)->withQueryString();

        return Inertia::render('Admin/DataManagement/BlogTags/Index', [
            'blogTags' => $blogTags,
            'filters' => $request->only(['search', 'sort']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/DataManagement/BlogTags/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:blog_tags,name',
            'slug' => 'nullable|string|max:255|unique:blog_tags,slug',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        BlogTag::create($validated);

        return redirect()
            ->route('admin.data.blog-tags.index')
            ->with('success', 'Blog tag created successfully.');
    }

    public function edit(BlogTag $blogTag)
    {
        $blogTag->loadCount('posts');
        
        return Inertia::render('Admin/DataManagement/BlogTags/Edit', [
            'blogTag' => $blogTag,
        ]);
    }

    public function update(Request $request, BlogTag $blogTag)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:blog_tags,name,' . $blogTag->id,
            'slug' => 'nullable|string|max:255|unique:blog_tags,slug,' . $blogTag->id,
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $blogTag->update($validated);

        return redirect()
            ->route('admin.data.blog-tags.index')
            ->with('success', 'Blog tag updated successfully.');
    }

    public function destroy(BlogTag $blogTag)
    {
        if ($blogTag->posts()->exists()) {
            return back()->with('error', 'Cannot delete blog tag with existing posts.');
        }

        $blogTag->delete();

        return redirect()
            ->route('admin.data.blog-tags.index')
            ->with('success', 'Blog tag deleted successfully.');
    }

    // BulkUploadable trait implementation
    protected function getModelClass(): string
    {
        return BlogTag::class;
    }

    protected function getRequiredColumns(): array
    {
        return ['name'];
    }

    protected function getOptionalColumns(): array
    {
        return ['slug'];
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
        ];
    }

    protected function saveRecord(array $data)
    {
        return BlogTag::create($data);
    }

    protected function getColumnDescriptions(): array
    {
        return [
            'name' => 'Tag name (must be unique)',
            'slug' => 'URL-friendly identifier (auto-generated if empty)',
        ];
    }

    protected function getSampleData(): array
    {
        return [
            ['name' => 'Work Visa', 'slug' => 'work-visa'],
            ['name' => 'Student Visa', 'slug' => 'student-visa'],
            ['name' => 'Tourist Visa', 'slug' => 'tourist-visa'],
            ['name' => 'Immigration', 'slug' => 'immigration'],
            ['name' => 'Documents', 'slug' => 'documents'],
            ['name' => 'Requirements', 'slug' => 'requirements'],
        ];
    }

    protected function validateRow(array $row, int $rowNumber): ?string
    {
        if (empty($row['name'])) {
            return "Row {$rowNumber}: Name is required";
        }

        return null;
    }

    protected function transformRowData(array $row): array
    {
        return [
            'name' => trim($row['name']),
            'slug' => !empty($row['slug']) ? Str::slug(trim($row['slug'])) : Str::slug(trim($row['name'])),
        ];
    }

    protected function getExportQuery(Request $request)
    {
        return BlogTag::query()->orderBy('name');
    }

    protected function getExportColumns(): array
    {
        return ['name', 'slug'];
    }

    protected function prepareExportRow($model): array
    {
        return [
            'name' => $model->name,
            'slug' => $model->slug,
        ];
    }
}
