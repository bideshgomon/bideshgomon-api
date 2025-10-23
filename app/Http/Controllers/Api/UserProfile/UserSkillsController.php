<?php

namespace App\Http\Controllers\Api\UserProfile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserSkillsController extends Controller
{
    /**
     * Store or update the user's skills.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'skills' => 'present|array', // Ensures 'skills' is an array, even if empty
            'skills.*' => 'string|max:100', // Validates each item in the array
        ]);

        $user = $request->user();
        
        // Update the skills on the user model directly
        $user->update([
            'skills' => $validated['skills'],
        ]);

        return response()->json($user->skills);
    }
}