<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\UserPassport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PassportController extends Controller
{
    /**
     * Display a listing of the user's passports.
     */
    public function index()
    {
        $passports = Auth::user()->passports()
            ->orderBy('is_current_passport', 'desc')
            ->orderBy('expiry_date', 'desc')
            ->get();

        return Inertia::render('Profile/PassportManagement', [
            'passports' => $passports,
        ]);
    }

    /**
     * Store a newly created passport in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'passport_number' => [
                    'nullable',
                    'string',
                    'max:50',
                    'unique:user_passports,passport_number,NULL,id,user_id,' . Auth::id()
                ],
                'passport_type' => 'nullable|in:regular,diplomatic,official,service,emergency',
                'issuing_country' => 'nullable|string|max:2',
                'issuing_authority' => 'nullable|string|max:255',
                'issue_date' => 'nullable|date',
                'expiry_date' => 'nullable|date',
                'place_of_issue' => 'nullable|string|max:255',
                'is_current_passport' => 'boolean',
                'is_lost_or_stolen' => 'boolean',
                'reported_lost_date' => 'nullable|date',
                'surname' => 'nullable|string|max:255',
                'given_names' => 'nullable|string|max:255',
                'nationality' => 'nullable|string|max:2',
                'sex' => 'nullable|in:M,F,X',
                'date_of_birth' => 'nullable|date',
                'place_of_birth' => 'nullable|string|max:255',
                'passport_scan' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'additional_pages' => 'nullable|file|mimes:pdf|max:10240',
                'notes' => 'nullable|string|max:1000',
            ]);

            // Handle file uploads
            if ($request->hasFile('passport_scan')) {
                $validated['passport_scan_path'] = $request->file('passport_scan')
                    ->store('passports/scans', 'public');
                unset($validated['passport_scan']);
            }

            if ($request->hasFile('additional_pages')) {
                $validated['additional_pages_path'] = $request->file('additional_pages')
                    ->store('passports/pages', 'public');
                unset($validated['additional_pages']);
            }

            $validated['user_id'] = Auth::id();

            $passport = UserPassport::create($validated);

            return redirect()->back()->with('success', 'Passport added successfully.');
        } catch (\Exception $e) {
            Log::error('Passport creation failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return redirect()->back()->withErrors(['error' => 'Failed to add passport. Please check your input.'])->withInput();
        }
    }

    /**
     * Update the specified passport in storage.
     */
    public function update(Request $request, $id)
    {
        $passport = Auth::user()->passports()->findOrFail($id);

        $validated = $request->validate([
            'passport_number' => [
                'required',
                'string',
                'max:50',
                'unique:user_passports,passport_number,' . $id . ',id,user_id,' . Auth::id()
            ],
            'passport_type' => 'required|in:regular,diplomatic,official,service,emergency',
            'issuing_country' => 'required|string|max:2',
            'issuing_authority' => 'nullable|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'place_of_issue' => 'nullable|string|max:255',
            'is_current_passport' => 'boolean',
            'is_lost_or_stolen' => 'boolean',
            'reported_lost_date' => 'nullable|date',
            'surname' => 'nullable|string|max:255',
            'given_names' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:2',
            'sex' => 'nullable|in:M,F,X',
            'date_of_birth' => 'nullable|date',
            'place_of_birth' => 'nullable|string|max:255',
            'passport_scan' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'additional_pages' => 'nullable|file|mimes:pdf|max:10240',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Handle file uploads
        if ($request->hasFile('passport_scan')) {
            if ($passport->passport_scan_path) {
                Storage::disk('public')->delete($passport->passport_scan_path);
            }
            $validated['passport_scan_path'] = $request->file('passport_scan')
                ->store('passports/scans', 'public');
            unset($validated['passport_scan']);
        }

        if ($request->hasFile('additional_pages')) {
            if ($passport->additional_pages_path) {
                Storage::disk('public')->delete($passport->additional_pages_path);
            }
            $validated['additional_pages_path'] = $request->file('additional_pages')
                ->store('passports/pages', 'public');
            unset($validated['additional_pages']);
        }

        $passport->update($validated);

        return redirect()->back()->with('success', 'Passport updated successfully.');
    }

    /**
     * Remove the specified passport from storage.
     */
    public function destroy($id)
    {
        $passport = Auth::user()->passports()->findOrFail($id);

        // Check if passport is referenced in visa history or travel history
        if ($passport->visaHistory()->exists()) {
            return redirect()->back()->withErrors([
                'error' => 'Cannot delete passport that has visa history records.'
            ]);
        }

        if ($passport->travelHistory()->exists()) {
            return redirect()->back()->withErrors([
                'error' => 'Cannot delete passport that has travel history records.'
            ]);
        }

        // Delete uploaded files
        if ($passport->passport_scan_path) {
            Storage::disk('public')->delete($passport->passport_scan_path);
        }
        if ($passport->additional_pages_path) {
            Storage::disk('public')->delete($passport->additional_pages_path);
        }

        $passport->delete();

        return redirect()->back()->with('success', 'Passport deleted successfully.');
    }
}
