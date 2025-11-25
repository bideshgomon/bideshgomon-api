<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;

class PassportServiceController extends Controller
{
    use CreatesServiceApplications;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_type' => 'required|string',
            'passport_type' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $passport = (object)['id' => uniqid(), 'user_id' => $request->user()->id ?? 1] + $validated;
        $this->createServiceApplicationFor($passport, 'passport-services', $validated);

        return response()->json(['message' => 'Passport service request created']);
    }
}
