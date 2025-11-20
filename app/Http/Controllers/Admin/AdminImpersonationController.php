<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminImpersonationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Events\ImpersonationStarted;
use App\Events\ImpersonationEnded;
use Inertia\Inertia;

class AdminImpersonationController extends Controller
{
    /**
     * Impersonate a target user (admin only).
     */
    public function impersonate(Request $request, int $id)
    {
    $admin = Auth::user();
    if ($admin) { $admin->loadMissing('role'); }
        if (! $admin || ! $admin->role || strtolower($admin->role->slug) !== 'admin') {
            abort(403, 'Only admins can impersonate users.');
        }

        // Block nested impersonation explicitly (middleware also handles)
        if (Session::has('impersonator_id')) {
            return redirect()->back()->with('error', 'Exit current impersonation first.');
        }

    $target = User::with('role')->findOrFail($id);

        // Gate policy check
        if (! \Gate::allows('impersonate', $target)) {
            abort(403, 'Not authorized to impersonate this user.');
        }

        // Store original admin id if not already impersonating
        if (! Session::has('impersonator_id')) {
            Session::put('impersonator_id', $admin->id);
        }

        // Validate purpose (industry standard: require traceable reason)
        $validated = $request->validate([
            'purpose' => ['required','string','max:150'],
        ]);

        // Create log entry
        $log = AdminImpersonationLog::create([
            'impersonator_id' => Session::get('impersonator_id'),
            'target_user_id' => $target->id,
            'started_at' => now(),
            'purpose' => $validated['purpose'],
        ]);

        // Store log id for ending later
        Session::put('impersonation_log_id', $log->id);

    Auth::login($target); // Switch auth user

    // Signed cookie for integrity (httpOnly; value is original admin id)
    cookie()->queue(cookie('impersonator_id', $admin->id, 60, null, null, false, true, false, 'Lax'));

        // Dispatch domain event
        event(new ImpersonationStarted($log));

        Log::info('Admin impersonation started', [
            'impersonator_id' => Session::get('impersonator_id'),
            'acting_as_id' => $target->id,
        ]);

        return redirect()->back()->with('success', 'Now impersonating ' . ($target->full_name ?? $target->name ?? 'user')); // Adjust name field as needed
    }

    /**
     * Leave impersonation and restore original admin identity.
     */
    public function leave(Request $request)
    {
    $impersonatorId = Session::get('impersonator_id');
    $logId = Session::get('impersonation_log_id');
        if (! $impersonatorId) {
            return redirect()->back()->with('info', 'Not currently impersonating.');
        }

        $cookieImpersonator = request()->cookie('impersonator_id');
        if ($cookieImpersonator && (int)$cookieImpersonator !== (int)$impersonatorId) {
            Log::warning('Impersonation cookie mismatch', [
                'session_impersonator_id' => $impersonatorId,
                'cookie_impersonator_id' => $cookieImpersonator,
            ]);
        }

        $admin = User::find($impersonatorId);
        if ($admin) {
            Auth::login($admin);
        }

        // Update log entry
        if ($logId) {
            $endedAt = now();
            AdminImpersonationLog::where('id', $logId)->update([
                'ended_at' => $endedAt,
            ]);
            if ($log = AdminImpersonationLog::find($logId)) {
                event(new ImpersonationEnded($log));
            }
        }

        Log::info('Admin impersonation ended', [
            'impersonator_id' => $impersonatorId,
            'restored_admin_id' => $admin?->id,
            'log_id' => $logId,
        ]);

    Session::forget('impersonator_id');
    Session::forget('impersonation_log_id');
    cookie()->queue(cookie('impersonator_id', '', -60));

        return redirect()->back()->with('success', 'Returned to admin account.');
    }
}
