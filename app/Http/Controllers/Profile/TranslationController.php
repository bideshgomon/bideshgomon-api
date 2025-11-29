<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Translation;
use App\Models\Country;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TranslationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Profile/Translation/Index');
    }

    public function create(): Response
    {
        $languages = [
            'English', 'Arabic', 'Bengali', 'Hindi', 'Urdu', 'Chinese', 'French', 
            'Spanish', 'German', 'Japanese', 'Korean', 'Russian', 'Italian', 
            'Portuguese', 'Turkish', 'Persian', 'Malay', 'Indonesian', 'Thai', 'Other'
        ];

        $documentTypes = [
            'Passport', 'Birth Certificate', 'Marriage Certificate', 'Divorce Certificate',
            'Death Certificate', 'Academic Transcripts', 'Degree Certificate', 'Diploma',
            'Employment Contract', 'Experience Certificate', 'Police Clearance',
            'Medical Reports', 'Bank Statements', 'Property Documents', 'Legal Documents',
            'Business Registration', 'Power of Attorney', 'Affidavit', 'Other'
        ];

        $certificationTypes = [
            'Standard Translation', 
            'Certified Translation', 
            'Notarized Translation',
            'Apostille Translation'
        ];

        return Inertia::render('Profile/Translation/Create', [
            'languages' => $languages,
            'documentTypes' => $documentTypes,
            'certificationTypes' => $certificationTypes,
        ]);
    }

    public function show(Translation $translation): Response
    {
        if ($translation->user_id !== auth()->id()) {
            abort(403);
        }

        $translation->load(['serviceApplication']);

        $quotes = [];
        $serviceApplication = null;
        
        if ($translation->serviceApplication) {
            $serviceApplication = $translation->serviceApplication;
            $quotes = $serviceApplication->quotes()
                ->with(['agency' => function($query) {
                    $query->select('id', 'name', 'email', 'phone', 'logo_path');
                }])
                ->orderBy('quoted_amount', 'asc')
                ->get()
                ->map(function($quote) {
                    return [
                        'id' => $quote->id,
                        'agency_name' => $quote->agency->name ?? 'Unknown Agency',
                        'agency_logo' => $quote->agency->logo_path ?? null,
                        'quoted_amount' => $quote->quoted_amount,
                        'processing_time_days' => $quote->processing_time_days,
                        'valid_until' => $quote->valid_until,
                        'quote_notes' => $quote->quote_notes,
                        'status' => $quote->status,
                        'is_expired' => $quote->isExpired(),
                    ];
                });
        }

        return Inertia::render('Profile/Translation/Show', [
            'application' => $translation,
            'serviceApplication' => $serviceApplication,
            'quotes' => $quotes,
            'hasQuotes' => $quotes->isNotEmpty(),
            'canEdit' => $translation->canBeEditedByUser(),
        ]);
    }
}
