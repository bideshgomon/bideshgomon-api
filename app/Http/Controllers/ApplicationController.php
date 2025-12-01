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
        $formWithData = $this->dataMapper->getFormWithData($service, auth()->user());

        return Inertia::render('Services/ApplicationForm', [
            'service' => $service,
            'formFields' => $service->formFields,
            'prefilledData' => $formWithData['formData'] ?? [],
            'existingApplication' => $existingApplication,
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

            // Redirect to success page for submitted applications
            if (!$isDraft) {
                return Inertia::render('Success/ApplicationSuccess', [
                    'application' => [
                        'id' => $application->id,
                        'application_number' => $application->application_number,
                        'status' => $application->status,
                        'amount_paid' => $application->amount_paid,
                        'created_at' => $application->created_at,
                    ],
                    'service' => [
                        'id' => $service->id,
                        'name' => $service->name,
                        'slug' => $service->slug,
                    ]
                ]);
            }

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
            ->with(['serviceModule', 'statusHistory' => function ($q) {
                $q->latest()->limit(1);
            }])
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

        $applications = $query->paginate(20)->withQueryString();

        // Transform applications to include service relationship
        $applications->through(function ($application) {
            return [
                'id' => $application->id,
                'application_number' => $application->application_number,
                'status' => $application->status,
                'submitted_at' => $application->submitted_at,
                'created_at' => $application->created_at,
                'updated_at' => $application->updated_at,
                'service' => [
                    'id' => $application->serviceModule->id,
                    'name' => $application->serviceModule->name,
                    'slug' => $application->serviceModule->slug,
                    'processing_days' => $application->serviceModule->processing_days,
                ],
                'latest_status_update' => $application->statusHistory->first(),
            ];
        });

        return Inertia::render('Services/MyApplications', [
            'applications' => $applications,
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
            'statusHistory' => function ($query) {
                $query->with('changer')->latest();
            },
        ]);

        // Group form data by field groups
        $groupedFormData = [];
        if ($application->form_data && $application->serviceModule->formFields) {
            foreach ($application->serviceModule->formFields as $field) {
                $groupName = $field->group_name ?: 'General Information';
                if (!isset($groupedFormData[$groupName])) {
                    $groupedFormData[$groupName] = [];
                }
                
                $groupedFormData[$groupName][] = [
                    'name' => $field->field_name,
                    'label' => $field->field_label,
                    'value' => $application->form_data[$field->field_name] ?? null,
                    'type' => $field->field_type,
                ];
            }
        }

        return Inertia::render('Services/ApplicationDetails', [
            'application' => [
                'id' => $application->id,
                'application_number' => $application->application_number,
                'status' => $application->status,
                'form_data' => $application->form_data,
                'profile_snapshot' => $application->profile_snapshot,
                'submitted_at' => $application->submitted_at,
                'created_at' => $application->created_at,
                'updated_at' => $application->updated_at,
                'service' => [
                    'id' => $application->serviceModule->id,
                    'name' => $application->serviceModule->name,
                    'slug' => $application->serviceModule->slug,
                    'short_description' => $application->serviceModule->short_description,
                    'processing_days' => $application->serviceModule->processing_days,
                    'requires_approval' => $application->serviceModule->requires_approval,
                ],
                'documents' => $application->documents,
                'status_history' => $application->statusHistory->map(function ($history) {
                    return [
                        'id' => $history->id,
                        'old_status' => $history->old_status,
                        'new_status' => $history->new_status,
                        'notes' => $history->notes,
                        'created_at' => $history->created_at,
                        'changed_by_name' => $history->changer ? $history->changer->name : null,
                    ];
                }),
                'grouped_form_data' => $groupedFormData,
            ],
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
