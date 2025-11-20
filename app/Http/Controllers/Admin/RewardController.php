<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Services\ReferralService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RewardController extends Controller
{
    protected ReferralService $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * Display all rewards.
     */
    public function index(Request $request): Response
    {
        $status = $request->get('status');
        $search = $request->get('search');

        $query = Reward::with(['user:id,name,email', 'referral.referred:id,name']);

        if ($status && in_array($status, ['pending', 'approved', 'rejected', 'paid'])) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $rewards = $query->latest()->paginate(20)->withQueryString();
        $stats = $this->referralService->getGlobalStats();

        return Inertia::render('Admin/Rewards/Index', [
            'rewards' => $rewards,
            'stats' => $stats,
            'filters' => [
                'status' => $status,
                'search' => $search,
            ],
        ]);
    }

    /**
     * Approve a reward.
     */
    public function approve(Reward $reward): RedirectResponse
    {
        if (!$reward->isPending()) {
            return redirect()->back()->with('error', 'Only pending rewards can be approved.');
        }

        try {
            $this->referralService->approveReward($reward, auth()->id());
            return redirect()->back()->with('success', 'Reward approved and credited to wallet!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject a reward.
     */
    public function reject(Request $request, Reward $reward): RedirectResponse
    {
        if (!$reward->isPending()) {
            return redirect()->back()->with('error', 'Only pending rewards can be rejected.');
        }

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $reward->reject(auth()->id(), $validated['reason'] ?? null);

        return redirect()->back()->with('success', 'Reward rejected.');
    }
}
