<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\AgencyVerificationDocument;
use App\Models\AgencyVerificationRequest;
use App\Services\EmailNotificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AgencyVerificationController extends Controller
{
    /**
     * Display a listing of verification requests.
     */
    public function index(Request $request)
    {
        $query = AgencyVerificationRequest::with(['agency', 'reviewer'])
            ->latest('submitted_at');

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search by agency name
        if ($request->has('search') && $request->search !== '') {
            $query->whereHas('agency', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('company_name', 'like', '%' . $request->search . '%');
            });
        }

        $requests = $query->paginate(20);

        return Inertia::render('Admin/AgencyVerification/Index', [
            'requests' => $requests,
            'filters' => [
                'status' => $request->status,
                'search' => $request->search,
            ],
        ]);
    }

    /**
     * Display the specified verification request.
     */
    public function show(AgencyVerificationRequest $verificationRequest)
    {
        $verificationRequest->load(['agency', 'reviewer']);

        $documents = AgencyVerificationDocument::whereIn('id', $verificationRequest->submitted_documents ?? [])
            ->get();

        return Inertia::render('Admin/AgencyVerification/Show', [
            'request' => $verificationRequest,
            'documents' => $documents,
            'agency' => $verificationRequest->agency->load('user'),
        ]);
    }

    /**
     * Update the status of a verification request.
     */
    public function updateStatus(Request $request, AgencyVerificationRequest $verificationRequest)
    {
        $request->validate([
            'status' => 'required|in:under_review,approved,rejected,requires_changes',
            'admin_notes' => 'nullable|string|max:2000',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:1000',
        ]);

        $verificationRequest->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'rejection_reason' => $request->rejection_reason,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'approved_at' => $request->status === 'approved' ? now() : null,
        ]);

        // If approved, update agency verification status and approve all documents
        if ($request->status === 'approved') {
            $verificationRequest->agency->update([
                'is_verified' => true,
                'verified_at' => now(),
            ]);

            // Approve all submitted documents
            AgencyVerificationDocument::whereIn('id', $verificationRequest->submitted_documents ?? [])
                ->update([
                    'status' => 'approved',
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                ]);

            // Send notification to agency
            \App\Models\UserNotification::create([
                'user_id' => $verificationRequest->agency->user_id,
                'type' => 'verification_approved',
                'title' => 'Verification Approved! 🎉',
                'body' => 'Congratulations! Your agency verification has been approved. You now have a verified badge.',
                'action_url' => route('agency.verification.index'),
                'icon' => '✅',
                'color' => 'green',
                'priority' => 'high',
            ]);

            // Send email notification
            app(EmailNotificationService::class)->sendFromTemplate(
                'verification_approved',
                $verificationRequest->agency->user->email,
                [
                    'user_name' => $verificationRequest->agency->user->name,
                    'agency_name' => $verificationRequest->agency->name,
                ],
                $verificationRequest->agency->user_id
            );
        }

        // If rejected, send notification
        if ($request->status === 'rejected') {
            \App\Models\UserNotification::create([
                'user_id' => $verificationRequest->agency->user_id,
                'type' => 'verification_rejected',
                'title' => 'Verification Rejected',
                'body' => 'Your verification request has been rejected. ' . ($request->rejection_reason ? 'Reason: ' . $request->rejection_reason : 'Please review the admin notes.'),
                'action_url' => route('agency.verification.index'),
                'icon' => '❌',
                'color' => 'red',
                'priority' => 'high',
            ]);

            // Send email notification
            app(EmailNotificationService::class)->sendFromTemplate(
                'verification_rejected',
                $verificationRequest->agency->user->email,
                [
                    'user_name' => $verificationRequest->agency->user->name,
                    'agency_name' => $verificationRequest->agency->name,
                    'reason' => $request->rejection_reason ?? 'Please review the admin notes for details.',
                ],
                $verificationRequest->agency->user_id
            );
        }

        // If requires changes, send notification
        if ($request->status === 'requires_changes') {
            \App\Models\UserNotification::create([
                'user_id' => $verificationRequest->agency->user_id,
                'type' => 'verification_requires_changes',
                'title' => 'Verification Requires Changes',
                'body' => 'Your verification request requires some changes. Please review the admin notes and resubmit.',
                'action_url' => route('agency.verification.index'),
                'icon' => '📝',
                'color' => 'orange',
                'priority' => 'normal',
            ]);
        }

        return back()->with('success', 'Verification request updated successfully.');
    }

    /**
     * Update the status of a specific document.
     */
    public function updateDocumentStatus(Request $request, AgencyVerificationDocument $document)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $document->update([
            'status' => $request->status,
            'rejection_reason' => $request->rejection_reason,
            'notes' => $request->notes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Document status updated successfully.');
    }

    /**
     * Display statistics for verification dashboard.
     */
    public function dashboard()
    {
        $stats = [
            'pending' => AgencyVerificationRequest::pending()->count(),
            'under_review' => AgencyVerificationRequest::underReview()->count(),
            'approved_this_month' => AgencyVerificationRequest::approved()
                ->whereMonth('approved_at', now()->month)
                ->count(),
            'rejected_this_month' => AgencyVerificationRequest::rejected()
                ->whereMonth('reviewed_at', now()->month)
                ->count(),
            'verified_agencies' => Agency::where('is_verified', true)->count(),
            'unverified_agencies' => Agency::where('is_verified', false)->count(),
        ];

        $recentRequests = AgencyVerificationRequest::with(['agency', 'reviewer'])
            ->latest('submitted_at')
            ->take(10)
            ->get();

        return Inertia::render('Admin/AgencyVerification/Dashboard', [
            'stats' => $stats,
            'recentRequests' => $recentRequests,
        ]);
    }
}
