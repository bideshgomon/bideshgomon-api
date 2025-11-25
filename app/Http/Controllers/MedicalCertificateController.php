<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;

class MedicalCertificateController extends Controller
{
    use CreatesServiceApplications;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'certificate_type' => 'required|string',
            'purpose' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $certificate = (object)['id' => uniqid(), 'user_id' => $request->user()->id ?? 1] + $validated;
        $this->createServiceApplicationFor($certificate, 'medical-certificate', $validated);

        return response()->json(['message' => 'Medical certificate request created']);
    }
}
