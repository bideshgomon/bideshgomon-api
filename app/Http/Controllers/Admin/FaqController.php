<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FaqController extends Controller
{
    /**
     * Display a listing of FAQs
     */
    public function index(Request $request)
    {
        $query = Faq::with('category');

        // Search
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('faq_category_id', $request->category_id);
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        $faqs = $query->ordered()->paginate(20);
        $categories = FaqCategory::active()->ordered()->get();

        return Inertia::render('Admin/Faqs/Index', [
            'faqs' => $faqs,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category_id', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new FAQ
     */
    public function create()
    {
        $categories = FaqCategory::active()->ordered()->get();

        return Inertia::render('Admin/Faqs/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created FAQ
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'faq_category_id' => 'required|exists:faq_categories,id',
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'order' => 'nullable|integer|min:0',
            'is_published' => 'boolean',
        ]);

        // Set default order if not provided
        if (!isset($validated['order'])) {
            $validated['order'] = Faq::where('faq_category_id', $validated['faq_category_id'])->max('order') + 1;
        }

        Faq::create($validated);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ created successfully.');
    }

    /**
     * Show the form for editing the FAQ
     */
    public function edit(Faq $faq)
    {
        $categories = FaqCategory::active()->ordered()->get();

        return Inertia::render('Admin/Faqs/Edit', [
            'faq' => $faq->load('category'),
            'categories' => $categories,
        ]);
    }

    /**
     * Update the FAQ
     */
    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'faq_category_id' => 'required|exists:faq_categories,id',
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'order' => 'nullable|integer|min:0',
            'is_published' => 'boolean',
        ]);

        $faq->update($validated);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ updated successfully.');
    }

    /**
     * Remove the FAQ
     */
    public function destroy(Faq $faq)
    {
        $faq->delete();

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ deleted successfully.');
    }

    /**
     * Reorder FAQs within a category
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:faqs,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        foreach ($validated['items'] as $item) {
            Faq::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return back()->with('success', 'FAQs reordered successfully.');
    }
}
