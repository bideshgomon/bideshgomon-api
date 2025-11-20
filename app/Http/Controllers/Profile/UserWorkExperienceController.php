<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\UserWorkExperience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserWorkExperienceController extends Controller
{
    /**
     * Display a listing of the user's work experience records.
     */
    public function index()
    {
        $experiences = Auth::user()->workExperiences()->orderBy('start_date', 'desc')->get();
        
        return response()->json($experiences);
    }

    /**
     * Store a newly created work experience record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'employment_type' => 'nullable|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_current_employment' => 'boolean',
            'city' => 'nullable|string|max:255',
            // Switched from normalized country_id to storing full country name for consistency with other profile sections
            'country' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:2000',
            'responsibilities' => 'nullable|array',
            'industry' => 'nullable|string|max:255',
            'job_level' => 'nullable|string|max:100',
            'salary' => 'nullable|numeric|min:0',
            'salary_currency' => 'nullable|string|max:3',
            'reference_name' => 'nullable|string|max:255',
            'reference_position' => 'nullable|string|max:255',
            'reference_email' => 'nullable|email|max:255',
            'reference_phone' => 'nullable|string|max:20',
            'experience_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'employment_contract' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'reference_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $validated['user_id'] = Auth::id();

        // Handle file uploads
        if ($request->hasFile('experience_letter')) {
            $path = $request->file('experience_letter')->store('work-experience/letters', 'public');
            $validated['experience_letter_path'] = $path;
        }

        if ($request->hasFile('employment_contract')) {
            $path = $request->file('employment_contract')->store('work-experience/contracts', 'public');
            $validated['employment_contract_path'] = $path;
        }

        if ($request->hasFile('reference_letter')) {
            $path = $request->file('reference_letter')->store('work-experience/references', 'public');
            $validated['reference_letter_path'] = $path;
        }

        $experience = UserWorkExperience::create($validated);

        return response()->json($experience, 201);
    }

    /**
     * Update the specified work experience record.
     */
    public function update(Request $request, UserWorkExperience $userWorkExperience)
    {
        // Ensure the user owns this work experience record
        if ($userWorkExperience->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'employment_type' => 'nullable|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_current_employment' => 'boolean',
            'city' => 'nullable|string|max:255',
            // Switched from normalized country_id to storing full country name for consistency with other profile sections
            'country' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:2000',
            'responsibilities' => 'nullable|array',
            'industry' => 'nullable|string|max:255',
            'job_level' => 'nullable|string|max:100',
            'salary' => 'nullable|numeric|min:0',
            'salary_currency' => 'nullable|string|max:3',
            'reference_name' => 'nullable|string|max:255',
            'reference_position' => 'nullable|string|max:255',
            'reference_email' => 'nullable|email|max:255',
            'reference_phone' => 'nullable|string|max:20',
            'experience_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'employment_contract' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'reference_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Handle file uploads
        if ($request->hasFile('experience_letter')) {
            if ($userWorkExperience->experience_letter_path) {
                Storage::disk('public')->delete($userWorkExperience->experience_letter_path);
            }
            $path = $request->file('experience_letter')->store('work-experience/letters', 'public');
            $validated['experience_letter_path'] = $path;
        }

        if ($request->hasFile('employment_contract')) {
            if ($userWorkExperience->employment_contract_path) {
                Storage::disk('public')->delete($userWorkExperience->employment_contract_path);
            }
            $path = $request->file('employment_contract')->store('work-experience/contracts', 'public');
            $validated['employment_contract_path'] = $path;
        }

        if ($request->hasFile('reference_letter')) {
            if ($userWorkExperience->reference_letter_path) {
                Storage::disk('public')->delete($userWorkExperience->reference_letter_path);
            }
            $path = $request->file('reference_letter')->store('work-experience/references', 'public');
            $validated['reference_letter_path'] = $path;
        }

        $userWorkExperience->update($validated);

        return response()->json($userWorkExperience);
    }

    /**
     * Remove the specified work experience record.
     */
    public function destroy(UserWorkExperience $userWorkExperience)
    {
        // Ensure the user owns this work experience record
        if ($userWorkExperience->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Delete associated files
        if ($userWorkExperience->experience_letter_path) {
            Storage::disk('public')->delete($userWorkExperience->experience_letter_path);
        }
        if ($userWorkExperience->employment_contract_path) {
            Storage::disk('public')->delete($userWorkExperience->employment_contract_path);
        }
        if ($userWorkExperience->reference_letter_path) {
            Storage::disk('public')->delete($userWorkExperience->reference_letter_path);
        }

        $userWorkExperience->delete();

        return response()->json(['message' => 'Work experience record deleted successfully']);
    }
}
