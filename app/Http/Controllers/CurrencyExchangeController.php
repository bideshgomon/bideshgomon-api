<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;

class CurrencyExchangeController extends Controller
{
    use CreatesServiceApplications;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_currency' => 'required|string',
            'to_currency' => 'required|string',
            'amount' => 'required|numeric',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $exchange = (object)['id' => uniqid(), 'user_id' => $request->user()->id ?? 1] + $validated;
        $this->createServiceApplicationFor($exchange, 'currency-exchange', $validated);

        return response()->json(['message' => 'Currency exchange request created']);
    }
}
