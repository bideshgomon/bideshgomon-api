<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;

class PoliceClearanceController extends Controller
{
    use CreatesServiceApplications;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'country' => 'required|string',
            'clearance_type' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $clearance = (object)['id' => uniqid(), 'user_id' => $request->user()->id ?? 1] + $validated;
        $this->createServiceApplicationFor($clearance, 'police-clearance', $validated);

        return response()->json(['message' => 'Police clearance request created']);
    }
}
