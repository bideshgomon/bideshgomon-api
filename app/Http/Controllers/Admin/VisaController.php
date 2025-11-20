<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisaApplication;
use App\Models\VisaDocument;
use App\Models\VisaAppointment;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VisaController extends Controller
{
    /**
     * Display all visa applications
     */
    public function index(Request $request)
    {
        $query = VisaApplication::with(['user', 'assignedTo', 'documents', 'appointments']);

        // Filter by status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter by visa type
        if ($request->filled('visa_type')) {
            $query->byVisaType($request->visa_type);
        }

        // Filter by destination
        if ($request->filled('destination')) {
            $query->byDestination($request->destination);
        }

        // Filter by assigned staff
        if ($request->filled('assigned_to')) {
            $query->assignedTo($request->assigned_to);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            if ($request->payment_status === 'paid') {
                $query->paid();
            } elseif ($request->payment_status === 'unpaid') {
                $query->unpaid();
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('application_reference', 'like', "%{$search}%")
                  ->orWhere('applicant_name', 'like', "%{$search}%")
                  ->orWhere('applicant_email', 'like', "%{$search}%")
                  ->orWhere('passport_number', 'like', "%{$search}%");
            });
        }

        $applications = $query->latest()->paginate(20);

        $stats = [
            'total' => VisaApplication::count(),
            'pending' => VisaApplication::pending()->count(),
            'under_review' => VisaApplication::underReview()->count(),
            'approved' => VisaApplication::approved()->count(),
            'rejected' => VisaApplication::rejected()->count(),
            'revenue' => VisaApplication::paid()->sum('total_amount'),
        ];

        $staffMembers = User::whereHas('role', function($q) {
            $q->whereIn('slug', ['admin', 'consultant', 'agency']);
        })->get(['id', 'name', 'email']);

        return Inertia::render('Admin/Visa/Index', [
            'applications' => $applications,
            'stats' => $stats,
            'staffMembers' => $staffMembers,
            'filters' => $request->only(['status', 'visa_type', 'destination', 'assigned_to', 'payment_status', 'search']),
        ]);
    }

    /**
     * Show application details
     */
    public function show(VisaApplication $application)
    {
        $application->load([
            'user',
            'assignedTo',
            'documents' => fn($q) => $q->latest(),
            'appointments' => fn($q) => $q->latest(),
        ]);

        return Inertia::render('Admin/Visa/Show', [
            'application' => $application,
        ]);
    }

    /**
     * Update application status
     */
    public function updateStatus(Request $request, VisaApplication $application)
    {
        $validated = $request->validate([
            'status' => 'required|in:submitted,under_review,documents_requested,documents_received,interview_scheduled,approved,rejected,cancelled',
            'status_notes' => 'nullable|string|max:1000',
        ]);

        $application->update($validated);

        // Auto-update dates based on status
        if ($validated['status'] === 'approved') {
            $application->update(['approved_at' => now()]);
        } elseif ($validated['status'] === 'rejected') {
            $application->update(['rejected_at' => now()]);
        }

        return back()->with('success', 'Application status updated successfully.');
    }

    /**
     * Assign application to staff
     */
    public function assign(Request $request, VisaApplication $application)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'priority' => 'nullable|in:normal,high,urgent',
        ]);

        $application->assignTo($validated['assigned_to']);

        if (isset($validated['priority'])) {
            $application->update(['priority' => $validated['priority']]);
        }

        return back()->with('success', 'Application assigned successfully.');
    }

    /**
     * Verify or reject document
     */
    public function verifyDocument(Request $request, VisaDocument $document)
    {
        $validated = $request->validate([
            'status' => 'required|in:verified,rejected,reupload_required',
            'verification_notes' => 'nullable|string|max:500',
        ]);

        $validated['verified_by'] = auth()->id();
        $validated['verified_at'] = now();

        $document->update($validated);

        if ($validated['status'] === 'verified') {
            return back()->with('success', 'Document verified successfully.');
        } elseif ($validated['status'] === 'rejected') {
            return back()->with('success', 'Document rejected.');
        } else {
            return back()->with('success', 'Document marked for re-upload.');
        }
    }

    /**
     * Schedule appointment
     */
    public function scheduleAppointment(Request $request, VisaApplication $application)
    {
        $validated = $request->validate([
            'appointment_type' => 'required|in:interview,biometrics,document_submission,visa_collection',
            'appointment_date' => 'required|date|after:now',
            'location' => 'required|string|max:255',
            'location_address' => 'nullable|string|max:500',
            'instructions' => 'nullable|string|max:1000',
            'send_sms_reminder' => 'nullable|boolean',
            'send_email_reminder' => 'nullable|boolean',
            'reminder_hours_before' => 'nullable|integer|min:1|max:168',
        ]);

        $validated['visa_application_id'] = $application->id;
        $validated['status'] = 'scheduled';

        $appointment = VisaAppointment::create($validated);

        // Update application status
        if ($validated['appointment_type'] === 'interview') {
            $application->scheduleInterview();
        }

        return back()->with('success', 'Appointment scheduled successfully.');
    }

    /**
     * Approve application
     */
    public function approve(Request $request, VisaApplication $application)
    {
        $validated = $request->validate([
            'visa_number' => 'required|string|max:100',
            'visa_issue_date' => 'required|date',
            'visa_expiry_date' => 'required|date|after:visa_issue_date',
            'approval_notes' => 'nullable|string|max:1000',
        ]);

        $application->approve();
        $application->update($validated);

        return back()->with('success', 'Visa application approved successfully.');
    }

    /**
     * Reject application
     */
    public function reject(Request $request, VisaApplication $application)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $application->reject($validated['rejection_reason']);

        return back()->with('success', 'Visa application rejected.');
    }

    /**
     * Request additional documents
     */
    public function requestDocuments(Request $request, VisaApplication $application)
    {
        $validated = $request->validate([
            'required_documents' => 'required|array|min:1',
            'required_documents.*' => 'required|in:passport,photo,bank_statement,invitation_letter,travel_itinerary,accommodation_proof,employment_letter,education_certificate,medical_reports,police_clearance,sponsor_letter,other',
            'request_notes' => 'nullable|string|max:1000',
        ]);

        $application->requestDocuments();
        $application->update([
            'status_notes' => $validated['request_notes'] ?? 'Additional documents requested: ' . implode(', ', $validated['required_documents']),
        ]);

        return back()->with('success', 'Document request sent to applicant.');
    }

    /**
     * Update application priority
     */
    public function updatePriority(Request $request, VisaApplication $application)
    {
        $validated = $request->validate([
            'priority' => 'required|in:normal,high,urgent',
        ]);

        $application->update($validated);

        return back()->with('success', 'Application priority updated.');
    }

    /**
     * Add internal notes
     */
    public function addNote(Request $request, VisaApplication $application)
    {
        $validated = $request->validate([
            'note' => 'required|string|max:2000',
        ]);

        $currentNotes = $application->internal_notes ?? '';
        $newNote = "[" . now()->format('Y-m-d H:i') . " - " . auth()->user()->name . "]\n" . $validated['note'];
        
        $application->update([
            'internal_notes' => $currentNotes . "\n\n" . $newNote,
        ]);

        return back()->with('success', 'Note added successfully.');
    }
}
