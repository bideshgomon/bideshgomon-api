<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // <-- Import the PDF Facade
use Illuminate\Support\Str; // <-- Import Str facade

class CvBuilderController extends Controller
{
    /**
     * Display the CV builder preview page (if you have one).
     */
    public function show()
    {
        // $user = Auth::user()->load(['profile', 'educations', 'experiences', 'skillSet', 'portfolios' /* , Add other needed relations */]);
        // return inertia('Profile/CvBuilder/Show', ['user' => $user]); // Example if using Inertia
        abort(501, 'Preview page not implemented yet.');
    }

    /**
     * Generate and download the user's CV as a PDF.
     */
    public function download(Request $request)
    {
        // 1. Get Authenticated User with all necessary profile data
        $user = Auth::user()->load([
            'profile', // Basic profile info
            'educations' => fn($q) => $q->orderBy('start_date', 'desc'), // Load and order
            'experiences' => fn($q) => $q->orderBy('start_date', 'desc'), // Load and order
            'skillSet', // Load skills via pivot table
            'portfolios',
            // --- Eager load ALL other relations needed for the CV template ---
            // 'languages.language', // Example: Load languages and the language name
            // 'licenses.licenseType', // Example
            // 'memberships',
            // 'technicalEducations',
        ]);

        // 2. Prepare data for the view
        $data = ['user' => $user];

        // 3. Load the Blade view with data and generate PDF
        $pdf = Pdf::loadView('cv.template', $data);

        // (Optional) Customize PDF options
        // $pdf->setPaper('a4', 'portrait');

        // 4. Generate a filename
        $filename = Str::slug($user->name . '-cv') . '.pdf';

        // 5. Stream the PDF download to the browser
        return $pdf->stream($filename);

        // --- OR ---
        // 5. Download the PDF directly
        // return $pdf->download($filename);
    }
}