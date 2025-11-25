<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TourPackageController extends Controller
{
    use CreatesServiceApplications;

    /**
     * Store a new tour package booking
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'package_name' => 'required|string',
            'destination' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'travelers_count' => 'required|integer|min:1',
            'accommodation_type' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        // Create package booking (simplified for demo)
        $booking = (object)[
            'id' => uniqid(),
            'user_id' => $request->user()->id ?? 1,
            'package_name' => $validated['package_name'],
            'destination' => $validated['destination'],
            'total_amount' => $validated['total_amount'],
        ];

        // 🔥 PLUGIN SYSTEM: Create ServiceApplication
        $this->createServiceApplicationFor(
            $booking,
            'tour-packages',
            [
                'package_name' => $validated['package_name'],
                'destination' => $validated['destination'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'travelers_count' => $validated['travelers_count'],
                'accommodation_type' => $validated['accommodation_type'],
                'total_amount' => $validated['total_amount'],
            ]
        );

        return response()->json([
            'message' => 'Tour package booking created successfully',
            'booking' => $booking,
        ]);
    }
}
