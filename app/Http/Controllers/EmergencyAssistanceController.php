<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;

class EmergencyAssistanceController extends Controller
{
    use CreatesServiceApplications;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'emergency_type' => 'required|string',
            'location' => 'required|string',
            'urgency' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $emergency = (object)['id' => uniqid(), 'user_id' => $request->user()->id ?? 1] + $validated;
        $this->createServiceApplicationFor($emergency, 'emergency-assistance', $validated);

        return response()->json(['message' => 'Emergency assistance request created']);
    }
}
