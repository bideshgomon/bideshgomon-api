<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\AgencyVerificationDocument;
use App\Models\AgencyVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class VerificationController extends Controller
{
    /**
     * Display the verification status and form.
     */
    public function index(Request $request)
    {
        $agency = $request->user()->agency;
        
        if (!$agency) {
            return redirect()->route('agency.dashboard')
                ->with('error', 'Agency not found.');
        }

        $latestRequest = $agency->verificationRequests()
            ->with('reviewer')
            ->latest('submitted_at')
            ->first();

        $documents = AgencyVerificationDocument::where('agency_id', $agency->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $requiredDocumentTypes = $this->getRequiredDocumentTypes();

        return Inertia::render('Agency/Verification/Index', [
            'agency' => $agency,
            'latestRequest' => $latestRequest,
            'documents' => $documents,
            'requiredDocumentTypes' => $requiredDocumentTypes,
        ]);
    }

    /**
     * Upload a verification document.
     */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'document_type' => 'required|string|in:business_license,trade_license,tax_certificate,registration_certificate,incorporation_certificate,professional_certificate,insurance_certificate,accreditation,other',
            'document_name' => 'nullable|string|max:255',
        ]);

        $agency = $request->user()->agency;

        if (!$agency) {
            return back()->with('error', 'Agency not found.');
        }

        $file = $request->file('document');
        $path = $file->store('verification-documents/' . $agency->id, 'public');

        $document = AgencyVerificationDocument::create([
            'agency_id' => $agency->id,
            'document_type' => $request->document_type,
            'document_name' => $request->document_name ?? $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'status' => 'pending',
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }

    /**
     * Delete a verification document.
     */
    public function deleteDocument(Request $request, AgencyVerificationDocument $document)
    {
        $agency = $request->user()->agency;

        if (!$agency || $document->agency_id !== $agency->id) {
            return back()->with('error', 'Unauthorized.');
        }

        // Can't delete if document is approved or part of an approved request
        if ($document->status === 'approved') {
            return back()->with('error', 'Cannot delete approved documents.');
        }

        // Delete file from storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return back()->with('success', 'Document deleted successfully.');
    }

    /**
     * Submit a verification request.
     */
    public function submitRequest(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string|max:1000',
            'document_ids' => 'required|array|min:1',
            'document_ids.*' => 'exists:agency_verification_documents,id',
        ]);

        $agency = $request->user()->agency;

        if (!$agency) {
            return back()->with('error', 'Agency not found.');
        }

        // Check if there's already a pending request
        $existingRequest = AgencyVerificationRequest::where('agency_id', $agency->id)
            ->whereIn('status', ['pending', 'under_review'])
            ->first();

        if ($existingRequest) {
            return back()->with('error', 'You already have a pending verification request.');
        }

        // Verify all documents belong to this agency and are pending
        $documents = AgencyVerificationDocument::whereIn('id', $request->document_ids)
            ->where('agency_id', $agency->id)
            ->where('status', 'pending')
            ->get();

        if ($documents->count() !== count($request->document_ids)) {
            return back()->with('error', 'Invalid documents selected.');
        }

        // Create verification request
        $verificationRequest = AgencyVerificationRequest::create([
            'agency_id' => $agency->id,
            'status' => 'pending',
            'message' => $request->message,
            'submitted_at' => now(),
            'required_documents' => array_keys($this->getRequiredDocumentTypes()),
            'submitted_documents' => $request->document_ids,
        ]);

        return back()->with('success', 'Verification request submitted successfully. We will review it shortly.');
    }

    /**
     * Get required document types.
     */
    protected function getRequiredDocumentTypes(): array
    {
        return [
            'business_license' => 'Business License',
            'trade_license' => 'Trade License',
            'tax_certificate' => 'Tax Certificate',
            'registration_certificate' => 'Registration Certificate',
            'incorporation_certificate' => 'Incorporation Certificate',
            'professional_certificate' => 'Professional Certificate (if applicable)',
            'insurance_certificate' => 'Insurance Certificate (if applicable)',
            'accreditation' => 'Accreditation Document (if applicable)',
        ];
    }
}
