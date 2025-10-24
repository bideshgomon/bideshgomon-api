<?php

namespace App\Http\Controllers\Profile; // Corrected Namespace based on typical structure

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Keep Auth facade for download method simplicity
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response as HttpResponse; // Use alias to avoid conflict

class CvBuilderController extends Controller
{
    /**
     * Display the CV builder preview page.
     */
    public function show(Request $request): Response
    {
        // Use $request->user() - loads authenticated user
        $user = $request->user()->load([
            'profile', // Load the profile relationship
            'educations' => fn ($query) => $query->orderBy('start_date', 'desc'),
            // Order experiences: current ones first, then by start date descending
            'experiences' => fn ($query) => $query->orderBy('is_current', 'desc')->orderBy('start_date', 'desc'),
            'skills' => fn ($query) => $query->orderBy('name'), // Load skills relationship
            'portfolios' => fn ($query) => $query->orderBy('created_at', 'desc'),
            'documents' => fn ($query) => $query->with('documentType') // Include documents if needed for preview
        ]);

        // Ensure the Inertia component path matches your file structure
        // Using 'Profile/CvBuilder' as it's more standard than 'Profile/Partials/CvBuilder'
        return Inertia::render('Profile/CvBuilder', [
            // Use 'userProfile' prop name consistent with previous Vue component examples
            'userProfile' => $user,
            // Skills are loaded via relationship, no need to pass separately if using the relationship correctly
        ]);
    }

    /**
     * Generate and download the user's CV as a PDF.
     */
    public function download(): HttpResponse // Correct return type hint
    {
        // Load the authenticated user with all necessary relationships for the PDF
        // Using Auth::user() here for simplicity as $request isn't needed otherwise
        $user = Auth::user()->load([
            'profile',
            'educations' => fn ($query) => $query->orderBy('start_date', 'desc'),
            'experiences' => fn ($query) => $query->orderBy('is_current', 'desc')->orderBy('start_date', 'desc'),
            'skills' => fn ($query) => $query->orderBy('name'),
            'portfolios' => fn ($query) => $query->orderBy('created_at', 'desc'),
            // Add 'documents' here if your PDF template requires them
        ]);

        // Prepare data for the Blade view
        $data = ['user' => $user]; // Pass the loaded user object to the view

        // Ensure 'resources/views/pdf/cv_template.blade.php' exists
        $pdf = Pdf::loadView('pdf.cv_template', $data);

        // Optional: Set paper size and orientation
        // $pdf->setPaper('A4', 'portrait');

        // Generate a dynamic filename (e.g., "john-doe-cv-YYYYMMDD.pdf")
        $filename = Str::slug($user->name) . '-cv-' . date('Ymd') . '.pdf';

        // Return the PDF as a download response
        return $pdf->download($filename);
    }
}