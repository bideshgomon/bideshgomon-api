<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user()->load([
            'profile',
            'educations',
            'experiences',
            'documents.documentType',
            'skillSet' // *** <-- FIX #1: Use the correct relationship name 'skillSet' ***
        ]);

        // Define the sections for completeness check
        $profileData = [
            'personal_info' => $user->profile?->bio && $user->profile?->address && $user->profile?->phone,
            'education' => $user->educations->isNotEmpty(),
            'experience' => $user->experiences->isNotEmpty(),
            'skills' => $user->skillSet->isNotEmpty(), // *** <-- FIX #2: Check 'skillSet' collection ***
            'documents' => $user->documents->isNotEmpty(),
            'passport' => $user->documents->contains(fn($doc) => str_contains(strtolower($doc->documentType->name), 'passport')),
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
        if (!$profileData['passport']) { $recommendations[] = ['text' => 'Upload your Passport (Crucial!).', 'route' => 'profile.edit']; }

        // Render the correct Vue component
        return Inertia::render('Guidance/Dashboard', [
            'userProfile' => $user,
            'completeness' => $profileData,
            'profileScore' => round($completeness),
            'recommendations' => $recommendations,
        ]);
    }
}