<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\WorkVisa;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WorkVisaController extends Controller
{
    /**
     * Display a listing of work visa applications.
     */
    public function index(): Response
    {
        // Data will be fetched via API
        return Inertia::render('Profile/WorkVisa/Index');
    }

    /**
     * Show the form for creating a new work visa application.
     */
    public function create(): Response
    {
        $countries = Country::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'country_code', 'iso_code']);

        $jobCategories = [
            'Skilled Worker',
            'Professional',
            'Manager',
            'Executive',
            'Technical Specialist',
            'Healthcare Professional',
            'Engineer',
            'IT Professional',
            'Hospitality',
            'Construction',
            'Agriculture',
            'Manufacturing',
            'Education',
            'Other',
        ];

        $employmentTypes = [
            'Permanent Full-Time',
            'Temporary Contract',
            'Seasonal Work',
            'Internship',
            'Transfer (Intra-company)',
        ];

        $experienceLevels = [
            'Entry Level (0-2 years)',
            'Mid Level (2-5 years)',
            'Senior Level (5-10 years)',
            'Expert Level (10+ years)',
        ];

        return Inertia::render('Profile/WorkVisa/Create', [
            'countries' => $countries,
            'jobCategories' => $jobCategories,
            'employmentTypes' => $employmentTypes,
            'experienceLevels' => $experienceLevels,
        ]);
    }

    /**
     * Display the specified work visa application.
     */
    public function show(WorkVisa $workVisa): Response
    {
        // Ensure user can only view their own applications
        if ($workVisa->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this application.');
        }

        $workVisa->load(['destinationCountry', 'documents.documentType', 'documents.userDocument', 'serviceApplication']);

        // Load quotes if serviceApplication exists
        $quotes = [];
        $serviceApplication = null;
        
        if ($workVisa->serviceApplication) {
            $serviceApplication = $workVisa->serviceApplication;
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

        return Inertia::render('Profile/WorkVisa/Show', [
            'application' => $workVisa,
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
            'canEdit' => $workVisa->canBeEditedByUser(),
            'canDelete' => $workVisa->canBeDeletedByUser(),
        ]);
    }

    /**
     * Get work visa requirements for a specific country (AJAX endpoint).
     */
    public function getRequirements(Request $request, $countryId)
    {
        $country = Country::with([
            'documentRequirements' => function($query) use ($request) {
                $query->where('visa_type', 'work')
                    ->orderBy('is_mandatory', 'desc')
                    ->orderBy('sort_order');
                
                // Filter by job category if provided
                if ($request->job_category) {
                    $query->where(function($q) use ($request) {
                        $q->whereNull('job_category')
                          ->orWhere('job_category', $request->job_category);
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
                    'job_category' => $req->job_category,
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
                    'job_category' => $req->job_category,
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
            'work_info' => [
                'popular_industries' => $this->getPopularIndustries($countryId),
                'average_salary' => $this->getAverageSalary($countryId),
                'processing_time' => $this->getProcessingTime($countryId),
                'work_permit_types' => $this->getWorkPermitTypes($countryId),
            ],
        ]);
    }

    /**
     * Get popular industries for work visas in a country
     */
    private function getPopularIndustries($countryId)
    {
        $industries = [
            1 => ['IT & Technology', 'Healthcare', 'Engineering', 'Construction'],
            2 => ['Healthcare', 'IT', 'Skilled Trades', 'Agriculture'],
            3 => ['Technology', 'Healthcare', 'Finance', 'Engineering'],
            // Middle East
            4 => ['Construction', 'Healthcare', 'Hospitality', 'Engineering'],
            5 => ['Oil & Gas', 'Construction', 'Healthcare', 'IT'],
        ];

        return $industries[$countryId] ?? ['General Employment'];
    }

    /**
     * Get average salary range for a country
     */
    private function getAverageSalary($countryId)
    {
        $salaries = [
            1 => ['min' => 50000, 'max' => 90000, 'currency' => 'AUD', 'period' => 'year'],
            2 => ['min' => 45000, 'max' => 80000, 'currency' => 'CAD', 'period' => 'year'],
            3 => ['min' => 60000, 'max' => 120000, 'currency' => 'USD', 'period' => 'year'],
            4 => ['min' => 1500, 'max' => 3500, 'currency' => 'SAR', 'period' => 'month'],
            5 => ['min' => 2000, 'max' => 5000, 'currency' => 'AED', 'period' => 'month'],
        ];

        return $salaries[$countryId] ?? null;
    }

    /**
     * Get typical processing time for work visa
     */
    private function getProcessingTime($countryId)
    {
        $times = [
            1 => '8-12 weeks',
            2 => '4-8 weeks',
            3 => '2-6 months',
            4 => '4-8 weeks',
            5 => '6-10 weeks',
        ];

        return $times[$countryId] ?? '4-12 weeks';
    }

    /**
     * Get work permit types available in country
     */
    private function getWorkPermitTypes($countryId)
    {
        $types = [
            1 => ['Temporary Skill Shortage (TSS)', 'Employer Nomination', 'Skilled Independent'],
            2 => ['Temporary Foreign Worker', 'Express Entry', 'Provincial Nominee'],
            3 => ['H-1B', 'L-1', 'O-1', 'TN'],
            4 => ['Iqama (Work Permit)', 'Temporary Work Visa'],
            5 => ['Employment Visa', 'Mission Visa'],
        ];

        return $types[$countryId] ?? ['General Work Permit'];
    }
}
