<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DirectoryCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DirectoryCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = DirectoryCategory::withCount('directories');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('name_bn', 'like', "%{$request->search}%");
            });
        }

        if ($request->status === 'active') {
            $query->active();
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        $categories = $query->ordered()->paginate(20);

        return Inertia::render('Admin/Directories/Categories/Index', [
            'categories' => $categories,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Directories/Categories/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:directory_categories,slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'color' => 'required|string|in:blue,green,red,yellow,purple,pink,indigo,gray',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        DirectoryCategory::create($validated);

        return redirect()->route('admin.directory-categories.index')
            ->with('success', 'Directory category created successfully');
    }

    public function edit(DirectoryCategory $directoryCategory)
    {
        return Inertia::render('Admin/Directories/Categories/Edit', [
            'category' => $directoryCategory->load('directories'),
        ]);
    }

    public function update(Request $request, DirectoryCategory $directoryCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:directory_categories,slug,' . $directoryCategory->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'color' => 'required|string|in:blue,green,red,yellow,purple,pink,indigo,gray',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $directoryCategory->update($validated);

        return redirect()->route('admin.directory-categories.index')
            ->with('success', 'Directory category updated successfully');
    }

    public function destroy(DirectoryCategory $directoryCategory)
    {
        if ($directoryCategory->directories()->count() > 0) {
            return back()->with('error', 'Cannot delete category with existing directories');
        }

        $directoryCategory->delete();

        return redirect()->route('admin.directory-categories.index')
            ->with('success', 'Directory category deleted successfully');
    }

    public function toggleActive(DirectoryCategory $directoryCategory)
    {
        $directoryCategory->update(['is_active' => !$directoryCategory->is_active]);

        return back()->with('success', 'Category status updated successfully');
    }

    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:directory_categories,id'
        ]);

        DirectoryCategory::updateOrder($validated['ids']);

        return back()->with('success', 'Category order updated successfully');
    }
}
