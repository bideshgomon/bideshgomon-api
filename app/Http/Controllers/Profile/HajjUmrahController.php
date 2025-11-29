<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\HajjUmrah;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HajjUmrahController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Profile/HajjUmrah/Index');
    }

    public function create(): Response
    {
        $packageTypes = [
            'Hajj Package', 'Umrah Package', 'Hajj + Umrah Package'
        ];

        $accommodationTypes = [
            'Economy (Shared Room)', 'Standard (4-6 persons)', 
            'Premium (2-3 persons)', 'VIP (Private Room)'
        ];

        $mealPlans = [
            'Without Meals', 'Breakfast Only', 'Half Board (Breakfast + Dinner)',
            'Full Board (All Meals)'
        ];

        $transportOptions = [
            'Standard Bus', 'AC Coach', 'Private Vehicle'
        ];

        $durations = [
            '7 Days', '10 Days', '14 Days', '21 Days', '30 Days', '40 Days', 'Custom'
        ];

        return Inertia::render('Profile/HajjUmrah/Create', [
            'packageTypes' => $packageTypes,
            'accommodationTypes' => $accommodationTypes,
            'mealPlans' => $mealPlans,
            'transportOptions' => $transportOptions,
            'durations' => $durations,
        ]);
    }

    public function show(HajjUmrah $hajjUmrah): Response
    {
        if ($hajjUmrah->user_id !== auth()->id()) {
            abort(403);
        }

        $hajjUmrah->load(['serviceApplication']);

        $quotes = [];
        $serviceApplication = null;
        
        if ($hajjUmrah->serviceApplication) {
            $serviceApplication = $hajjUmrah->serviceApplication;
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
                        'package_details' => $quote->quote_notes,
                        'status' => $quote->status,
                    ];
                });
        }

        return Inertia::render('Profile/HajjUmrah/Show', [
            'application' => $hajjUmrah,
            'serviceApplication' => $serviceApplication,
            'quotes' => $quotes,
            'canEdit' => $hajjUmrah->canBeEditedByUser(),
        ]);
    }
}
