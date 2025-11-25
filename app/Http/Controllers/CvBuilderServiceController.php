<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;

class CvBuilderServiceController extends Controller
{
    use CreatesServiceApplications;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cv_type' => 'required|string',
            'industry' => 'required|string',
            'experience_level' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $cv = (object)['id' => uniqid(), 'user_id' => $request->user()->id ?? 1] + $validated;
        $this->createServiceApplicationFor($cv, 'cv-builder', $validated);

        return response()->json(['message' => 'CV builder request created', 'cv' => $cv]);
    }
}
