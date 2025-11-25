<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;

class BirthCertificateController extends Controller
{
    use CreatesServiceApplications;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'certificate_type' => 'required|string',
            'country' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $certificate = (object)['id' => uniqid(), 'user_id' => $request->user()->id ?? 1] + $validated;
        $this->createServiceApplicationFor($certificate, 'birth-certificate', $validated);

        return response()->json(['message' => 'Birth certificate request created']);
    }
}
