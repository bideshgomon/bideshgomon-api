<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;

class TaxFilingController extends Controller
{
    use CreatesServiceApplications;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'country' => 'required|string',
            'tax_year' => 'required|string',
            'filing_type' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $filing = (object)['id' => uniqid(), 'user_id' => $request->user()->id ?? 1] + $validated;
        $this->createServiceApplicationFor($filing, 'tax-filing', $validated);

        return response()->json(['message' => 'Tax filing assistance request created']);
    }
}
