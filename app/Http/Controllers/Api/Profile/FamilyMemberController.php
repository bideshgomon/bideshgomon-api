<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FamilyMemberController extends Controller
{
    public function index()
    {
        $familyMembers = Auth::user()->familyMembers()->orderBy('created_at', 'desc')->get();

        return response()->json($familyMembers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'relationship' => 'required|string|in:spouse,child,parent,sibling,grandparent,grandchild,uncle,aunt,cousin,nephew,niece,in-law,other',
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'place_of_birth' => 'nullable|string|max:255',
            'gender' => 'required|string|in:male,female,other',
            'nationality' => 'required|string|max:100',
            'country_of_residence' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:255',
            'employer_name' => 'nullable|string|max:255',
            'annual_income' => 'nullable|numeric|min:0',
            'income_currency' => 'nullable|string|max:3',
            'education_level' => 'nullable|string|in:none,primary,secondary,higher_secondary,bachelors,masters,doctorate',
            'marital_status' => 'nullable|string|in:single,married,divorced,widowed,separated',
            'is_dependent' => 'boolean',
            'lives_with_user' => 'boolean',
            'will_accompany' => 'boolean',
            'will_accompany_travel' => 'boolean',
            'passport_number' => 'nullable|string|max:50',
            'immigration_status' => 'nullable|string|in:citizen,permanent_resident,temporary_resident,student,refugee,other,not_applicable',
            'is_deceased' => 'boolean',
            'deceased_date' => 'nullable|date|before_or_equal:today',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'emergency_contact' => 'boolean',
            'relationship_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Handle file upload
        if ($request->hasFile('relationship_proof')) {
            $file = $request->file('relationship_proof');
            $path = $file->store('family-members/proofs', 'public');
            $validated['relationship_proof_path'] = $path;
            $validated['relationship_proof_uploaded'] = true;
        } else {
            $validated['relationship_proof_uploaded'] = false;
        }

    $familyMember = Auth::user()->familyMembers()->create($validated);

        return response()->json($familyMember, 201);
    }

    public function update(Request $request, FamilyMember $familyMember)
    {
        if ($familyMember->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'relationship' => 'required|string|in:spouse,child,parent,sibling,grandparent,grandchild,uncle,aunt,cousin,nephew,niece,in-law,other',
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'place_of_birth' => 'nullable|string|max:255',
            'gender' => 'required|string|in:male,female,other',
            'nationality' => 'required|string|max:100',
            'country_of_residence' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:255',
            'employer_name' => 'nullable|string|max:255',
            'annual_income' => 'nullable|numeric|min:0',
            'income_currency' => 'nullable|string|max:3',
            'education_level' => 'nullable|string|in:none,primary,secondary,higher_secondary,bachelors,masters,doctorate',
            'marital_status' => 'nullable|string|in:single,married,divorced,widowed,separated',
            'is_dependent' => 'boolean',
            'lives_with_user' => 'boolean',
            'will_accompany' => 'boolean',
            'will_accompany_travel' => 'boolean',
            'passport_number' => 'nullable|string|max:50',
            'immigration_status' => 'nullable|string|in:citizen,permanent_resident,temporary_resident,student,refugee,other,not_applicable',
            'is_deceased' => 'boolean',
            'deceased_date' => 'nullable|date|before_or_equal:today',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'emergency_contact' => 'boolean',
            'relationship_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

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

        $familyMember->update($validated);

        return response()->json($familyMember);
    }

    public function destroy(FamilyMember $familyMember)
    {
        if ($familyMember->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete relationship proof file if exists
        if ($familyMember->relationship_proof_path) {
            Storage::disk('public')->delete($familyMember->relationship_proof_path);
        }

        $familyMember->delete();

        return response()->json(['message' => 'Family member deleted successfully']);
    }
}
