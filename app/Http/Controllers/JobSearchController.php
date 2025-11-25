<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;

class JobSearchController extends Controller
{
    use CreatesServiceApplications;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_title' => 'required|string',
            'industry' => 'required|string',
            'preferred_countries' => 'required|array',
            'experience_years' => 'required|integer',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $search = (object)[
            'id' => uniqid(),
            'user_id' => $request->user()->id ?? 1,
            'job_title' => $validated['job_title'],
            'industry' => $validated['industry'],
            'total_amount' => $validated['total_amount'],
        ];

        $this->createServiceApplicationFor($search, 'job-search', $validated);

        return response()->json(['message' => 'Job search assistance request created', 'search' => $search]);
    }
}
