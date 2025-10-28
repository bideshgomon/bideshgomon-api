<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log; // Keep Log import

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        // Load relationships with correct names
        $user = $request->user()->load([
            'profile',
            'educations',
            'workExperiences', // Corrected relationship name
            'documents.documentType',
            'skills' // Assuming 'skills' is the correct relationship
        ]);

        // Define the sections for completeness check
        $profileDataChecks = [ // Renamed variable for clarity
            'personal_info' => $user->profile?->bio && $user->profile?->address && $user->profile?->phone,
            'education' => $user->educations->isNotEmpty(),
            'experience' => $user->workExperiences->isNotEmpty(),
            'skills' => $user->skills->isNotEmpty(),
            'documents' => $user->documents->isNotEmpty(),
            // Corrected check for passport using optional chaining and null coalescing
            'passport' => $user->documents->contains(fn($doc) => str_contains(strtolower(optional($doc->documentType)->name ?? ''), 'passport')),
        ];

        $completedSections = count(array_filter($profileDataChecks));
        $totalSections = count($profileDataChecks);
        $completenessPercentage = $totalSections > 0 ? ($completedSections / $totalSections) * 100 : 0;

        // Generate recommendations
        $recommendations = [];
        if (!$profileDataChecks['personal_info']) { $recommendations[] = ['text' => 'Complete your personal information (bio, phone, address).', 'route' => 'profile.edit']; }
        if (!$profileDataChecks['education']) { $recommendations[] = ['text' => 'Add your educational background.', 'route' => 'profile.edit']; }
        if (!$profileDataChecks['experience']) { $recommendations[] = ['text' => 'Add your work experience.', 'route' => 'profile.edit']; }
        if (!$profileDataChecks['skills']) { $recommendations[] = ['text' => 'Add your skills.', 'route' => 'profile.edit']; }
        if (!$profileDataChecks['passport']) { $recommendations[] = ['text' => 'Upload your Passport (Crucial!).', 'route' => 'profile.edit']; }

        // Render the Dashboard view component with corrected props
        return Inertia::render('Dashboard', [ // Ensure 'Dashboard.vue' exists
            'userProfile' => $user,
            'completenessChecks' => $profileDataChecks,      // <-- Pass the boolean array here
            'profileScore' => round($completenessPercentage), // <-- Pass the calculated number here
            'recommendations' => $recommendations,
        ]);
    }
}