<?php

namespace App\Http\Controllers\Api\UserProfile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Skill;

class UserSkillController extends Controller
{
    /**
     * Get all skills associated with the user.
     */
    public function index(Request $request)
    {
        return $request->user()->skills()->get();
    }

    /**
     * Attach a new skill to the user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'skill_id' => 'required|exists:skills,id',
        ]);

        $user = $request->user();
        
        // Use syncWithoutDetaching to add the skill
        // This avoids adding duplicates
        $user->skills()->syncWithoutDetaching([$validated['skill_id']]);

        return $user->skills()->get();
    }

    /**
     * Detach a skill from the user.
     */
    public function destroy(Request $request, string $skill_id)
    {
        // Check if the skill_id is valid before detaching
        if (!Skill::find($skill_id)) {
             return response()->json(['message' => 'Skill not found'], 404);
        }

        $user = $request->user();
        
        // Detach the skill
        $user->skills()->detach($skill_id);

        return response()->json(null, 204);
    }
}