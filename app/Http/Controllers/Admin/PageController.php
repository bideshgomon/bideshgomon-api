<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Page::with('updatedBy');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->byType($request->type);
        }

        // Filter by published status
        if ($request->has('status')) {
            if ($request->status === 'published') {
                $query->published();
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        $pages = $query->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Pages/Index', [
            'pages' => $pages,
            'filters' => $request->only(['search', 'type', 'status']),
            'pageTypes' => [
                'terms' => 'Terms of Service',
                'privacy' => 'Privacy Policy',
                'refund' => 'Refund Policy',
                'about' => 'About Us',
                'help' => 'Help Center',
                'contact' => 'Contact Us',
                'custom' => 'Custom Page',
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Pages/Create', [
            'pageTypes' => [
                'terms' => 'Terms of Service',
                'privacy' => 'Privacy Policy',
                'refund' => 'Refund Policy',
                'about' => 'About Us',
                'help' => 'Help Center',
                'contact' => 'Contact Us',
                'custom' => 'Custom Page',
            ],
            'templates' => [
                'default' => 'Default Template',
                'legal' => 'Legal Document',
                'informational' => 'Informational',
                'full-width' => 'Full Width',
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_bn' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'content' => 'required|string',
            'content_bn' => 'nullable|string',
            'page_type' => 'required|string|in:terms,privacy,refund,about,help,contact,custom',
            'template' => 'required|string|in:default,legal,informational,full-width',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'is_published' => 'boolean',
            'show_in_footer' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $validated['updated_by'] = auth()->id();
        $validated['published_at'] = $validated['is_published'] ? now() : null;

        Page::create($validated);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Page $page)
    {
        $page->load('updatedBy');

        return Inertia::render('Admin/Pages/Show', [
            'page' => $page,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Page $page)
    {
        return Inertia::render('Admin/Pages/Edit', [
            'page' => $page,
            'pageTypes' => [
                'terms' => 'Terms of Service',
                'privacy' => 'Privacy Policy',
                'refund' => 'Refund Policy',
                'about' => 'About Us',
                'help' => 'Help Center',
                'contact' => 'Contact Us',
                'custom' => 'Custom Page',
            ],
            'templates' => [
                'default' => 'Default Template',
                'legal' => 'Legal Document',
                'informational' => 'Informational',
                'full-width' => 'Full Width',
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_bn' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug,' . $page->id,
            'content' => 'required|string',
            'content_bn' => 'nullable|string',
            'page_type' => 'required|string|in:terms,privacy,refund,about,help,contact,custom',
            'template' => 'required|string|in:default,legal,informational,full-width',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'is_published' => 'boolean',
            'show_in_footer' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Auto-update slug if changed
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $validated['updated_by'] = auth()->id();
        
        // Update published_at when publishing
        if ($validated['is_published'] && !$page->is_published) {
            $validated['published_at'] = now();
        }

        $page->update($validated);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page deleted successfully.');
    }

    /**
     * Toggle published status
     */
    public function togglePublished(Page $page)
    {
        $page->update([
            'is_published' => !$page->is_published,
            'published_at' => !$page->is_published ? now() : $page->published_at,
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Page status updated successfully.');
    }

    /**
     * Duplicate a page
     */
    public function duplicate(Page $page)
    {
        $newPage = $page->replicate();
        $newPage->title = $page->title . ' (Copy)';
        $newPage->slug = Str::slug($newPage->title) . '-' . time();
        $newPage->is_published = false;
        $newPage->published_at = null;
        $newPage->updated_by = auth()->id();
        $newPage->save();

        return redirect()->route('admin.pages.edit', $newPage)
            ->with('success', 'Page duplicated successfully.');
    }
}
