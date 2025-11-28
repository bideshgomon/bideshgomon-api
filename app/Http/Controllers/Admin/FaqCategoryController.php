<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class FaqCategoryController extends Controller
{
    /**
     * Display a listing of FAQ categories
     */
    public function index()
    {
        $categories = FaqCategory::withCount('faqs')->ordered()->paginate(20);

        return Inertia::render('Admin/FaqCategories/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return Inertia::render('Admin/FaqCategories/Create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:faq_categories,slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Auto-generate slug if not provided
        if (!isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Set default order if not provided
        if (!isset($validated['order'])) {
            $validated['order'] = FaqCategory::max('order') + 1;
        }

        FaqCategory::create($validated);

        return redirect()->route('admin.faq-categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Show the form for editing the category
     */
    public function edit(FaqCategory $faqCategory)
    {
        return Inertia::render('Admin/FaqCategories/Edit', [
            'category' => $faqCategory->loadCount('faqs'),
        ]);
    }

    /**
     * Update the category
     */
    public function update(Request $request, FaqCategory $faqCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:faq_categories,slug,' . $faqCategory->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Auto-generate slug if not provided
        if (!isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $faqCategory->update($validated);

        return redirect()->route('admin.faq-categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the category
     */
    public function destroy(FaqCategory $faqCategory)
    {
        // Check if category has FAQs
        if ($faqCategory->faqs()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete category with existing FAQs.']);
        }

        $faqCategory->delete();

        return redirect()->route('admin.faq-categories.index')->with('success', 'Category deleted successfully.');
    }

    /**
     * Reorder categories
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:faq_categories,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        foreach ($validated['items'] as $item) {
            FaqCategory::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return back()->with('success', 'Categories reordered successfully.');
    }
}
