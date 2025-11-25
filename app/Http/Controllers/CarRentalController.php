<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CarRentalController extends Controller
{
    use CreatesServiceApplications;

    /**
     * Store a new car rental booking
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pickup_location' => 'required|string',
            'dropoff_location' => 'required|string',
            'pickup_date' => 'required|date',
            'return_date' => 'required|date|after:pickup_date',
            'vehicle_category' => 'required|string',
            'driver_required' => 'boolean',
            'total_amount' => 'required|numeric|min:0',
        ]);

        // Create rental record (simplified for demo)
        $rental = (object)[
            'id' => uniqid(),
            'user_id' => $request->user()->id ?? 1,
            'pickup_location' => $validated['pickup_location'],
            'vehicle_category' => $validated['vehicle_category'],
            'total_amount' => $validated['total_amount'],
        ];

        // 🔥 PLUGIN SYSTEM: Create ServiceApplication
        $this->createServiceApplicationFor(
            $rental,
            'car-rental',
            [
                'pickup_location' => $validated['pickup_location'],
                'dropoff_location' => $validated['dropoff_location'],
                'pickup_date' => $validated['pickup_date'],
                'return_date' => $validated['return_date'],
                'vehicle_category' => $validated['vehicle_category'],
                'driver_required' => $validated['driver_required'] ?? false,
                'total_amount' => $validated['total_amount'],
            ]
        );

        return response()->json([
            'message' => 'Car rental booking created successfully',
            'rental' => $rental,
        ]);
    }
}
