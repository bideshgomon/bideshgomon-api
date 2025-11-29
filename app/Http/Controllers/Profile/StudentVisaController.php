<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\StudentVisa;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentVisaController extends Controller
{
    /**
     * Display a listing of student visa applications.
     */
    public function index(): Response
    {
        // Data will be fetched via API
        return Inertia::render('Profile/StudentVisa/Index');
    }

    /**
     * Show the form for creating a new student visa application.
     */
    public function create(): Response
    {
        $countries = Country::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'country_code', 'iso_code']);

        $educationLevels = [
            'High School',
            'Diploma',
            'Bachelor\'s Degree',
            'Master\'s Degree',
            'PhD',
            'Certificate Course',
            'Language Course',
            'Other',
        ];

        $studyFields = [
            'Engineering',
            'Computer Science',
            'Business Administration',
            'Medicine',
            'Nursing',
            'Accounting',
            'Law',
            'Arts & Humanities',
            'Natural Sciences',
            'Social Sciences',
            'Education',
            'Agriculture',
            'Architecture',
            'Other',
        ];

        return Inertia::render('Profile/StudentVisa/Create', [
            'countries' => $countries,
            'educationLevels' => $educationLevels,
            'studyFields' => $studyFields,
        ]);
    }

    /**
     * Display the specified student visa application.
     */
    public function show(StudentVisa $studentVisa): Response
    {
        // Ensure user can only view their own applications
        if ($studentVisa->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this application.');
        }

        $studentVisa->load(['destinationCountry', 'documents.documentType', 'documents.userDocument', 'serviceApplication']);

        // Load quotes if serviceApplication exists
        $quotes = [];
        $serviceApplication = null;
        
        if ($studentVisa->serviceApplication) {
            $serviceApplication = $studentVisa->serviceApplication;
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
                        'agency_phone' => $quote->agency->phone ?? null,
                        'quoted_amount' => $quote->quoted_amount,
                        'processing_time_days' => $quote->processing_time_days,
                        'valid_until' => $quote->valid_until,
                        'quote_notes' => $quote->quote_notes,
                        'status' => $quote->status,
                        'is_expired' => $quote->isExpired(),
                        'created_at' => $quote->created_at,
                    ];
                });
        }

        $countries = Country::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'country_code', 'iso_code']);

        return Inertia::render('Profile/StudentVisa/Show', [
            'application' => $studentVisa,
            'serviceApplication' => $serviceApplication ? [
                'id' => $serviceApplication->id,
                'application_number' => $serviceApplication->application_number,
                'status' => $serviceApplication->status,
                'quoted_amount' => $serviceApplication->quoted_amount,
                'agency_id' => $serviceApplication->agency_id,
            ] : null,
            'quotes' => $quotes,
            'hasQuotes' => $quotes->isNotEmpty(),
            'hasAcceptedQuote' => $serviceApplication && $serviceApplication->agency_id !== null,
            'countries' => $countries,
            'canEdit' => $studentVisa->canBeEditedByUser(),
            'canDelete' => $studentVisa->canBeDeletedByUser(),
        ]);
    }

    /**
     * Get student visa requirements for a specific country (AJAX endpoint).
     */
    public function getRequirements(Request $request, $countryId)
    {
        $country = Country::with([
            'documentRequirements' => function($query) use ($request) {
                $query->where('visa_type', 'student')
                    ->orderBy('is_mandatory', 'desc')
                    ->orderBy('sort_order');
                
                // Filter by education level if provided
                if ($request->education_level) {
                    $query->where(function($q) use ($request) {
                        $q->whereNull('education_level')
                          ->orWhere('education_level', $request->education_level);
                    });
                }
            },
            'documentRequirements.document.category'
        ])->find($countryId);
        
        if (!$country) {
            return response()->json(['error' => 'Country not found'], 404);
        }

        // Group requirements by mandatory/optional
        $mandatoryDocs = $country->documentRequirements
            ->where('is_mandatory', true)
            ->map(function($req) {
                return [
                    'id' => $req->document->id,
                    'name' => $req->document->document_name,
                    'category' => $req->document->category->name ?? 'General',
                    'description' => $req->document->description,
                    'specific_notes' => $req->specific_notes,
                    'education_level' => $req->education_level,
                ];
            })->values();

        $optionalDocs = $country->documentRequirements
            ->where('is_mandatory', false)
            ->map(function($req) {
                return [
                    'id' => $req->document->id,
                    'name' => $req->document->document_name,
                    'category' => $req->document->category->name ?? 'General',
                    'description' => $req->document->description,
                    'specific_notes' => $req->specific_notes,
                    'education_level' => $req->education_level,
                ];
            })->values();

        return response()->json([
            'success' => true,
            'country' => [
                'id' => $country->id,
                'name' => $country->name,
            ],
            'requirements' => [
                'mandatory_documents' => $mandatoryDocs,
                'optional_documents' => $optionalDocs,
                'total_documents' => $mandatoryDocs->count() + $optionalDocs->count(),
            ],
            'study_info' => [
                'popular_institutions' => $this->getPopularInstitutions($countryId),
                'average_tuition' => $this->getAverageTuition($countryId),
                'processing_time' => $this->getProcessingTime($countryId),
            ],
        ]);
    }

    /**
     * Get popular educational institutions for a country
     */
    private function getPopularInstitutions($countryId)
    {
        // This would be fetched from database in production
        $institutions = [
            1 => ['University of Sydney', 'University of Melbourne', 'ANU'],
            2 => ['University of Toronto', 'UBC', 'McGill University'],
            3 => ['Harvard', 'MIT', 'Stanford'],
            // Add more countries
        ];

        return $institutions[$countryId] ?? [];
    }

    /**
     * Get average tuition range for a country
     */
    private function getAverageTuition($countryId)
    {
        $tuition = [
            1 => ['min' => 15000, 'max' => 40000, 'currency' => 'AUD'],
            2 => ['min' => 20000, 'max' => 35000, 'currency' => 'CAD'],
            3 => ['min' => 30000, 'max' => 70000, 'currency' => 'USD'],
        ];

        return $tuition[$countryId] ?? null;
    }

    /**
     * Get typical processing time for student visa
     */
    private function getProcessingTime($countryId)
    {
        $times = [
            1 => '4-8 weeks',
            2 => '8-12 weeks',
            3 => '2-4 months',
        ];

        return $times[$countryId] ?? '4-12 weeks';
    }
}
