<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\TouristVisa;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TouristVisaController extends Controller
{
    /**
     * Display a listing of tourist visa applications.
     */
    public function index(): Response
    {
        // Data will be fetched via API
        return Inertia::render('Profile/TouristVisa/Index');
    }

    /**
     * Show the form for creating a new tourist visa application.
     */
    public function create(): Response
    {
        $countries = Country::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'country_code', 'iso_code']);

        return Inertia::render('Profile/TouristVisa/Create', [
            'countries' => $countries,
        ]);
    }

    /**
     * Display the specified tourist visa application.
     */
    public function show(TouristVisa $touristVisa): Response
    {
        // Ensure user can only view their own applications
        if ($touristVisa->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this application.');
        }

        $touristVisa->load(['destinationCountry', 'documents.documentType', 'documents.userDocument', 'serviceApplication']);

        // Load quotes if serviceApplication exists
        $quotes = [];
        $serviceApplication = null;
        
        if ($touristVisa->serviceApplication) {
            $serviceApplication = $touristVisa->serviceApplication;
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

        return Inertia::render('Profile/TouristVisa/Show', [
            'application' => $touristVisa,
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
            'canEdit' => $touristVisa->canBeEditedByUser(),
            'canDelete' => $touristVisa->canBeDeletedByUser(),
        ]);
    }

    /**
     * Get visa requirements for a specific country (AJAX endpoint).
     */
    public function getRequirements(Request $request, $countryId)
    {
        $country = Country::find($countryId);
        
        if (!$country) {
            return response()->json(['error' => 'Country not found'], 404);
        }

        // Return basic visa information
        // In a full implementation, this would integrate with VisaRequirementService
        return response()->json([
            'success' => true,
            'country' => $country,
            'requirements' => [
                'passport_validity_months' => 6,
                'processing_time_days' => '7-14',
                'required_documents' => [
                    'Valid passport',
                    'Recent passport photos',
                    'Bank statements',
                    'Flight itinerary',
                    'Hotel reservations',
                ],
            ],
            'fees' => [
                'base_fee' => 8500,
                'service_charge' => 3500,
                'total' => 12000,
                'currency' => 'BDT',
            ],
        ]);
    }
}
