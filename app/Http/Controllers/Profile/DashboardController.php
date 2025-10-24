<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth; // Not strictly necessary if using $request->user()

class DashboardController extends Controller
{
    /**
     * Display the user's dashboard with profile completeness.
     */
    public function index(Request $request): Response
    {
        // Load the user and their related profile data
        $user = $request->user()->load([
            'profile',
            'educations',
            'experiences',
            'skills',
            'documents'
        ]);

        // Define the sections for completeness check
        // Line 28: Correctly checks string attributes using boolean logic & nullsafe operator
        $profileData = [
            'personal_info' => !empty($user->profile?->bio) && !empty($user->profile?->address) && !empty($user->profile?->phone),
            // Lines 29-32: Correctly use isNotEmpty() on collections
            'education' => $user->educations->isNotEmpty(),
            'experience' => $user->experiences->isNotEmpty(),
            'skills' => $user->skills->isNotEmpty(),
            'documents' => $user->documents->isNotEmpty(),
        ];

        // Line 34: Correctly counts elements - cannot cause the reported error
        $completedSections = count(array_filter($profileData));
        $totalSections = count($profileData);
        $completeness = $totalSections > 0 ? ($completedSections / $totalSections) * 100 : 0;

        // Generate dynamic recommendations based on the checks
        $recommendations = [];
        if (!$profileData['personal_info']) {
            $recommendations[] = [
                'text' => 'Complete your personal information (bio, phone, address).',
                'route' => 'profile.edit' // Points to the profile page
            ];
        }
        if (!$profileData['education']) {
            $recommendations[] = [
                'text' => 'Add your educational background.',
                'route' => 'profile.edit'
            ];
        }
        if (!$profileData['experience']) {
            $recommendations[] = [
                'text' => 'Add your work experience.',
                'route' => 'profile.edit'
            ];
        }
        if (!$profileData['skills']) {
            $recommendations[] = [
                'text' => 'Add your skills.',
                'route' => 'profile.edit'
            ];
        }
        if (!$profileData['documents']) {
            $recommendations[] = [
                'text' => 'Upload supporting documents (CV, passport).',
                'route' => 'profile.edit'
            ];
        }

        // Render the Inertia component
        return Inertia::render('Dashboard', [
            'completeness' => round($completeness), // Round the percentage
            'recommendations' => $recommendations,
        ]);
    }
}