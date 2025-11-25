<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AirportTransferController extends Controller
{
    use CreatesServiceApplications;

    /**
     * Store a new airport transfer booking
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pickup_location' => 'required|string',
            'dropoff_location' => 'required|string',
            'pickup_date' => 'required|date',
            'pickup_time' => 'required|string',
            'passengers_count' => 'required|integer|min:1',
            'vehicle_type' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        // Create booking record (simplified for demo)
        $booking = (object)[
            'id' => uniqid(),
            'user_id' => $request->user()->id ?? 1,
            'pickup_location' => $validated['pickup_location'],
            'dropoff_location' => $validated['dropoff_location'],
            'total_amount' => $validated['total_amount'],
        ];

        // 🔥 PLUGIN SYSTEM: Create ServiceApplication
        $this->createServiceApplicationFor(
            $booking,
            'airport-transfer',
            [
                'pickup_location' => $validated['pickup_location'],
                'dropoff_location' => $validated['dropoff_location'],
                'pickup_date' => $validated['pickup_date'],
                'pickup_time' => $validated['pickup_time'],
                'passengers_count' => $validated['passengers_count'],
                'vehicle_type' => $validated['vehicle_type'],
                'total_amount' => $validated['total_amount'],
            ]
        );

        return response()->json([
            'message' => 'Airport transfer booking created successfully',
            'booking' => $booking,
        ]);
    }
}
