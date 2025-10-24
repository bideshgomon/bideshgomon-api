<?php

namespace App\Http\Controllers\Profile; // Correct namespace

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

// --- Import the base Gemini client ---
use Gemini\Client; // Make sure this line is here

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        // ... (index method code remains the same) ...
        $user = $request->user();
        $profileData = [
            'personal_info' => $user->profile?->bio && $user->profile?->address && $user->profile?->phone,
            'education' => $user->educations()->exists(),
            'experience' => $user->experiences()->exists(),
            'skills' => $user->skillSet()->exists(),
            'documents' => $user->documents()->exists(),
        ];
        $completedSections = count(array_filter($profileData));
        $totalSections = count($profileData);
        $completeness = $totalSections > 0 ? ($completedSections / $totalSections) * 100 : 0;
        $recommendations = [];
        if (!$profileData['personal_info']) { $recommendations[] = ['text' => 'Complete your personal information (bio, phone, address).', 'route' => 'profile.edit']; }
        if (!$profileData['education']) { $recommendations[] = ['text' => 'Add your educational background.', 'route' => 'profile.edit']; }
        if (!$profileData['experience']) { $recommendations[] = ['text' => 'Add your work experience.', 'route' => 'profile.edit']; }
        if (!$profileData['skills']) { $recommendations[] = ['text' => 'Add your skills.', 'route' => 'profile.edit']; }
        if (!$profileData['documents']) { $recommendations[] = ['text' => 'Upload supporting documents (CV, passport).', 'route' => 'profile.edit']; }
        return Inertia::render('Dashboard', [
            'completeness' => $completeness,
            'recommendations' => $recommendations,
        ]);
    }

    // --- USING DEPENDENCY INJECTION ---
    // Type-hint the Gemini\Client class, Laravel will inject it
    public function testGemini(Client $geminiClient): JsonResponse
    {
        try {
            // --- Use the injected $geminiClient instance ---
            // Let's explicitly choose the model to be safe
            $modelName = 'gemini-1.0-pro'; 
            $result = $geminiClient
                ->geminiPro() // Keep this if needed by package structure
                ->generateContent('Write a short welcome message for a user visiting their dashboard.');
             // Alternative:
             // $result = $geminiClient
             //     ->generativeModel($modelName) 
             //     ->generateContent('Write a short welcome message...');

            return response()->json([
                'success' => true,
                'response' => $result->text(),
            ]);

        } catch (\Exception $e) {
            Log::error('Gemini API Test Failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to connect to Gemini API: ' . $e->getMessage(),
            ], 500);
        }
    }
    // ------------------------------------------
}