<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;

class InterviewPrepController extends Controller
{
    use CreatesServiceApplications;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_role' => 'required|string',
            'company_type' => 'required|string',
            'interview_type' => 'required|string',
            'preparation_level' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $prep = (object)[
            'id' => uniqid(),
            'user_id' => $request->user()->id ?? 1,
            'job_role' => $validated['job_role'],
            'interview_type' => $validated['interview_type'],
            'total_amount' => $validated['total_amount'],
        ];

        $this->createServiceApplicationFor($prep, 'interview-prep', $validated);

        return response()->json(['message' => 'Interview preparation enrollment created', 'prep' => $prep]);
    }
}
