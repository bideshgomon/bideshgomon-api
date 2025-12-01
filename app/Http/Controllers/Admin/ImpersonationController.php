<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ImpersonationController extends Controller
{
    public function impersonate(Request $request, User $user)
    {
        // Prevent impersonating yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot impersonate yourself.');
        }

        // Prevent impersonating other admins
        if ($user->hasRole('admin')) {
            return back()->with('error', 'You cannot impersonate other administrators.');
        }

        // Validate purpose/reason
        $validated = $request->validate([
            'purpose' => 'required|string|min:10|max:500'
        ]);

        // Store original user ID
        Session::put('impersonate_original_user', auth()->id());
        
        // Log impersonation with purpose
        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties([
                'user_name' => $user->name,
                'user_email' => $user->email,
                'purpose' => $validated['purpose']
            ])
            ->log('Admin started impersonating user');

        // Impersonate the user
        Auth::loginUsingId($user->id);

        return redirect()->route('dashboard')->with('success', "You are now impersonating {$user->name}");
    }

    public function leave()
    {
        // Get original user ID
        $originalUserId = Session::get('impersonate_original_user');

        if (!$originalUserId) {
            return redirect()->route('dashboard')->with('error', 'You are not impersonating anyone.');
        }

        // Get current impersonated user before switching
        $impersonatedUser = auth()->user();

        // Log impersonation end
        activity()
            ->causedBy(User::find($originalUserId))
            ->performedOn($impersonatedUser)
            ->withProperties([
                'user_name' => $impersonatedUser->name,
                'user_email' => $impersonatedUser->email
            ])
            ->log('Admin stopped impersonating user');

        // Switch back to original user
        Auth::loginUsingId($originalUserId);

        // Remove impersonation session
        Session::forget('impersonate_original_user');

        return redirect()->route('admin.users.index')->with('success', 'You have stopped impersonating.');
    }

    public function check()
    {
        return response()->json([
            'impersonating' => Session::has('impersonate_original_user'),
            'original_user_id' => Session::get('impersonate_original_user')
        ]);
    }
}
