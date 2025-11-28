<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterDocument;
use App\Models\DocumentCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MasterDocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterDocument::with('category');

        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('search')) {
            $query->where('document_name', 'like', '%' . $request->search . '%');
        }

        $documents = $query->orderBy('category_id')->orderBy('sort_order')->paginate(20);
        $categories = DocumentCategory::orderBy('sort_order')->get();

        return Inertia::render('Admin/MasterDocuments/Index', [
            'documents' => $documents,
            'categories' => $categories,
            'filters' => $request->only(['category', 'search']),
        ]);
    }

    public function create()
    {
        $categories = DocumentCategory::orderBy('sort_order')->get();

        return Inertia::render('Admin/MasterDocuments/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:document_categories,id',
            'document_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'specifications' => 'nullable|string',
            'translation_required' => 'boolean',
            'notarization_required' => 'boolean',
            'typical_validity_days' => 'nullable|integer',
            'international_standard' => 'nullable|string|max:50',
            'example_url' => 'nullable|url|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $document = MasterDocument::create($validated);

        return redirect()->route('admin.master-documents.index')
            ->with('success', 'Document created successfully.');
    }

    public function show(MasterDocument $masterDocument)
    {
        $masterDocument->load(['category', 'countryRequirements.country']);

        return Inertia::render('Admin/MasterDocuments/Show', [
            'document' => $masterDocument,
        ]);
    }

    public function edit(MasterDocument $masterDocument)
    {
        $categories = DocumentCategory::orderBy('sort_order')->get();

        return Inertia::render('Admin/MasterDocuments/Edit', [
            'document' => $masterDocument,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, MasterDocument $masterDocument)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:document_categories,id',
            'document_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'specifications' => 'nullable|string',
            'translation_required' => 'boolean',
            'notarization_required' => 'boolean',
            'typical_validity_days' => 'nullable|integer',
            'international_standard' => 'nullable|string|max:50',
            'example_url' => 'nullable|url|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $masterDocument->update($validated);

        return redirect()->route('admin.master-documents.index')
            ->with('success', 'Document updated successfully.');
    }

    public function destroy(MasterDocument $masterDocument)
    {
        $masterDocument->delete();

        return redirect()->route('admin.master-documents.index')
            ->with('success', 'Document deleted successfully.');
    }
}
