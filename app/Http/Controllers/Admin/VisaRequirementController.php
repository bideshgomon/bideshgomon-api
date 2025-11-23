<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisaRequirement;
use App\Models\VisaRequirementDocument;
use App\Models\ProfessionVisaRequirement;
use App\Models\ServiceModule;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VisaRequirementController extends Controller
{
    /**
     * Display visa requirements management dashboard.
     */
    public function index(Request $request)
    {
        $query = VisaRequirement::with(['serviceModule', 'documents', 'professionRequirements']);

        // Filters
        if ($request->filled('country')) {
            $query->where('country', 'like', '%' . $request->country . '%');
        }

        if ($request->filled('visa_type')) {
            $query->where('visa_type', $request->visa_type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $visaRequirements = $query->orderBy('country')
            ->orderBy('visa_type')
            ->paginate(20)
            ->withQueryString();

        // Get statistics
        $stats = [
            'total_requirements' => VisaRequirement::count(),
            'total_countries' => VisaRequirement::distinct('country')->count(),
            'active_requirements' => VisaRequirement::where('is_active', true)->count(),
            'total_documents' => VisaRequirementDocument::count(),
            'profession_variants' => ProfessionVisaRequirement::count(),
        ];

        // Get unique countries for filter
        $countries = VisaRequirement::select('country', 'country_code')
            ->distinct()
            ->orderBy('country')
            ->get()
            ->map(fn($item) => [
                'value' => $item->country,
                'label' => "{$item->country} ({$item->country_code})",
            ]);

        // Visa types
        $visaTypes = [
            ['value' => 'tourist', 'label' => 'Tourist Visa'],
            ['value' => 'business', 'label' => 'Business Visa'],
            ['value' => 'student', 'label' => 'Student Visa'],
            ['value' => 'work', 'label' => 'Work Visa'],
            ['value' => 'medical', 'label' => 'Medical Visa'],
            ['value' => 'transit', 'label' => 'Transit Visa'],
            ['value' => 'family', 'label' => 'Family Visa'],
        ];

        return Inertia::render('Admin/VisaRequirements/Index', [
            'visaRequirements' => $visaRequirements,
            'stats' => $stats,
            'countries' => $countries,
            'visaTypes' => $visaTypes,
            'filters' => $request->only(['country', 'visa_type', 'is_active']),
        ]);
    }

    /**
     * Show detailed view of a visa requirement.
     */
    public function show(VisaRequirement $visaRequirement)
    {
        $visaRequirement->load([
            'serviceModule',
            'documents' => fn($q) => $q->orderBy('sort_order'),
            'professionRequirements' => fn($q) => $q->orderBy('sort_order'),
        ]);

        return Inertia::render('Admin/VisaRequirements/Show', [
            'visaRequirement' => $visaRequirement,
        ]);
    }

    /**
     * Show form to create new visa requirement.
     */
    public function create()
    {
        $serviceModules = ServiceModule::where('service_category_id', function ($query) {
            $query->select('id')
                ->from('service_categories')
                ->where('slug', 'visa')
                ->limit(1);
        })->get(['id', 'name']);

        $professionCategories = [
            'employed' => 'Employed/Salaried',
            'self_employed' => 'Self-Employed',
            'business_owner' => 'Business Owner',
            'student' => 'Student',
            'retired' => 'Retired',
            'unemployed' => 'Unemployed',
            'homemaker' => 'Homemaker',
        ];

        return Inertia::render('Admin/VisaRequirements/Create', [
            'serviceModules' => $serviceModules,
            'professionCategories' => $professionCategories,
        ]);
    }

    /**
     * Store new visa requirement.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_module_id' => 'nullable|exists:service_modules,id',
            'country' => 'required|string|max:255',
            'country_code' => 'required|string|max:3',
            'visa_type' => 'required|string|max:255',
            'visa_category' => 'nullable|string|max:255',
            'general_requirements' => 'required|string',
            'eligibility_criteria' => 'nullable|string',
            'processing_time_info' => 'nullable|string',
            'validity_info' => 'nullable|string',
            'important_notes' => 'nullable|string',
            'min_bank_balance' => 'nullable|numeric|min:0',
            'bank_statement_months' => 'nullable|integer|min:1|max:12',
            'financial_requirements' => 'nullable|string',
            'government_fee' => 'nullable|numeric|min:0',
            'service_fee' => 'nullable|numeric|min:0',
            'processing_fee_standard' => 'nullable|numeric|min:0',
            'processing_fee_express' => 'nullable|numeric|min:0',
            'processing_fee_urgent' => 'nullable|numeric|min:0',
            'processing_days_standard' => 'nullable|integer|min:1',
            'processing_days_express' => 'nullable|integer|min:1',
            'processing_days_urgent' => 'nullable|integer|min:1',
            'interview_required' => 'boolean',
            'interview_details' => 'nullable|string',
            'biometrics_required' => 'boolean',
            'biometrics_details' => 'nullable|string',
            'application_method' => 'nullable|string',
            'embassy_website' => 'nullable|url',
            'application_process' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $visaRequirement = VisaRequirement::create($validated);

        return redirect()
            ->route('admin.visa-requirements.show', $visaRequirement)
            ->with('success', 'Visa requirement created successfully!');
    }

    /**
     * Show form to edit visa requirement.
     */
    public function edit(VisaRequirement $visaRequirement)
    {
        $visaRequirement->load(['documents', 'professionRequirements']);

        $serviceModules = ServiceModule::where('service_category_id', function ($query) {
            $query->select('id')
                ->from('service_categories')
                ->where('slug', 'visa')
                ->limit(1);
        })->get(['id', 'name']);

        $professionCategories = [
            'employed' => 'Employed/Salaried',
            'self_employed' => 'Self-Employed',
            'business_owner' => 'Business Owner',
            'student' => 'Student',
            'retired' => 'Retired',
            'unemployed' => 'Unemployed',
            'homemaker' => 'Homemaker',
        ];

        return Inertia::render('Admin/VisaRequirements/Edit', [
            'visaRequirement' => $visaRequirement,
            'serviceModules' => $serviceModules,
            'professionCategories' => $professionCategories,
        ]);
    }

    /**
     * Update visa requirement.
     */
    public function update(Request $request, VisaRequirement $visaRequirement)
    {
        $validated = $request->validate([
            'service_module_id' => 'nullable|exists:service_modules,id',
            'country' => 'required|string|max:255',
            'country_code' => 'required|string|max:3',
            'visa_type' => 'required|string|max:255',
            'visa_category' => 'nullable|string|max:255',
            'general_requirements' => 'required|string',
            'eligibility_criteria' => 'nullable|string',
            'processing_time_info' => 'nullable|string',
            'validity_info' => 'nullable|string',
            'important_notes' => 'nullable|string',
            'min_bank_balance' => 'nullable|numeric|min:0',
            'bank_statement_months' => 'nullable|integer|min:1|max:12',
            'financial_requirements' => 'nullable|string',
            'government_fee' => 'nullable|numeric|min:0',
            'service_fee' => 'nullable|numeric|min:0',
            'processing_fee_standard' => 'nullable|numeric|min:0',
            'processing_fee_express' => 'nullable|numeric|min:0',
            'processing_fee_urgent' => 'nullable|numeric|min:0',
            'processing_days_standard' => 'nullable|integer|min:1',
            'processing_days_express' => 'nullable|integer|min:1',
            'processing_days_urgent' => 'nullable|integer|min:1',
            'interview_required' => 'boolean',
            'interview_details' => 'nullable|string',
            'biometrics_required' => 'boolean',
            'biometrics_details' => 'nullable|string',
            'application_method' => 'nullable|string',
            'embassy_website' => 'nullable|url',
            'application_process' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $visaRequirement->update($validated);

        return redirect()
            ->route('admin.visa-requirements.show', $visaRequirement)
            ->with('success', 'Visa requirement updated successfully!');
    }

    /**
     * Delete visa requirement.
     */
    public function destroy(VisaRequirement $visaRequirement)
    {
        $visaRequirement->delete();

        return redirect()
            ->route('admin.visa-requirements.index')
            ->with('success', 'Visa requirement deleted successfully!');
    }

    /**
     * Add document to visa requirement.
     */
    public function addDocument(Request $request, VisaRequirement $visaRequirement)
    {
        $validated = $request->validate([
            'document_name' => 'required|string|max:255',
            'document_type' => 'nullable|string|max:255',
            'description' => 'required|string',
            'specifications' => 'nullable|string',
            'is_mandatory' => 'boolean',
            'conditions' => 'nullable|string',
            'quantity' => 'integer|min:1',
            'format' => 'nullable|string',
            'sample_url' => 'nullable|url',
            'common_mistakes' => 'nullable|string',
        ]);

        $visaRequirement->documents()->create($validated);

        return back()->with('success', 'Document added successfully!');
    }

    /**
     * Add profession-specific requirement.
     */
    public function addProfessionRequirement(Request $request, VisaRequirement $visaRequirement)
    {
        $validated = $request->validate([
            'profession_category' => 'required|string|max:255',
            'profession_title' => 'nullable|string|max:255',
            'additional_requirements' => 'required|string',
            'additional_documents' => 'nullable|string',
            'min_bank_balance_override' => 'nullable|numeric|min:0',
            'financial_proof_details' => 'nullable|string',
            'fee_adjustment' => 'nullable|numeric',
            'fee_adjustment_type' => 'nullable|in:fixed,percentage',
            'employment_proof_requirements' => 'nullable|string',
            'income_proof_requirements' => 'nullable|string',
            'special_conditions' => 'nullable|string',
            'rejection_risks' => 'nullable|string',
            'success_tips' => 'nullable|string',
            'risk_level' => 'integer|min:1|max:3',
        ]);

        $visaRequirement->professionRequirements()->create($validated);

        return back()->with('success', 'Profession requirement added successfully!');
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(VisaRequirement $visaRequirement)
    {
        $visaRequirement->update([
            'is_active' => !$visaRequirement->is_active,
        ]);

        return back()->with('success', 'Status updated successfully!');
    }
}
