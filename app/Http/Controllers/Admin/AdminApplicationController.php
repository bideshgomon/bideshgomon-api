<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceApplication;
use App\Models\ServiceModule;
use App\Services\ServiceApplicationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminApplicationController extends Controller
{
    protected $applicationService;

    public function __construct(ServiceApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    /**
     * Display a listing of applications
     */
    public function index(Request $request): Response
    {
        $query = ServiceApplication::with(['user', 'serviceModule'])
            ->when($request->search, function ($q, $search) {
                $q->where('application_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            })
            ->when($request->service, function ($q, $service) {
                $q->where('service_module_id', $service);
            })
            ->when($request->status, function ($q, $status) {
                $q->where('status', $status);
            })
            ->when($request->date_from, function ($q, $date) {
                $q->whereDate('submitted_at', '>=', $date);
            })
            ->when($request->date_to, function ($q, $date) {
                $q->whereDate('submitted_at', '<=', $date);
            })
            ->latest('submitted_at');

        $applications = $query->paginate(20)->withQueryString();

        $services = ServiceModule::active()->ordered()->get();

        $stats = [
            'total' => ServiceApplication::count(),
            'pending' => ServiceApplication::where('status', 'pending')->count(),
            'under_review' => ServiceApplication::where('status', 'under_review')->count(),
            'approved' => ServiceApplication::where('status', 'approved')->count(),
            'rejected' => ServiceApplication::where('status', 'rejected')->count(),
        ];

        return Inertia::render('Admin/Applications/Index', [
            'applications' => $applications,
            'services' => $services,
            'stats' => $stats,
            'filters' => $request->only(['search', 'service', 'status', 'date_from', 'date_to']),
        ]);
    }

    /**
     * Display the specified application
     */
    public function show(ServiceApplication $application): Response
    {
        $application->load([
            'user.profile',
            'user.passports',
            'user.phoneNumbers',
            'serviceModule.formFields',
            'documents',
            'statusHistory.changer',
        ]);

        return Inertia::render('Admin/Applications/Show', [
            'application' => $application,
        ]);
    }

    /**
     * Change application status
     */
    public function changeStatus(Request $request, ServiceApplication $application)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,under_review,additional_info,approved,rejected,cancelled,completed',
            'notes' => 'nullable|string',
        ]);

        try {
            $this->applicationService->changeStatus(
                $application,
                $validated['status'],
                $validated['notes'] ?? null,
                auth()->user()
            );

            return back()->with('success', 'Application status updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Approve application
     */
    public function approve(Request $request, ServiceApplication $application)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        try {
            $this->applicationService->changeStatus(
                $application,
                'approved',
                $validated['notes'] ?? 'Application approved',
                auth()->user()
            );

            return back()->with('success', 'Application approved successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject application
     */
    public function reject(Request $request, ServiceApplication $application)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        try {
            // Update rejection reason
            $application->update([
                'rejection_reason' => $validated['rejection_reason'],
            ]);

            $this->applicationService->changeStatus(
                $application,
                'rejected',
                $validated['notes'] ?? $validated['rejection_reason'],
                auth()->user()
            );

            return back()->with('success', 'Application rejected.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Request additional information
     */
    public function requestInfo(Request $request, ServiceApplication $application)
    {
        $validated = $request->validate([
            'requested_documents' => 'required|array',
            'notes' => 'required|string',
        ]);

        try {
            $application->update([
                'requested_documents' => $validated['requested_documents'],
            ]);

            $this->applicationService->changeStatus(
                $application,
                'additional_info',
                $validated['notes'],
                auth()->user()
            );

            // TODO: Send notification to user

            return back()->with('success', 'Additional information requested.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Add admin notes
     */
    public function addNotes(Request $request, ServiceApplication $application)
    {
        $validated = $request->validate([
            'notes' => 'required|string',
        ]);

        $application->update([
            'admin_notes' => $validated['notes'],
        ]);

        return back()->with('success', 'Notes added successfully.');
    }

    /**
     * Verify uploaded document
     */
    public function verifyDocument(Request $request, ServiceApplication $application, $documentId)
    {
        $document = $application->documents()->findOrFail($documentId);

        $document->update([
            'is_verified' => true,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Document verified successfully.');
    }

    /**
     * Bulk actions on applications
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject,mark_under_review,export',
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:service_applications,id',
            'notes' => 'nullable|string',
        ]);

        $applications = ServiceApplication::whereIn('id', $validated['application_ids'])->get();

        switch ($validated['action']) {
            case 'approve':
                foreach ($applications as $application) {
                    $this->applicationService->changeStatus(
                        $application,
                        'approved',
                        $validated['notes'] ?? 'Bulk approved',
                        auth()->user()
                    );
                }
                $message = 'Applications approved successfully.';
                break;

            case 'reject':
                foreach ($applications as $application) {
                    $this->applicationService->changeStatus(
                        $application,
                        'rejected',
                        $validated['notes'] ?? 'Bulk rejected',
                        auth()->user()
                    );
                }
                $message = 'Applications rejected.';
                break;

            case 'mark_under_review':
                foreach ($applications as $application) {
                    $this->applicationService->changeStatus(
                        $application,
                        'under_review',
                        $validated['notes'] ?? 'Under review',
                        auth()->user()
                    );
                }
                $message = 'Applications marked as under review.';
                break;

            case 'export':
                // TODO: Implement export functionality
                return $this->exportApplications($applications);
        }

        return back()->with('success', $message);
    }

    /**
     * Get application statistics
     */
    public function statistics(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->subDays(30);
        $dateTo = $request->date_to ?? now();

        $stats = [
            'total' => ServiceApplication::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            'by_status' => ServiceApplication::whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
            'by_service' => ServiceApplication::with('serviceModule')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw('service_module_id, COUNT(*) as count')
                ->groupBy('service_module_id')
                ->get()
                ->mapWithKeys(fn($item) => [$item->serviceModule->name => $item->count]),
            'avg_processing_days' => ServiceApplication::whereNotNull('approved_at')
                ->whereNotNull('submitted_at')
                ->whereBetween('submitted_at', [$dateFrom, $dateTo])
                ->selectRaw('AVG(DATEDIFF(approved_at, submitted_at)) as avg_days')
                ->value('avg_days'),
        ];

        return response()->json($stats);
    }

    /**
     * Export applications (placeholder)
     */
    protected function exportApplications($applications)
    {
        // TODO: Implement CSV/Excel export
        return response()->json([
            'message' => 'Export functionality coming soon',
            'count' => $applications->count(),
        ]);
    }

    /**
     * Download application as PDF
     */
    public function downloadPdf(ServiceApplication $application)
    {
        $application->load([
            'user.profile',
            'serviceModule.formFields',
            'documents',
        ]);

        // TODO: Implement PDF generation
        return response()->json([
            'message' => 'PDF generation coming soon',
        ]);
    }
}
