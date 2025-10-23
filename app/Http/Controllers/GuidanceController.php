<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class GuidanceController extends Controller
{
    /**
     * Display the user's guidance dashboard.
     */
    public function dashboard(Request $request): Response
    {
        $user = Auth::user();

        // Eager load all profile relationships in one query
        $user->load(
            'educations', 
            'experiences', 
            'portfolios', 
            'documents'
        );

        // Define the "completeness" criteria
        $completeness = [
            'personal_info' => $user->name && $user->email,
            'education' => $user->educations->isNotEmpty(),
            'experience' => $user->experiences->isNotEmpty(),
            'skills' => !empty($user->skills),
            'documents' => $user->documents->isNotEmpty(),
            'passport' => $user->documents->contains(
                fn($doc) => $doc->document_type_id === 1 // Assuming 'Passport' is ID 1
            ),
        ];

        // Calculate the score
        $score = (count(array_filter($completeness)) / count($completeness)) * 100;

        return Inertia::render('Guidance/Dashboard', [
            'userProfile' => $user,
            'completeness' => $completeness,
            'profileScore' => round($score),
        ]);
    }
}