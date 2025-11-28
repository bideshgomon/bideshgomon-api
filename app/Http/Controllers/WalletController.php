<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use App\Services\EmailNotificationService;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WalletController extends Controller
{
    protected WalletService $walletService;
    protected EmailNotificationService $emailService;

    public function __construct(WalletService $walletService, EmailNotificationService $emailService)
    {
        $this->walletService = $walletService;
        $this->emailService = $emailService;
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
        $recentTransactions = $wallet->transactions()->latest()->take(10)->get();
        
        // Calculate statistics
        $totalIn = $wallet->transactions()
            ->where('transaction_type', 'credit')
            ->where('status', 'completed')
            ->sum('amount');
            
        $totalOut = $wallet->transactions()
            ->where('transaction_type', 'debit')
            ->where('status', 'completed')
            ->sum('amount');
            
        $transactionCount = $wallet->transactions()->count();

        return Inertia::render('Wallet/Index', [
            'wallet' => $wallet,
            'balance' => (float) $wallet->balance,
            'formattedBalance' => $wallet->formatted_balance,
            'recentTransactions' => $recentTransactions,
            'totalIn' => (float) $totalIn,
            'totalOut' => (float) $totalOut,
            'transactionCount' => $transactionCount,
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
            'gateway' => 'required|in:sslcommerz,bkash,nagad',
        ]);

        $user = $request->user();
        
        // Create wallet if doesn't exist
        if (!$user->wallet) {
            $this->walletService->createWallet($user);
            $user->load('wallet');
        }

        try {
            // Redirect to payment gateway initiation
            $gateway = $validated['gateway'];
            $route = "payment.{$gateway}.initiate";
            
            return redirect()->route($route, [
                'amount' => $validated['amount'],
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone_number ?? $user->email,
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to initiate payment. Please try again.');
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

            // Create notification
            UserNotification::create([
                'user_id' => $user->id,
                'type' => 'withdrawal_completed',
                'title' => 'Withdrawal Completed 💸',
                'body' => "৳ {$validated['amount']} has been withdrawn to your {$validated['account_type']} account.",
                'icon' => '💸',
                'color' => 'blue',
                'priority' => 'high',
                'action_url' => route('wallet.transactions'),
            ]);

            // Send email notification
            $this->emailService->sendFromTemplate(
                'withdrawal_completed',
                $user->email,
                [
                    'user_name' => $user->name,
                    'amount' => number_format($validated['amount'], 2),
                    'account_type' => $validated['account_type'],
                    'balance' => number_format($user->wallet->fresh()->balance, 2),
                ],
                $user->id
            );

            return redirect()->back()->with('success', "৳ {$validated['amount']} withdrawal successful!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to process withdrawal. Please try again.');
        }
    }
}
