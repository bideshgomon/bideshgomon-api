<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class FamilyMemberController extends Controller
{
    /**
     * Display a listing of family members for the authenticated user.
     */
    public function index(Request $request)
    {
        $familyMembers = FamilyMember::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        // Return JSON for AJAX requests
        if ($request->wantsJson()) {
            return response()->json($familyMembers);
        }

        // Return Inertia page for direct navigation
        return Inertia::render('Profile/FamilyMembers', [
            'familyMembers' => $familyMembers
        ]);
    }

    /**
     * Store a newly created family member in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'relationship' => 'required|string|in:spouse,child,parent,sibling,grandparent,grandchild,uncle,aunt,cousin,nephew,niece,in-law,other',
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'place_of_birth' => 'nullable|string|max:255',
            'gender' => 'required|string|in:male,female,other',
            'nationality' => 'required|string|max:100',
            'current_country' => 'nullable|string|max:100',
            'current_city' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:255',
            'employer' => 'nullable|string|max:255',
            'annual_income_bdt' => 'nullable|numeric|min:0',
            'education_level' => 'nullable|string|in:none,primary,secondary,higher_secondary,bachelors,masters,doctorate',
            'marital_status' => 'nullable|string|in:single,married,divorced,widowed,separated',
            'is_dependent' => 'boolean',
            'lives_with_user' => 'boolean',
            'will_accompany_travel' => 'boolean',
            'passport_number' => 'nullable|string|max:50',
            'visa_status' => 'nullable|string|in:none,tourist,student,work,permanent_resident,citizen',
            'deceased' => 'boolean',
            'deceased_date' => 'nullable|date|before_or_equal:today',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'emergency_contact' => 'boolean',
            'relationship_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'notes' => 'nullable|string|max:1000'
        ]);

        // Validation: If dependent, should not have income
        if ($request->input('is_dependent') && $request->filled('annual_income_bdt') && $request->input('annual_income_bdt') > 0) {
            return response()->json([
                'message' => 'A dependent cannot have an annual income.',
                'errors' => ['annual_income_bdt' => ['Dependent members should not have income listed.']]
            ], 422);
        }

        // Validation: If deceased, require deceased date
        if ($request->input('deceased') && !$request->filled('deceased_date')) {
            return response()->json([
                'message' => 'Deceased date is required when marking as deceased.',
                'errors' => ['deceased_date' => ['Please provide the date of death.']]
            ], 422);
        }

        // Handle file upload
        if ($request->hasFile('relationship_proof')) {
            $file = $request->file('relationship_proof');
            $path = $file->store('family-members/proofs', 'public');
            $validated['relationship_proof_path'] = $path;
            $validated['relationship_proof_uploaded'] = true;
        } else {
            $validated['relationship_proof_uploaded'] = false;
        }

        // Add user_id
        $validated['user_id'] = auth()->id();

        // Create the family member
        $familyMember = FamilyMember::create($validated);

        return response()->json([
            'message' => 'Family member added successfully.',
            'family_member' => $familyMember
        ], 201);
    }

    /**
     * Update the specified family member in storage.
     */
    public function update(Request $request, $id)
    {
        $familyMember = FamilyMember::where('user_id', auth()->id())
            ->findOrFail($id);

        $validated = $request->validate([
            'relationship' => 'required|string|in:spouse,child,parent,sibling,grandparent,grandchild,uncle,aunt,cousin,nephew,niece,in-law,other',
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'place_of_birth' => 'nullable|string|max:255',
            'gender' => 'required|string|in:male,female,other',
            'nationality' => 'required|string|max:100',
            'current_country' => 'nullable|string|max:100',
            'current_city' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:255',
            'employer' => 'nullable|string|max:255',
            'annual_income_bdt' => 'nullable|numeric|min:0',
            'education_level' => 'nullable|string|in:none,primary,secondary,higher_secondary,bachelors,masters,doctorate',
            'marital_status' => 'nullable|string|in:single,married,divorced,widowed,separated',
            'is_dependent' => 'boolean',
            'lives_with_user' => 'boolean',
            'will_accompany_travel' => 'boolean',
            'passport_number' => 'nullable|string|max:50',
            'visa_status' => 'nullable|string|in:none,tourist,student,work,permanent_resident,citizen',
            'deceased' => 'boolean',
            'deceased_date' => 'nullable|date|before_or_equal:today',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'emergency_contact' => 'boolean',
            'relationship_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'notes' => 'nullable|string|max:1000'
        ]);

        // Validation: If dependent, should not have income
        if ($request->input('is_dependent') && $request->filled('annual_income_bdt') && $request->input('annual_income_bdt') > 0) {
            return response()->json([
                'message' => 'A dependent cannot have an annual income.',
                'errors' => ['annual_income_bdt' => ['Dependent members should not have income listed.']]
            ], 422);
        }

        // Validation: If deceased, require deceased date
        if ($request->input('deceased') && !$request->filled('deceased_date')) {
            return response()->json([
                'message' => 'Deceased date is required when marking as deceased.',
                'errors' => ['deceased_date' => ['Please provide the date of death.']]
            ], 422);
        }

        // Handle file upload
        if ($request->hasFile('relationship_proof')) {
            // Delete old file if exists
            if ($familyMember->relationship_proof_path) {
                Storage::disk('public')->delete($familyMember->relationship_proof_path);
            }

            $file = $request->file('relationship_proof');
            $path = $file->store('family-members/proofs', 'public');
            $validated['relationship_proof_path'] = $path;
            $validated['relationship_proof_uploaded'] = true;
        }

        // Update the family member
        $familyMember->update($validated);

        return response()->json([
            'message' => 'Family member updated successfully.',
            'family_member' => $familyMember
        ]);
    }

    /**
     * Remove the specified family member from storage.
     */
    public function destroy($id)
    {
        $familyMember = FamilyMember::where('user_id', auth()->id())
            ->findOrFail($id);

        // Delete relationship proof file if exists
        if ($familyMember->relationship_proof_path) {
            Storage::disk('public')->delete($familyMember->relationship_proof_path);
        }

        $familyMember->delete();

        return response()->json([
            'message' => 'Family member deleted successfully.'
        ]);
    }
}
