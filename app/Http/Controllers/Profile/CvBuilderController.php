<?php

namespace App\Http\Controllers\Profile; // Assuming namespace based on context

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
// If you need PDF generation: use Barryvdh\DomPDF\Facade\Pdf;

class CvBuilderController extends Controller
{
    /**
     * Display the CV builder page.
     */
    public function show(Request $request): Response
    {
        $user = $request->user();

        // ✅ [FIX] Use the correct relationship name 'userProfile'
        // Eager load all necessary relationships for the CV builder page
        $user->load([
            'userProfile', // Correct relationship name
            'educations' => fn ($query) => $query->orderBy('end_date', 'desc'),
            'experiences' => fn ($query) => $query->orderBy('end_date', 'desc'), // Assumes 'experiences' now points to UserWorkExperience
            'skillSet', // Assumes 'skillSet' is the correct name for the skills relationship
            'portfolios' => fn ($query) => $query->orderBy('created_at', 'desc'),
            'documents' // Load documents if needed on this page
            // Add other relationships like languages, licenses, etc. if needed
        ]);

        return Inertia::render('Profile/CvBuilder', [ // Assuming this is your Inertia component name
            'user' => $user, // Pass the user model with all loaded relationships
        ]);
    }

    /**
     * Download the user's CV as a PDF (Example Implementation).
     */
    public function download(Request $request)
    {
        $user = $request->user();
        $user->load([
            'userProfile',
            'educations' => fn ($query) => $query->orderBy('end_date', 'desc'),
            'experiences' => fn ($query) => $query->orderBy('end_date', 'desc'),
            'skillSet',
            'portfolios' => fn ($query) => $query->orderBy('created_at', 'desc'),
             // Add other relationships as needed for the PDF
        ]);

        // --- Example using DomPDF ---
        // 1. Create a Blade view (e.g., resources/views/cv/template.blade.php)
        // 2. Pass the $user data to the view
        // 3. Style the Blade view for PDF output

        /*
        $pdf = Pdf::loadView('cv.template', ['user' => $user]);
        return $pdf->download($user->name . '_CV.pdf');
        */

        // --- Placeholder Response ---
        // Replace this with your actual PDF generation logic
        return response()->json([
            'message' => 'PDF download endpoint not fully implemented.',
            'user_data' => $user // For testing, you can see the data structure
        ]);
    }
}