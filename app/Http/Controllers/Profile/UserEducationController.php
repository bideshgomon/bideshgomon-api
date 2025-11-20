<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\UserEducation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserEducationController extends Controller
{
    /**
     * Display a listing of the user's education records.
     */
    public function index()
    {
        $educations = Auth::user()->educations()->orderBy('start_date', 'desc')->get();
        
        return response()->json($educations);
    }

    /**
     * Store a newly created education record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'institution_name' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'field_of_study' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'is_completed' => 'boolean',
            'gpa_or_grade' => 'nullable|string|max:100',
            'language_of_instruction' => 'nullable|string|max:100',
            'courses_completed' => 'nullable|string|max:500',
            'honors_awards' => 'nullable|string|max:500',
            'degree_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'transcript' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $validated['user_id'] = Auth::id();

        // Handle file uploads
        if ($request->hasFile('degree_certificate')) {
            $path = $request->file('degree_certificate')->store('education/certificates', 'public');
            $validated['degree_certificate_path'] = $path;
            unset($validated['degree_certificate']);
        }

        if ($request->hasFile('transcript')) {
            $path = $request->file('transcript')->store('education/transcripts', 'public');
            $validated['transcript_path'] = $path;
            unset($validated['transcript']);
        }

        $education = UserEducation::create($validated);

        return response()->json($education, 201);
    }

    /**
     * Update the specified education record.
     */
    public function update(Request $request, UserEducation $userEducation)
    {
        // Ensure the user owns this education record
        if ($userEducation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'institution_name' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'field_of_study' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'is_completed' => 'boolean',
            'gpa_or_grade' => 'nullable|string|max:100',
            'language_of_instruction' => 'nullable|string|max:100',
            'courses_completed' => 'nullable|string|max:500',
            'honors_awards' => 'nullable|string|max:500',
            'degree_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'transcript' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Handle file uploads
        if ($request->hasFile('degree_certificate')) {
            // Delete old file
            if ($userEducation->degree_certificate_path) {
                Storage::disk('public')->delete($userEducation->degree_certificate_path);
            }
            $path = $request->file('degree_certificate')->store('education/certificates', 'public');
            $validated['degree_certificate_path'] = $path;
            unset($validated['degree_certificate']);
        }

        if ($request->hasFile('transcript')) {
            // Delete old file
            if ($userEducation->transcript_path) {
                Storage::disk('public')->delete($userEducation->transcript_path);
            }
            $path = $request->file('transcript')->store('education/transcripts', 'public');
            $validated['transcript_path'] = $path;
            unset($validated['transcript']);
        }

        $userEducation->update($validated);

        return response()->json($userEducation);
    }

    /**
     * Remove the specified education record.
     */
    public function destroy(UserEducation $userEducation)
    {
        // Ensure the user owns this education record
        if ($userEducation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Delete associated files
        if ($userEducation->degree_certificate_path) {
            Storage::disk('public')->delete($userEducation->degree_certificate_path);
        }
        if ($userEducation->transcript_path) {
            Storage::disk('public')->delete($userEducation->transcript_path);
        }

        $userEducation->delete();

        return response()->json(['message' => 'Education record deleted successfully']);
    }
}
