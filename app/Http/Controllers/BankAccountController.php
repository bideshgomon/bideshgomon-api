<?php

namespace App\Http\Controllers;

use App\Traits\CreatesServiceApplications;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    use CreatesServiceApplications;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string',
            'account_type' => 'required|string',
            'country' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $account = (object)['id' => uniqid(), 'user_id' => $request->user()->id ?? 1] + $validated;
        $this->createServiceApplicationFor($account, 'bank-account', $validated);

        return response()->json(['message' => 'Bank account opening request created']);
    }
}
