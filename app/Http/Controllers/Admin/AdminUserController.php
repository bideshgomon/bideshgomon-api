<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
    // Eager load role (new role_id-based system), profile, country
    $query = User::with(['profile', 'country', 'role']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            // Support filtering by role slug or name (legacy compatibility)
            $roleFilter = $request->role;
            $query->whereHas('role', function ($q) use ($roleFilter) {
                $q->where('slug', $roleFilter)->orWhere('name', $roleFilter);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('suspended_at');
            } elseif ($request->status === 'suspended') {
                $query->whereNotNull('suspended_at');
            }
        }

        // Filter by email verification
        if ($request->filled('email_verified')) {
            if ($request->email_verified === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->email_verified === 'unverified') {
                $query->whereNull('email_verified_at');
            }
        }

        // Filter by country
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total' => User::count(),
            'active' => User::whereNull('suspended_at')->count(),
            'suspended' => User::whereNotNull('suspended_at')->count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'unverified' => User::whereNull('email_verified_at')->count(),
            'admins' => User::whereHas('role', function ($q) { $q->where('slug', 'admin')->orWhere('name', 'admin'); })->count(),
        ];

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'stats' => $stats,
            'filters' => $request->only(['search', 'role', 'status', 'email_verified', 'country_id']),
        ]);
    }

    public function show($id)
    {
        $user = User::with([
            'profile',
            'country',
            'cvs',
            'jobApplications.jobPosting',
            'wallet',
        ])->findOrFail($id);

        return Inertia::render('Admin/Users/Show', [
            'user' => $user,
        ]);
    }

    public function suspend(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $user = User::findOrFail($id);

        // Prevent suspending admin via role relationship
        if ($user->role && in_array(strtolower($user->role->slug), ['admin'])) {
            return back()->with('error', 'Cannot suspend admin users.');
        }

        $user->update([
            'suspended_at' => now(),
            'suspension_reason' => $request->reason,
        ]);

        return back()->with('success', 'User suspended successfully.');
    }

    public function unsuspend($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);

        return back()->with('success', 'User unsuspended successfully.');
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:user,admin',
        ]);

        $user = User::findOrFail($id);

        // Prevent self-demotion
        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return back()->with('error', 'You cannot change your own role.');
        }

        $user->update([
            'role' => $request->role,
        ]);

        return back()->with('success', 'User role updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Prevent deleting admins
        if ($user->role && in_array(strtolower($user->role->slug), ['admin'])) {
            return back()->with('error', 'Cannot delete admin users.');
        }

        // Check if user has active job applications
        if ($user->jobApplications()->whereIn('status', ['pending', 'reviewed', 'shortlisted'])->exists()) {
            return back()->with('error', 'Cannot delete user with active job applications.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function bulkSuspend(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'reason' => 'required|string|max:500',
        ]);

        $users = User::whereIn('id', $request->user_ids)
            ->where('role', '!=', 'admin')
            ->get();

        foreach ($users as $user) {
            $user->update([
                'suspended_at' => now(),
                'suspension_reason' => $request->reason,
            ]);
        }

        return back()->with('success', count($users) . ' users suspended successfully.');
    }

    public function bulkUnsuspend(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        User::whereIn('id', $request->user_ids)->update([
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);

        return back()->with('success', count($request->user_ids) . ' users unsuspended successfully.');
    }

    public function export(Request $request)
    {
    $query = User::with(['profile', 'country', 'role']);

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $roleFilter = $request->role;
            $query->whereHas('role', function ($q) use ($roleFilter) {
                $q->where('slug', $roleFilter)->orWhere('name', $roleFilter);
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('suspended_at');
            } elseif ($request->status === 'suspended') {
                $query->whereNotNull('suspended_at');
            }
        }

        $users = $query->get();

        $csv = "Name,Email,Phone,Role,Status,Country,Registered,Email Verified\n";

        foreach ($users as $user) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s\n",
                $user->name,
                $user->email,
                $user->phone ?? 'N/A',
                $user->role,
                $user->suspended_at ? 'Suspended' : 'Active',
                $user->country->name ?? 'N/A',
                $user->created_at->format('Y-m-d'),
                $user->email_verified_at ? 'Yes' : 'No'
            );
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
