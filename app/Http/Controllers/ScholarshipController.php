<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;

class ScholarshipController extends Controller
{
    use CreatesServiceApplications;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'scholarship_type' => 'required|string',
            'country' => 'required|string',
            'degree_level' => 'required|string',
            'academic_score' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $application = (object)[
            'id' => uniqid(),
            'user_id' => $request->user()->id ?? 1,
            'scholarship_type' => $validated['scholarship_type'],
            'country' => $validated['country'],
            'total_amount' => $validated['total_amount'],
        ];

        $this->createServiceApplicationFor($application, 'scholarship-assistance', $validated);

        return response()->json(['message' => 'Scholarship assistance request created', 'application' => $application]);
    }
}
