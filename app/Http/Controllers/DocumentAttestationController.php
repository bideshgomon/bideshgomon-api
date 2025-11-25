<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;

class DocumentAttestationController extends Controller
{
    use CreatesServiceApplications;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_type' => 'required|string',
            'destination_country' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $attestation = (object)['id' => uniqid(), 'user_id' => $request->user()->id ?? 1] + $validated;
        $this->createServiceApplicationFor($attestation, 'attestation', $validated);

        return response()->json(['message' => 'Document attestation request created']);
    }
}
