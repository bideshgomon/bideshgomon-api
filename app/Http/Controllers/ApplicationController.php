<?php

namespace App\Http\Controllers;

use App\Models\ServiceModule;
use App\Models\ServiceApplication;
use App\Services\ServiceApplicationService;
use App\Services\DataMapperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ApplicationController extends Controller
{
    protected $applicationService;
    protected $dataMapper;

    public function __construct(
        ServiceApplicationService $applicationService,
        DataMapperService $dataMapper
    ) {
        $this->applicationService = $applicationService;
        $this->dataMapper = $dataMapper;
    }

    /**
     * Display application form
     */
    public function create(string $serviceSlug): Response
    {
        $service = ServiceModule::with(['formFields' => function ($query) {
            $query->active()->ordered();
        }])
        ->where('slug', $serviceSlug)
        ->active()
        ->firstOrFail();

        // Check if user already has a pending application
        $existingApplication = auth()->user()
            ->serviceApplications()
            ->where('service_module_id', $service->id)
            ->whereIn('status', ['draft', 'pending', 'under_review'])
            ->latest()
            ->first();

        if ($existingApplication && $existingApplication->status !== 'draft') {
            return redirect()->route('applications.show', $existingApplication->id)
                ->with('info', 'You already have a pending application for this service.');
        }

        // Get form with pre-filled data from user profile
        $formData = $this->dataMapper->getFormWithData($service, auth()->user());

        return Inertia::render('Applications/Create', [
            'service' => $service,
            'formData' => $formData,
            'draftApplication' => $existingApplication,
        ]);
    }

    /**
     * Store new application (as draft or submitted)
     */
    public function store(Request $request, string $serviceSlug)
    {
        $service = ServiceModule::where('slug', $serviceSlug)
            ->active()
            ->firstOrFail();

        // Check if saving as draft or submitting
        $isDraft = $request->input('save_as_draft', false);

        try {
            DB::beginTransaction();

            $application = $this->applicationService->createApplication(
                $service,
                auth()->user(),
                $request->input('form_data', []),
                $isDraft
            );

            // Handle file uploads
            if ($request->hasFile('documents')) {
                $documents = [];
                foreach ($request->file('documents') as $fieldName => $file) {
                    $documents[$fieldName] = [
                        'file' => $file,
                        'document_type' => $fieldName,
                    ];
                }
                $this->applicationService->attachDocuments($application, $documents);
            }

            // Update profile if user checked "save to profile"
            if ($request->input('save_to_profile', false)) {
                $this->dataMapper->updateProfileFromFormData(
                    auth()->user(),
                    $service,
                    $request->input('form_data', [])
                );
            }

            DB::commit();

            $message = $isDraft 
                ? 'Application saved as draft. You can complete it later.'
                : 'Application submitted successfully! You will receive updates via email.';

            return redirect()->route('applications.show', $application->id)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display user's applications list
     */
    public function index(Request $request): Response
    {
        $query = auth()->user()
            ->serviceApplications()
            ->with(['serviceModule'])
            ->when($request->status, function ($q, $status) {
                $q->where('status', $status);
            })
            ->when($request->search, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('application_number', 'like', "%{$search}%")
                          ->orWhereHas('serviceModule', function ($serviceQuery) use ($search) {
                              $serviceQuery->where('name', 'like', "%{$search}%");
                          });
                });
            })
            ->latest();

        $applications = $query->paginate(10)->withQueryString();

        // Get counts by status
        $statusCounts = [
            'all' => auth()->user()->serviceApplications()->count(),
            'draft' => auth()->user()->serviceApplications()->where('status', 'draft')->count(),
            'pending' => auth()->user()->serviceApplications()->where('status', 'pending')->count(),
            'under_review' => auth()->user()->serviceApplications()->where('status', 'under_review')->count(),
            'approved' => auth()->user()->serviceApplications()->where('status', 'approved')->count(),
            'rejected' => auth()->user()->serviceApplications()->where('status', 'rejected')->count(),
        ];

        return Inertia::render('Applications/Index', [
            'applications' => $applications,
            'statusCounts' => $statusCounts,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    /**
     * Display specific application
     */
    public function show(ServiceApplication $application): Response
    {
        // Ensure user owns this application
        if ($application->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to application.');
        }

        $application->load([
            'serviceModule.formFields',
            'documents',
            'statusHistory.changer',
        ]);

        return Inertia::render('Applications/Show', [
            'application' => $application,
            'canEdit' => $application->status === 'draft',
            'canCancel' => in_array($application->status, ['draft', 'pending', 'under_review']),
        ]);
    }

    /**
     * Update draft application
     */
    public function update(Request $request, ServiceApplication $application)
    {
        // Ensure user owns this application
        if ($application->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to application.');
        }

        // Can only update drafts
        if ($application->status !== 'draft') {
            return back()->with('error', 'Only draft applications can be edited.');
        }

        try {
            DB::beginTransaction();

            $this->applicationService->updateApplication(
                $application,
                $request->input('form_data', [])
            );

            // Handle file uploads
            if ($request->hasFile('documents')) {
                $documents = [];
                foreach ($request->file('documents') as $fieldName => $file) {
                    $documents[$fieldName] = [
                        'file' => $file,
                        'document_type' => $fieldName,
                    ];
                }
                $this->applicationService->attachDocuments($application, $documents);
            }

            // Update profile if requested
            if ($request->input('save_to_profile', false)) {
                $this->dataMapper->updateProfileFromFormData(
                    auth()->user(),
                    $application->serviceModule,
                    $request->input('form_data', [])
                );
            }

            DB::commit();

            return back()->with('success', 'Application updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Submit draft application
     */
    public function submit(ServiceApplication $application)
    {
        // Ensure user owns this application
        if ($application->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to application.');
        }

        // Can only submit drafts
        if ($application->status !== 'draft') {
            return back()->with('error', 'This application has already been submitted.');
        }

        try {
            $this->applicationService->submitDraftApplication($application);

            return redirect()->route('applications.show', $application->id)
                ->with('success', 'Application submitted successfully! You will receive updates via email.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel application
     */
    public function cancel(ServiceApplication $application)
    {
        // Ensure user owns this application
        if ($application->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to application.');
        }

        // Can only cancel certain statuses
        if (!in_array($application->status, ['draft', 'pending', 'under_review'])) {
            return back()->with('error', 'This application cannot be cancelled.');
        }

        try {
            $this->applicationService->changeStatus(
                $application,
                'cancelled',
                'Cancelled by user',
                auth()->user()
            );

            return back()->with('success', 'Application cancelled successfully.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete draft application
     */
    public function destroy(ServiceApplication $application)
    {
        // Ensure user owns this application
        if ($application->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to application.');
        }

        // Can only delete drafts
        if ($application->status !== 'draft') {
            return back()->with('error', 'Only draft applications can be deleted.');
        }

        try {
            DB::beginTransaction();

            // Delete associated documents
            foreach ($application->documents as $document) {
                if ($document->file_path && \Storage::disk('public')->exists($document->file_path)) {
                    \Storage::disk('public')->delete($document->file_path);
                }
                $document->delete();
            }

            $application->delete();

            DB::commit();

            return redirect()->route('applications.index')
                ->with('success', 'Draft application deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Download application PDF
     */
    public function downloadPdf(ServiceApplication $application)
    {
        // Ensure user owns this application
        if ($application->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to application.');
        }

        $application->load([
            'serviceModule.formFields',
            'documents',
        ]);

        // TODO: Implement PDF generation using DomPDF or similar
        return response()->json([
            'message' => 'PDF generation coming soon',
            'application' => $application,
        ]);
    }
}
