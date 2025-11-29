<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Attestation;
use App\Models\Country;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AttestationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Profile/Attestation/Index');
    }

    public function create(): Response
    {
        $countries = Country::where('status', 'active')->orderBy('name')->get(['id', 'name', 'country_code']);

        $documentTypes = [
            'Educational Certificates', 'Degree/Diploma', 'Transcripts', 'Mark Sheets',
            'Birth Certificate', 'Marriage Certificate', 'Divorce Certificate',
            'Police Clearance', 'Medical Certificate', 'Experience Certificate',
            'Employment Contract', 'Power of Attorney', 'Commercial Documents',
            'Company Registration', 'Business License', 'Other'
        ];

        $attestationTypes = [
            'MOFA Attestation', 'Embassy Attestation', 'HRD Attestation',
            'MEA Attestation', 'Chamber of Commerce', 'Notary Attestation',
            'Home Department', 'SDM Attestation', 'Apostille'
        ];

        $purposes = [
            'Employment Visa', 'Student Visa', 'Business Visa', 'Family Visa',
            'Work Permit', 'Higher Education', 'Immigration', 'Business Setup', 'Other'
        ];

        return Inertia::render('Profile/Attestation/Create', [
            'countries' => $countries,
            'documentTypes' => $documentTypes,
            'attestationTypes' => $attestationTypes,
            'purposes' => $purposes,
        ]);
    }

    public function show(Attestation $attestation): Response
    {
        if ($attestation->user_id !== auth()->id()) {
            abort(403);
        }

        $attestation->load(['targetCountry', 'serviceApplication']);

        $quotes = [];
        $serviceApplication = null;
        
        if ($attestation->serviceApplication) {
            $serviceApplication = $attestation->serviceApplication;
            $quotes = $serviceApplication->quotes()
                ->with(['agency'])
                ->orderBy('quoted_amount', 'asc')
                ->get()
                ->map(function($quote) {
                    return [
                        'id' => $quote->id,
                        'agency_name' => $quote->agency->name ?? 'Unknown',
                        'agency_logo' => $quote->agency->logo_path ?? null,
                        'quoted_amount' => $quote->quoted_amount,
                        'processing_time_days' => $quote->processing_time_days,
                        'status' => $quote->status,
                        'quote_notes' => $quote->quote_notes,
                    ];
                });
        }

        return Inertia::render('Profile/Attestation/Show', [
            'application' => $attestation,
            'serviceApplication' => $serviceApplication,
            'quotes' => $quotes,
            'canEdit' => $attestation->canBeEditedByUser(),
        ]);
    }
}
