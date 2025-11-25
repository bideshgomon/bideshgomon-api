<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;

class RelocationServiceController extends Controller
{
    use CreatesServiceApplications;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'destination_country' => 'required|string',
            'relocation_type' => 'required|string',
            'family_size' => 'required|integer',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $relocation = (object)['id' => uniqid(), 'user_id' => $request->user()->id ?? 1] + $validated;
        $this->createServiceApplicationFor($relocation, 'relocation', $validated);

        return response()->json(['message' => 'Relocation service request created']);
    }
}
