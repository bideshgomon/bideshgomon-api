<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\UserVisaHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class VisaHistoryController extends Controller
{
    /**
     * Display a listing of the user's visa history.
     */
    public function index()
    {
        $visaHistory = Auth::user()->visaHistory()
            ->with('passport')
            ->orderBy('application_date', 'desc')
            ->get()
            ->map(function ($visa) {
                return [
                    'id' => $visa->id,
                    'user_passport_id' => $visa->user_passport_id,
                    'country' => $visa->country,
                    'visa_type' => $visa->visa_type,
                    'visa_number' => $visa->visa_number,
                    'application_date' => $visa->application_date,
                    'issue_date' => $visa->issue_date,
                    'expiry_date' => $visa->expiry_date,
                    'valid_from' => $visa->valid_from,
                    'valid_to' => $visa->valid_to,
                    'status' => $visa->status,
                    'rejection_reason' => $visa->rejection_reason,
                    'rejection_date' => $visa->rejection_date,
                    'appeal_status' => $visa->appeal_status,
                    'duration_days' => $visa->duration_days,
                    'entry_type' => $visa->entry_type,
                    'purpose' => $visa->purpose,
                    'sponsoring_organization' => $visa->sponsoring_organization,
                    'overstay_days' => $visa->overstay_days,
                    'violation_details' => $visa->violation_details,
                    'consulate_location' => $visa->consulate_location,
                    'visa_fee_paid' => $visa->visa_fee_paid,
                    'supporting_documents_uploaded' => $visa->supporting_documents_uploaded,
                    'notes' => $visa->notes,
                    'is_rejected' => $visa->isRejected(),
                    'has_overstayed' => $visa->hasOverstayed(),
                    'is_active' => $visa->isActive(),
                    'created_at' => $visa->created_at,
                    'updated_at' => $visa->updated_at,
                ];
            });

        $passports = Auth::user()->passports()
            ->orderBy('is_primary', 'desc')
            ->orderBy('expiry_date', 'desc')
            ->get(['id', 'passport_number', 'issuing_country', 'expiry_date']);

        return Inertia::render('Profile/VisaHistoryManagement', [
            'visaHistory' => $visaHistory,
            'passports' => $passports,
        ]);
    }

    /**
     * Store a newly created visa history record in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_passport_id' => 'required|exists:user_passports,id',
            'country' => 'required|string|size:2', // ISO 2-letter code
            'visa_type' => 'required|in:tourist,business,student,work,transit,family_visit,medical,conference,other',
            'visa_category' => 'nullable|string|max:50',
            'visa_number' => 'nullable|string|max:100',
            'application_date' => 'required|date',
            'application_reference' => 'nullable|string|max:100',
            'issue_date' => 'nullable|date|after_or_equal:application_date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'status' => 'required|in:approved,rejected,pending,cancelled,expired',
            'was_visa_rejected' => 'boolean',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:1000',
            'entry_date' => 'nullable|date',
            'exit_date' => 'nullable|date|after_or_equal:entry_date',
            'duration_of_stay' => 'nullable|integer|min:1',
            'purpose_of_visit' => 'nullable|string|max:500',
            'overstay_occurred' => 'boolean',
            'overstay_days' => 'nullable|integer|min:0',
            'embassy_location' => 'nullable|string|max:255',
            'visa_fee' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3', // ISO currency code
            'supporting_documents' => 'nullable|array',
            'supporting_documents.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Verify passport belongs to user
        $passport = Auth::user()->passports()->findOrFail($validated['user_passport_id']);

        // Handle file uploads
        if ($request->hasFile('supporting_documents')) {
            $uploadedFiles = [];
            foreach ($request->file('supporting_documents') as $file) {
                $uploadedFiles[] = $file->store('visa-history/documents', 'public');
            }
            $validated['supporting_documents_uploaded'] = json_encode($uploadedFiles);
        }

        // Add user_id
        $validated['user_id'] = Auth::id();

        // Remove supporting_documents array from validated data
        unset($validated['supporting_documents']);

        $visaHistory = UserVisaHistory::create($validated);

        return redirect()->back()->with('success', 'Visa history record added successfully.');
    }

    /**
     * Update the specified visa history record in storage.
     */
    public function update(Request $request, $id)
    {
        $visaHistory = Auth::user()->visaHistory()->findOrFail($id);

        $validated = $request->validate([
            'user_passport_id' => 'required|exists:user_passports,id',
            'country' => 'required|string|size:2', // ISO 2-letter code
            'visa_type' => 'required|in:tourist,business,student,work,transit,family_visit,medical,conference,other',
            'visa_category' => 'nullable|string|max:50',
            'visa_number' => 'nullable|string|max:100',
            'application_date' => 'required|date',
            'application_reference' => 'nullable|string|max:100',
            'issue_date' => 'nullable|date|after_or_equal:application_date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'status' => 'required|in:approved,rejected,pending,cancelled,expired',
            'was_visa_rejected' => 'boolean',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:1000',
            'entry_date' => 'nullable|date',
            'exit_date' => 'nullable|date|after_or_equal:entry_date',
            'duration_of_stay' => 'nullable|integer|min:1',
            'purpose_of_visit' => 'nullable|string|max:500',
            'overstay_occurred' => 'boolean',
            'overstay_days' => 'nullable|integer|min:0',
            'embassy_location' => 'nullable|string|max:255',
            'visa_fee' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3', // ISO currency code
            'supporting_documents' => 'nullable|array',
            'supporting_documents.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Verify passport belongs to user
        $passport = Auth::user()->passports()->findOrFail($validated['user_passport_id']);

        // Handle file uploads
        if ($request->hasFile('supporting_documents')) {
            // Delete old files if exists
            if ($visaHistory->supporting_documents_uploaded) {
                $oldFiles = json_decode($visaHistory->supporting_documents_uploaded, true);
                if (is_array($oldFiles)) {
                    foreach ($oldFiles as $oldFile) {
                        Storage::disk('public')->delete($oldFile);
                    }
                }
            }

            $uploadedFiles = [];
            foreach ($request->file('supporting_documents') as $file) {
                $uploadedFiles[] = $file->store('visa-history/documents', 'public');
            }
            $validated['supporting_documents_uploaded'] = json_encode($uploadedFiles);
        }

        // Remove supporting_documents array from validated data
        unset($validated['supporting_documents']);

        $visaHistory->update($validated);

        return redirect()->back()->with('success', 'Visa history record updated successfully.');
    }

    /**
     * Remove the specified visa history record from storage.
     */
    public function destroy($id)
    {
        $visaHistory = Auth::user()->visaHistory()->findOrFail($id);

        // Check if visa history is referenced in travel history
        if ($visaHistory->travelHistory()->exists()) {
            return redirect()->back()->withErrors([
                'error' => 'Cannot delete visa history that has related travel history records.'
            ]);
        }

        // Delete uploaded files
        if ($visaHistory->supporting_documents_uploaded) {
            $files = json_decode($visaHistory->supporting_documents_uploaded, true);
            if (is_array($files)) {
                foreach ($files as $file) {
                    Storage::disk('public')->delete($file);
                }
            }
        }

        $visaHistory->delete();

        return redirect()->back()->with('success', 'Visa history record deleted successfully.');
    }
}
