<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;

class SkillVerificationController extends Controller
{
    use CreatesServiceApplications;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'skill_type' => 'required|string',
            'certification_level' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $verification = (object)['id' => uniqid(), 'user_id' => $request->user()->id ?? 1] + $validated;
        $this->createServiceApplicationFor($verification, 'skill-verification', $validated);

        return response()->json(['message' => 'Skill verification request created']);
    }
}
