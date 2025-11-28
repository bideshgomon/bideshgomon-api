<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CountryDocumentRequirement;
use App\Models\Country;
use App\Models\MasterDocument;
use App\Models\DocumentCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CountryDocumentAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Country::with(['documentRequirements.document.category']);

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $countries = $query->orderBy('name')->paginate(20);

        return Inertia::render('Admin/DocumentAssignments/Index', [
            'countries' => $countries,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        $countries = Country::orderBy('name')->get();
        $categories = DocumentCategory::with('documents')->orderBy('sort_order')->get();
        
        $visaTypes = ['tourist', 'business', 'student', 'work', 'medical', 'transit', 'family'];
        $professions = ['Job Holder', 'Business Person', 'Student'];

        return Inertia::render('Admin/DocumentAssignments/Create', [
            'countries' => $countries,
            'categories' => $categories,
            'visaTypes' => $visaTypes,
            'professions' => $professions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'visa_type' => 'required|string|in:tourist,business,student,work,medical,transit,family',
            'profession_category' => 'nullable|string',
            'document_id' => 'required|exists:master_documents,id',
            'is_mandatory' => 'boolean',
            'specific_notes' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        CountryDocumentRequirement::create($validated);

        return redirect()->route('admin.document-assignments.show', $validated['country_id'])
            ->with('success', 'Document assigned successfully.');
    }

    public function show($countryId)
    {
        $country = Country::with([
            'documentRequirements' => function ($query) {
                $query->orderBy('visa_type')
                    ->orderBy('profession_category')
                    ->orderBy('sort_order');
            },
            'documentRequirements.document.category'
        ])->findOrFail($countryId);

        $categories = DocumentCategory::with('documents')->orderBy('sort_order')->get();
        $visaTypes = ['tourist', 'business', 'student', 'work', 'medical', 'transit', 'family'];
        $professions = ['Job Holder', 'Business Person', 'Student'];

        // Group requirements by visa type and profession
        $groupedRequirements = $country->documentRequirements->groupBy(function ($req) {
            return $req->visa_type . '|' . ($req->profession_category ?? 'all');
        });

        return Inertia::render('Admin/DocumentAssignments/Show', [
            'country' => $country,
            'groupedRequirements' => $groupedRequirements,
            'categories' => $categories,
            'visaTypes' => $visaTypes,
            'professions' => $professions,
        ]);
    }

    public function destroy(CountryDocumentRequirement $assignment)
    {
        $countryId = $assignment->country_id;
        $assignment->delete();

        return redirect()->route('admin.document-assignments.show', $countryId)
            ->with('success', 'Document assignment removed.');
    }

    public function bulkAssign(Request $request)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'visa_type' => 'required|string',
            'profession_category' => 'nullable|string',
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:master_documents,id',
            'is_mandatory' => 'boolean',
        ]);

        $sortOrder = 1;
        foreach ($validated['document_ids'] as $documentId) {
            CountryDocumentRequirement::create([
                'country_id' => $validated['country_id'],
                'visa_type' => $validated['visa_type'],
                'profession_category' => $validated['profession_category'] ?? null,
                'document_id' => $documentId,
                'is_mandatory' => $validated['is_mandatory'] ?? true,
                'sort_order' => $sortOrder++,
            ]);
        }

        return redirect()->route('admin.document-assignments.show', $validated['country_id'])
            ->with('success', count($validated['document_ids']) . ' documents assigned successfully.');
    }
}
