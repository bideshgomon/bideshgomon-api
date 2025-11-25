<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;

class HajjUmrahController extends Controller
{
    use CreatesServiceApplications;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'package_type' => 'required|string',
            'travel_date' => 'required|date',
            'travelers_count' => 'required|integer',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $package = (object)['id' => uniqid(), 'user_id' => $request->user()->id ?? 1] + $validated;
        $this->createServiceApplicationFor($package, 'hajj-umrah', $validated);

        return response()->json(['message' => 'Hajj/Umrah package booking created']);
    }
}
