<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;

class LanguageTestPrepController extends Controller
{
    use CreatesServiceApplications;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'test_type' => 'required|string',
            'target_score' => 'required|string',
            'current_level' => 'required|string',
            'preparation_duration' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $prep = (object)[
            'id' => uniqid(),
            'user_id' => $request->user()->id ?? 1,
            'test_type' => $validated['test_type'],
            'target_score' => $validated['target_score'],
            'total_amount' => $validated['total_amount'],
        ];

        $this->createServiceApplicationFor($prep, 'language-test-prep', $validated);

        return response()->json(['message' => 'Language test prep enrollment created', 'prep' => $prep]);
    }
}
