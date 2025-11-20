<?php

namespace App\Http\Controllers;

use App\Models\VisaApplication;
use App\Models\VisaDocument;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VisaApplicationController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display visa types and countries
     */
    public function index()
    {
        $visaTypes = [
            'tourist' => [
                'name' => 'Tourist Visa',
                'description' => 'For leisure, vacation, or visiting family and friends',
                'icon' => 'camera',
            ],
            'business' => [
                'name' => 'Business Visa',
                'description' => 'For business meetings, conferences, or trade activities',
                'icon' => 'briefcase',
            ],
            'student' => [
                'name' => 'Student Visa',
                'description' => 'For pursuing education or academic programs',
                'icon' => 'academic-cap',
            ],
            'work' => [
                'name' => 'Work Visa',
                'description' => 'For employment or professional work opportunities',
                'icon' => 'identification',
            ],
            'medical' => [
                'name' => 'Medical Visa',
                'description' => 'For medical treatment or healthcare services',
                'icon' => 'heart',
            ],
            'transit' => [
                'name' => 'Transit Visa',
                'description' => 'For passing through a country en route to another destination',
                'icon' => 'globe',
            ],
        ];

        $popularCountries = [
            ['name' => 'United States', 'code' => 'USA', 'flag' => '🇺🇸', 'service_fee' => 15000],
            ['name' => 'United Kingdom', 'code' => 'GBR', 'flag' => '🇬🇧', 'service_fee' => 12000],
            ['name' => 'Canada', 'code' => 'CAN', 'flag' => '🇨🇦', 'service_fee' => 10000],
            ['name' => 'Australia', 'code' => 'AUS', 'flag' => '🇦🇺', 'service_fee' => 13000],
            ['name' => 'Schengen', 'code' => 'SCH', 'flag' => '🇪🇺', 'service_fee' => 8000],
            ['name' => 'United Arab Emirates', 'code' => 'ARE', 'flag' => '🇦🇪', 'service_fee' => 6000],
            ['name' => 'Singapore', 'code' => 'SGP', 'flag' => '🇸🇬', 'service_fee' => 7000],
            ['name' => 'Malaysia', 'code' => 'MYS', 'flag' => '🇲🇾', 'service_fee' => 3000],
            ['name' => 'Thailand', 'code' => 'THA', 'flag' => '🇹🇭', 'service_fee' => 2500],
            ['name' => 'India', 'code' => 'IND', 'flag' => '🇮🇳', 'service_fee' => 3500],
        ];

        return Inertia::render('Services/Visa/Index', [
            'visaTypes' => $visaTypes,
            'popularCountries' => $popularCountries,
        ]);
    }

    /**
     * Show visa application form
     */
    public function create(Request $request)
    {
        $visaType = $request->query('type', 'tourist');
        $country = $request->query('country');

        $documentRequirements = $this->getDocumentRequirements($visaType);

        return Inertia::render('Services/Visa/Apply', [
            'visaType' => $visaType,
            'selectedCountry' => $country,
            'documentRequirements' => $documentRequirements,
        ]);
    }

    /**
     * Store new visa application
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'visa_type' => 'required|in:tourist,business,student,work,medical,transit',
            'destination_country' => 'required|string|max:100',
            'destination_country_code' => 'required|string|max:10',
            'visa_category' => 'required|in:single_entry,multiple_entry,transit',
            'duration_days' => 'nullable|integer|min:1|max:365',
            'applicant_name' => 'required|string|max:255',
            'applicant_email' => 'required|email|max:255',
            'applicant_phone' => 'required|string|max:20',
            'applicant_dob' => 'required|date|before:today',
            'passport_number' => 'required|string|max:50',
            'passport_issue_date' => 'required|date',
            'passport_expiry_date' => 'required|date|after:passport_issue_date',
            'passport_issuing_country' => 'required|string|max:100',
            'nationality' => 'required|string|max:100',
            'intended_travel_date' => 'required|date|after:today',
            'return_date' => 'nullable|date|after:intended_travel_date',
            'travel_purpose' => 'required|string|max:500',
            'occupation' => 'nullable|string|max:100',
            'employer_name' => 'nullable|string|max:255',
            'previous_visa_rejected' => 'nullable|boolean',
            'processing_type' => 'required|in:standard,express,urgent',
            'service_fee' => 'required|numeric|min:0',
            'government_fee' => 'required|numeric|min:0',
            'processing_fee' => 'required|numeric|min:0',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'draft';
        $validated['payment_status'] = 'pending';
        $validated['total_amount'] = $validated['service_fee'] + $validated['government_fee'] + $validated['processing_fee'];
        $validated['ip_address'] = $request->ip();
        $validated['user_agent'] = $request->userAgent();

        // Set processing days based on type
        $validated['processing_days'] = match($validated['processing_type']) {
            'standard' => 15,
            'express' => 7,
            'urgent' => 3,
        };

        $application = VisaApplication::create($validated);

        return redirect()->route('visa.show', $application)
            ->with('success', 'Visa application created successfully. Please upload required documents.');
    }

    /**
     * Display user's applications
     */
    public function myApplications()
    {
        $applications = VisaApplication::forUser(auth()->id())
            ->with(['documents', 'appointments'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Services/Visa/MyApplications', [
            'applications' => $applications,
        ]);
    }

    /**
     * Show specific application
     */
    public function show(VisaApplication $application)
    {
        $this->authorize('view', $application);

        $application->load(['documents', 'appointments', 'user']);

        return Inertia::render('Services/Visa/ShowApplication', [
            'application' => $application,
        ]);
    }

    /**
     * Upload document for application
     */
    public function uploadDocument(Request $request, VisaApplication $application)
    {
        $this->authorize('update', $application);

        $validated = $request->validate([
            'document_type' => 'required|in:passport,photo,bank_statement,invitation_letter,travel_itinerary,accommodation_proof,employment_letter,education_certificate,medical_reports,police_clearance,sponsor_letter,other',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'document_number' => 'nullable|string|max:100',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'notes' => 'nullable|string|max:500',
        ]);

        // Upload file
        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('visa-documents/' . $application->id, $filename, 'public');

        $validated['visa_application_id'] = $application->id;
        $validated['file_path'] = $path;
        $validated['file_name'] = $file->getClientOriginalName();
        $validated['file_size'] = $file->getSize();
        $validated['mime_type'] = $file->getMimeType();
        $validated['status'] = 'pending';
        $validated['uploaded_by'] = auth()->id();

        $document = VisaDocument::create($validated);

        return back()->with('success', 'Document uploaded successfully.');
    }

    /**
     * Show payment page
     */
    public function payment(VisaApplication $application)
    {
        $this->authorize('view', $application);

        if ($application->isPaid()) {
            return redirect()->route('visa.show', $application)
                ->with('info', 'This application has already been paid for.');
        }

        return Inertia::render('Services/Visa/Payment', [
            'application' => $application,
        ]);
    }

    /**
     * Process payment
     */
    public function processPayment(Request $request, VisaApplication $application)
    {
        $this->authorize('update', $application);

        $validated = $request->validate([
            'payment_method' => 'required|in:bkash,nagad,rocket,bank_transfer,card',
            'transaction_id' => 'required|string|max:100',
        ]);

        $application->update([
            'payment_method' => $validated['payment_method'],
            'payment_transaction_id' => $validated['transaction_id'],
            'payment_status' => 'paid',
            'paid_at' => now(),
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('visa.show', $application)
            ->with('success', 'Payment successful! Your visa application has been submitted.');
    }

    /**
     * Cancel application
     */
    public function cancel(VisaApplication $application)
    {
        $this->authorize('delete', $application);

        if ($application->isApproved() || $application->isRejected()) {
            return back()->with('error', 'Cannot cancel an application that has already been processed.');
        }

        $application->cancel();

        return back()->with('success', 'Application cancelled successfully.');
    }

    /**
     * Get document requirements based on visa type
     */
    private function getDocumentRequirements(string $visaType): array
    {
        $common = ['passport', 'photo', 'bank_statement', 'travel_itinerary', 'accommodation_proof'];

        $specific = match($visaType) {
            'business' => ['invitation_letter', 'employment_letter'],
            'student' => ['education_certificate', 'admission_letter'],
            'work' => ['employment_letter', 'work_permit'],
            'medical' => ['medical_reports', 'hospital_invitation'],
            default => [],
        };

        return array_merge($common, $specific);
    }
}
