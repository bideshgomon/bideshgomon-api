<?php

namespace App\Http\Controllers;

use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WalletController extends Controller
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Display the wallet dashboard.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        
        // Create wallet if doesn't exist
        if (!$user->wallet) {
            $this->walletService->createWallet($user);
            $user->load('wallet');
        }

        $wallet = $user->wallet;
        $recentTransactions = $wallet->transactions()->take(10)->get();

        return Inertia::render('Wallet/Index', [
            'wallet' => $wallet,
            'balance' => $wallet->balance,
            'formattedBalance' => $wallet->formatted_balance,
            'recentTransactions' => $recentTransactions,
        ]);
    }

    /**
     * Display transaction history.
     */
    public function transactions(Request $request): Response|RedirectResponse
    {
        $user = $request->user();
        
        if (!$user->wallet) {
            return redirect()->route('wallet.index');
        }

        $type = $request->get('type');
        $search = $request->get('search');

        $query = $user->wallet->transactions();

        if ($type && in_array($type, ['credit', 'debit'])) {
            $query->where('transaction_type', $type);
        }

        if ($search) {
            $query->where('description', 'like', "%{$search}%");
        }

        $transactions = $query->paginate(20)->withQueryString();

        return Inertia::render('Wallet/Transactions', [
            'transactions' => $transactions,
            'filters' => [
                'type' => $type,
                'search' => $search,
            ],
        ]);
    }

    /**
     * Add funds to wallet.
     */
    public function addFunds(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:100|max:100000',
            'payment_method' => 'required|in:bKash,Nagad,Rocket,Bank,Card',
        ]);

        $user = $request->user();
        
        // Create wallet if doesn't exist
        if (!$user->wallet) {
            $this->walletService->createWallet($user);
            $user->load('wallet');
        }

        try {
            // Create payment reference
            $methodPrefix = match($validated['payment_method']) {
                'bKash' => 'BKS',
                'Nagad' => 'NGD',
                'Rocket' => 'RKT',
                'Bank' => 'BNK',
                'Card' => 'CRD',
            };
            
            $referenceId = $methodPrefix . rand(100000, 999999);

            // Add funds to wallet
            $this->walletService->creditWallet(
                wallet: $user->wallet,
                amount: $validated['amount'],
                description: "Add funds via {$validated['payment_method']}",
                referenceType: 'payment',
                referenceId: $referenceId,
            );

            return redirect()->back()->with('success', "৳ {$validated['amount']} added successfully!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add funds. Please try again.');
        }
    }

    /**
     * Withdraw funds from wallet.
     */
    public function withdraw(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:500|max:50000',
            'account_type' => 'required|in:bKash,Nagad,Rocket,Bank',
            'account_number' => 'required|string|min:10|max:20',
        ]);

        $user = $request->user();
        
        if (!$user->wallet) {
            return redirect()->back()->with('error', 'Wallet not found.');
        }

        // Check sufficient balance
        if ($user->wallet->balance < $validated['amount']) {
            return redirect()->back()->with('error', 'Insufficient balance.');
        }

        try {
            $referenceId = 'WTH' . rand(100000, 999999);

            // Withdraw from wallet
            $this->walletService->debitWallet(
                wallet: $user->wallet,
                amount: $validated['amount'],
                description: "Withdrawal to {$validated['account_type']} ({$validated['account_number']})",
                referenceType: 'withdrawal',
                referenceId: $referenceId,
            );

            return redirect()->back()->with('success', "৳ {$validated['amount']} withdrawal successful!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to process withdrawal. Please try again.');
        }
    }
}
