<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class DocumentTypeController extends Controller
{
    protected $entityName = 'Document Type';
    protected $entityNamePlural = 'Document Types';
    protected $indexRoute = 'admin.data.document-types.index';

    /**
     * Display a listing of document types
     */
    public function index(Request $request)
    {
        $query = DocumentType::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_bn', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        // Sort
        $sortField = $request->get('sort', 'sort_order');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $documentTypes = $query->paginate($request->get('per_page', 15))
            ->withQueryString();

        // Get unique categories for filter
        $categories = DocumentType::select('category')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        return Inertia::render('Admin/DataManagement/DocumentTypes/Index', [
            'documentTypes' => $documentTypes,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category', 'is_active']),
            'sort' => [
                'field' => $sortField,
                'direction' => $sortDirection,
            ],
        ]);
    }

    /**
     * Show the form for creating a new document type
     */
    public function create()
    {
        return Inertia::render('Admin/DataManagement/DocumentTypes/Create');
    }

    /**
     * Store a newly created document type
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'name_bn' => 'nullable|string|max:100',
            'slug' => 'nullable|string|max:100|unique:document_types,slug',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:50',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        try {
            DocumentType::create($validated);

            return redirect()->route('admin.data.document-types.index')
                ->with('success', 'Document type created successfully.');
        } catch (\Exception $e) {
            Log::error('Document type creation failed', [
                'error' => $e->getMessage(),
                'data' => $validated,
            ]);

            return back()->withInput()->with('error', 'Failed to create document type: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified document type
     */
    public function show(DocumentType $documentType)
    {
        return Inertia::render('Admin/DataManagement/DocumentTypes/Show', [
            'documentType' => $documentType,
        ]);
    }

    /**
     * Show the form for editing the specified document type
     */
    public function edit(DocumentType $documentType)
    {
        return Inertia::render('Admin/DataManagement/DocumentTypes/Edit', [
            'documentType' => $documentType,
        ]);
    }

    /**
     * Update the specified document type
     */
    public function update(Request $request, DocumentType $documentType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'name_bn' => 'nullable|string|max:100',
            'slug' => ['nullable', 'string', 'max:100', Rule::unique('document_types')->ignore($documentType->id)],
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:50',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        try {
            $documentType->update($validated);

            return redirect()->route('admin.data.document-types.index')
                ->with('success', 'Document type updated successfully.');
        } catch (\Exception $e) {
            Log::error('Document type update failed', [
                'document_type_id' => $documentType->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'Failed to update document type: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified document type
     */
    public function destroy(DocumentType $documentType)
    {
        try {
            $documentType->delete();

            return redirect()->route('admin.data.document-types.index')
                ->with('success', 'Document type deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Document type deletion failed', [
                'document_type_id' => $documentType->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to delete document type: ' . $e->getMessage());
        }
    }

    /**
     * Toggle document type status
     */
    public function toggleStatus(DocumentType $documentType)
    {
        try {
            $documentType->update(['is_active' => !$documentType->is_active]);

            return back()->with('success', 'Document type status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Document type status toggle failed', [
                'document_type_id' => $documentType->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to toggle status: ' . $e->getMessage());
        }
    }

    /**
     * Export document types to CSV
     */
    public function export(Request $request)
    {
        $query = DocumentType::query();

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        $documentTypes = $query->orderBy('sort_order')->get();

        $csvData = "Name,Name (Bengali),Slug,Description,Category,Required,Active,Sort Order\n";
        
        foreach ($documentTypes as $type) {
            $csvData .= sprintf(
                "\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",%d\n",
                $type->name,
                $type->name_bn ?? '',
                $type->slug,
                str_replace('"', '""', $type->description ?? ''),
                $type->category ?? '',
                $type->is_required ? 'Yes' : 'No',
                $type->is_active ? 'Yes' : 'No',
                $type->sort_order
            );
        }

        return response($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="document-types-' . date('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Download bulk upload template
     */
    public function downloadTemplate()
    {
        $csvData = "Name,Name (Bengali),Slug,Description,Category,Required,Active,Sort Order\n";
        $csvData .= "\"Passport\",\"পাসপোর্ট\",\"passport\",\"Valid passport copy\",\"Identity\",\"Yes\",\"Yes\",1\n";
        $csvData .= "\"National ID Card\",\"জাতীয় পরিচয়পত্র\",\"national-id-card\",\"NID front and back copy\",\"Identity\",\"Yes\",\"Yes\",2\n";
        $csvData .= "\"Bank Statement\",\"ব্যাংক স্টেটমেন্ট\",\"bank-statement\",\"Last 6 months bank statement\",\"Financial\",\"No\",\"Yes\",3\n";

        return response($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="document-types-template.csv"',
        ]);
    }

    /**
     * Show bulk upload form
     */
    public function bulkUpload()
    {
        return Inertia::render('Admin/DataManagement/DocumentTypes/BulkUpload', [
            'entityName' => $this->entityName,
            'entityNamePlural' => $this->entityNamePlural,
            'backRoute' => $this->indexRoute,
        ]);
    }

    /**
     * Process bulk upload
     */
    public function processBulkUpload(Request $request)
    {
        // Simple implementation - can be enhanced later
        return back()->with('info', 'Bulk upload feature coming soon.');
    }
}
