<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;

class UniversityApplicationController extends Controller
{
    use CreatesServiceApplications;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'university_name' => 'required|string',
            'country' => 'required|string',
            'program_name' => 'required|string',
            'degree_level' => 'required|string',
            'intake_year' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $application = (object)[
            'id' => uniqid(),
            'user_id' => $request->user()->id ?? 1,
            'university_name' => $validated['university_name'],
            'program_name' => $validated['program_name'],
            'total_amount' => $validated['total_amount'],
        ];

        $this->createServiceApplicationFor($application, 'university-application', $validated);

        return response()->json(['message' => 'University application created', 'application' => $application]);
    }
}
