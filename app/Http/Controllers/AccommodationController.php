<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;

class AccommodationController extends Controller
{
    use CreatesServiceApplications;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'city' => 'required|string',
            'accommodation_type' => 'required|string',
            'budget_range' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $accommodation = (object)['id' => uniqid(), 'user_id' => $request->user()->id ?? 1] + $validated;
        $this->createServiceApplicationFor($accommodation, 'accommodation', $validated);

        return response()->json(['message' => 'Accommodation finding request created']);
    }
}
