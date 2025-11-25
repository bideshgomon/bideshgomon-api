<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;

class LegalConsultationController extends Controller
{
    use CreatesServiceApplications;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'consultation_type' => 'required|string',
            'legal_matter' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $consultation = (object)['id' => uniqid(), 'user_id' => $request->user()->id ?? 1] + $validated;
        $this->createServiceApplicationFor($consultation, 'legal-consultation', $validated);

        return response()->json(['message' => 'Legal consultation request created']);
    }
}
