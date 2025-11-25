<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;

class CourseCounselingController extends Controller
{
    use CreatesServiceApplications;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'education_level' => 'required|string',
            'field_of_interest' => 'required|string',
            'preferred_countries' => 'required|array',
            'budget_range' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $counseling = (object)[
            'id' => uniqid(),
            'user_id' => $request->user()->id ?? 1,
            'field_of_interest' => $validated['field_of_interest'],
            'total_amount' => $validated['total_amount'],
        ];

        $this->createServiceApplicationFor($counseling, 'course-counseling', $validated);

        return response()->json(['message' => 'Course counseling request created', 'counseling' => $counseling]);
    }
}
