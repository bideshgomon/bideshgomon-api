<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;

class CulturalSupportController extends Controller
{
    use CreatesServiceApplications;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'country' => 'required|string',
            'support_type' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $support = (object)['id' => uniqid(), 'user_id' => $request->user()->id ?? 1] + $validated;
        $this->createServiceApplicationFor($support, 'cultural-support', $validated);

        return response()->json(['message' => 'Cultural integration support request created']);
    }
}
