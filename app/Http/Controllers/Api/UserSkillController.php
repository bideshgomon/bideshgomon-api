<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSkillController extends Controller
{
    /**
     * Display a listing of the user's skills.
     */
    public function index()
    {
        $userSkills = Auth::user()->skills()
            ->with('users')
            ->withPivot('proficiency_level', 'years_of_experience', 'id')
            ->get()
            ->map(function ($skill) {
                return [
                    'id' => $skill->pivot->id,
                    'skill_id' => $skill->id,
                    'skill' => [
                        'id' => $skill->id,
                        'name' => $skill->name,
                        'category' => $skill->category,
                        'description' => $skill->description,
                    ],
                    'proficiency_level' => $skill->pivot->proficiency_level,
                    'years_of_experience' => $skill->pivot->years_of_experience,
                    'created_at' => $skill->pivot->created_at,
                ];
            });

        return response()->json($userSkills);
    }

    /**
     * Store a newly created skill for the user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'skill_id' => 'required|exists:skills,id',
            'proficiency_level' => 'required|in:Beginner,Intermediate,Advanced,Expert',
            'years_of_experience' => 'nullable|integer|min:0|max:50',
        ]);

        // Check if user already has this skill
        if (Auth::user()->skills()->where('skill_id', $validated['skill_id'])->exists()) {
            return response()->json(['message' => 'You already have this skill'], 422);
        }

        Auth::user()->skills()->attach($validated['skill_id'], [
            'proficiency_level' => $validated['proficiency_level'],
            'years_of_experience' => $validated['years_of_experience'] ?? 0,
        ]);

        return response()->json(['message' => 'Skill added successfully'], 201);
    }

    /**
     * Update the specified skill for the user.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'proficiency_level' => 'required|in:Beginner,Intermediate,Advanced,Expert',
            'years_of_experience' => 'nullable|integer|min:0|max:50',
        ]);

        // Find the pivot record
        $userSkill = \DB::table('user_skill')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$userSkill) {
            return response()->json(['message' => 'Skill not found'], 404);
        }

        \DB::table('user_skill')
            ->where('id', $id)
            ->update([
                'proficiency_level' => $validated['proficiency_level'],
                'years_of_experience' => $validated['years_of_experience'] ?? 0,
                'updated_at' => now(),
            ]);

        return response()->json(['message' => 'Skill updated successfully']);
    }

    /**
     * Remove the specified skill from the user.
     */
    public function destroy($id)
    {
        $userSkill = \DB::table('user_skill')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$userSkill) {
            return response()->json(['message' => 'Skill not found'], 404);
        }

        \DB::table('user_skill')->where('id', $id)->delete();

        return response()->json(['message' => 'Skill removed successfully']);
    }
}
