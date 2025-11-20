<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
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
     * Display all wallets.
     */
    public function index(Request $request): Response
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = Wallet::with('user:id,name,email');

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status && in_array($status, ['active', 'suspended', 'closed'])) {
            $query->where('status', $status);
        }

        $wallets = $query->latest()->paginate(20)->withQueryString();
        $stats = $this->walletService->getWalletStats();

        return Inertia::render('Admin/Wallets/Index', [
            'wallets' => $wallets,
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
        ]);
    }

    /**
     * Display a specific wallet.
     */
    public function show(Wallet $wallet): Response
    {
        $wallet->load('user:id,name,email,role_id');
        $transactions = $wallet->transactions()
            ->with('processedBy:id,name')
            ->paginate(20);

        return Inertia::render('Admin/Wallets/Show', [
            'wallet' => $wallet,
            'transactions' => $transactions,
        ]);
    }

    /**
     * Credit a wallet.
     */
    public function credit(Request $request, Wallet $wallet): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:999999.99'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        try {
            $this->walletService->creditWallet(
                $wallet,
                $validated['amount'],
                $validated['description'],
                'manual_adjustment',
                null,
                ['admin_note' => $request->get('note')],
                auth()->id()
            );

            return redirect()->back()->with('success', 'Wallet credited successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Debit a wallet.
     */
    public function debit(Request $request, Wallet $wallet): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:999999.99'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        try {
            $this->walletService->debitWallet(
                $wallet,
                $validated['amount'],
                $validated['description'],
                'manual_adjustment',
                null,
                ['admin_note' => $request->get('note')],
                auth()->id()
            );

            return redirect()->back()->with('success', 'Wallet debited successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reverse a transaction.
     */
    public function reverseTransaction(WalletTransaction $transaction): RedirectResponse
    {
        try {
            $this->walletService->reverseTransaction($transaction);
            return redirect()->back()->with('success', 'Transaction reversed successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Toggle wallet status.
     */
    public function toggleStatus(Wallet $wallet): RedirectResponse
    {
        if ($wallet->isActive()) {
            $this->walletService->suspendWallet($wallet);
            $message = 'Wallet suspended successfully!';
        } else {
            $this->walletService->activateWallet($wallet);
            $message = 'Wallet activated successfully!';
        }

        return redirect()->back()->with('success', $message);
    }
}
