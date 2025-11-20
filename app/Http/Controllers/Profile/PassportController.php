<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\UserPassport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            ->orderBy('is_primary', 'desc')
            ->orderBy('expiry_date', 'desc')
            ->get()
            ->map(function ($passport) {
                return [
                    'id' => $passport->id,
                    'passport_number' => $passport->passport_number,
                    'full_name_on_passport' => $passport->full_name_on_passport,
                    'issuing_country' => $passport->issuing_country,
                    'nationality' => $passport->nationality,
                    'date_of_birth' => $passport->date_of_birth,
                    'place_of_birth' => $passport->place_of_birth,
                    'issue_date' => $passport->issue_date,
                    'expiry_date' => $passport->expiry_date,
                    'issuing_authority' => $passport->issuing_authority,
                    'passport_type' => $passport->passport_type,
                    'mrz_code' => $passport->mrz_code,
                    'pages_count' => $passport->pages_count,
                    'is_primary' => $passport->is_primary,
                    'scan_front_upload' => $passport->scan_front_upload,
                    'scan_back_upload' => $passport->scan_back_upload,
                    'scan_bio_page_upload' => $passport->scan_bio_page_upload,
                    'notes' => $passport->notes,
                    'is_valid' => $passport->isValid(),
                    'is_expiring_soon' => $passport->expiresWithinMonths(6),
                    'days_until_expiry' => $passport->daysUntilExpiry(),
                    'created_at' => $passport->created_at,
                    'updated_at' => $passport->updated_at,
                ];
            });

        return Inertia::render('Profile/PassportManagement', [
            'passports' => $passports,
        ]);
    }

    /**
     * Store a newly created passport in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'passport_number' => [
                'required',
                'string',
                'max:50',
                'unique:user_passports,passport_number,NULL,id,user_id,' . Auth::id()
            ],
            'full_name_on_passport' => 'required|string|max:255',
            'issuing_country' => 'required|string|max:100',
            'nationality' => 'required|string|max:100',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'issuing_authority' => 'nullable|string|max:255',
            'passport_type' => 'required|in:regular,diplomatic,official,emergency',
            'mrz_code' => 'nullable|string|max:255',
            'pages_count' => 'nullable|integer|min:20|max:100',
            'is_primary' => 'boolean',
            'scan_front' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'scan_back' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'scan_bio_page' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'notes' => 'nullable|string|max:1000',
        ]);

        // If this is marked as primary, unset other primary passports
        if ($validated['is_primary'] ?? false) {
            Auth::user()->passports()->update(['is_primary' => false]);
        }

        // Handle file uploads
        if ($request->hasFile('scan_front')) {
            $validated['scan_front_upload'] = $request->file('scan_front')
                ->store('passports/scans', 'public');
        }

        if ($request->hasFile('scan_back')) {
            $validated['scan_back_upload'] = $request->file('scan_back')
                ->store('passports/scans', 'public');
        }

        if ($request->hasFile('scan_bio_page')) {
            $validated['scan_bio_page_upload'] = $request->file('scan_bio_page')
                ->store('passports/scans', 'public');
        }

        // Remove file keys and add user_id
        unset($validated['scan_front'], $validated['scan_back'], $validated['scan_bio_page']);
        $validated['user_id'] = Auth::id();

        $passport = UserPassport::create($validated);

        return redirect()->back()->with('success', 'Passport added successfully.');
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
            'full_name_on_passport' => 'required|string|max:255',
            'issuing_country' => 'required|string|max:100',
            'nationality' => 'required|string|max:100',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'issuing_authority' => 'nullable|string|max:255',
            'passport_type' => 'required|in:regular,diplomatic,official,emergency',
            'mrz_code' => 'nullable|string|max:255',
            'pages_count' => 'nullable|integer|min:20|max:100',
            'is_primary' => 'boolean',
            'scan_front' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'scan_back' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'scan_bio_page' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'notes' => 'nullable|string|max:1000',
        ]);

        // If this is marked as primary, unset other primary passports
        if ($validated['is_primary'] ?? false) {
            Auth::user()->passports()->where('id', '!=', $id)->update(['is_primary' => false]);
        }

        // Handle file uploads
        if ($request->hasFile('scan_front')) {
            // Delete old file if exists
            if ($passport->scan_front_upload) {
                Storage::disk('public')->delete($passport->scan_front_upload);
            }
            $validated['scan_front_upload'] = $request->file('scan_front')
                ->store('passports/scans', 'public');
        }

        if ($request->hasFile('scan_back')) {
            // Delete old file if exists
            if ($passport->scan_back_upload) {
                Storage::disk('public')->delete($passport->scan_back_upload);
            }
            $validated['scan_back_upload'] = $request->file('scan_back')
                ->store('passports/scans', 'public');
        }

        if ($request->hasFile('scan_bio_page')) {
            // Delete old file if exists
            if ($passport->scan_bio_page_upload) {
                Storage::disk('public')->delete($passport->scan_bio_page_upload);
            }
            $validated['scan_bio_page_upload'] = $request->file('scan_bio_page')
                ->store('passports/scans', 'public');
        }

        // Remove file keys
        unset($validated['scan_front'], $validated['scan_back'], $validated['scan_bio_page']);

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
        if ($passport->scan_front_upload) {
            Storage::disk('public')->delete($passport->scan_front_upload);
        }
        if ($passport->scan_back_upload) {
            Storage::disk('public')->delete($passport->scan_back_upload);
        }
        if ($passport->scan_bio_page_upload) {
            Storage::disk('public')->delete($passport->scan_bio_page_upload);
        }

        $passport->delete();

        return redirect()->back()->with('success', 'Passport deleted successfully.');
    }
}
