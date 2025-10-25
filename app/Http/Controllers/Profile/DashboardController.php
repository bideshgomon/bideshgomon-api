<?php

namespace App\Http\Controllers\Profile; // Correct namespace

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse; // Keep for potential future JSON responses
use Illuminate\Support\Facades\Log; // Keep for logging

// --- REMOVED ALL Gemini-related imports ---

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        // Get the authenticated user
        $user = $request->user();

        // Check profile completeness
        $profileData = [
            'personal_info' => $user->profile?->bio && $user->profile?->address && $user->profile?->phone,
            'education' => $user->educations()->exists(),
            'experience' => $user->experiences()->exists(),
            'skills' => $user->skillSet()->exists(), // Use the correct relationship name
            'documents' => $user->documents()->exists(),
        ];
        $completedSections = count(array_filter($profileData));
        $totalSections = count($profileData);
        $completeness = $totalSections > 0 ? ($completedSections / $totalSections) * 100 : 0;

        // Generate recommendations
        $recommendations = [];
        if (!$profileData['personal_info']) { $recommendations[] = ['text' => 'Complete your personal information (bio, phone, address).', 'route' => 'profile.edit']; }
        if (!$profileData['education']) { $recommendations[] = ['text' => 'Add your educational background.', 'route' => 'profile.edit']; }
        if (!$profileData['experience']) { $recommendations[] = ['text' => 'Add your work experience.', 'route' => 'profile.edit']; }
        if (!$profileData['skills']) { $recommendations[] = ['text' => 'Add your skills.', 'route' => 'profile.edit']; }
        if (!$profileData['documents']) { $recommendations[] = ['text' => 'Upload supporting documents (CV, passport).', 'route' => 'profile.edit']; }

        // Render the dashboard view
        return Inertia::render('Dashboard', [
            'completeness' => $completeness,
            'recommendations' => $recommendations,
        ]);
    }

    // --- REMOVED the testGemini() method ---

}