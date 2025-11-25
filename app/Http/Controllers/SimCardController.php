<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;

class SimCardController extends Controller
{
    use CreatesServiceApplications;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'country' => 'required|string',
            'plan_type' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $sim = (object)['id' => uniqid(), 'user_id' => $request->user()->id ?? 1] + $validated;
        $this->createServiceApplicationFor($sim, 'sim-card', $validated);

        return response()->json(['message' => 'SIM card request created']);
    }
}
