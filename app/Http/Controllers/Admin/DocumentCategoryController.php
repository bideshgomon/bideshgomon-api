<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DocumentCategoryController extends Controller
{
    public function index()
    {
        $categories = DocumentCategory::withCount('documents')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Admin/DocumentCategories/Index', [
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/DocumentCategories/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $category = DocumentCategory::create($validated);

        return redirect()->route('admin.document-categories.index')
            ->with('success', 'Document category created successfully.');
    }

    public function show(DocumentCategory $documentCategory)
    {
        $documentCategory->load('documents');

        return Inertia::render('Admin/DocumentCategories/Show', [
            'category' => $documentCategory,
        ]);
    }

    public function edit(DocumentCategory $documentCategory)
    {
        return Inertia::render('Admin/DocumentCategories/Edit', [
            'category' => $documentCategory,
        ]);
    }

    public function update(Request $request, DocumentCategory $documentCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $documentCategory->update($validated);

        return redirect()->route('admin.document-categories.index')
            ->with('success', 'Document category updated successfully.');
    }

    public function destroy(DocumentCategory $documentCategory)
    {
        $documentCategory->delete();

        return redirect()->route('admin.document-categories.index')
            ->with('success', 'Document category deleted successfully.');
    }
}
