<?php

namespace App\Http\Controllers\Profile; // Assuming namespace based on context

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $user = $request->user()->load([
            'profile', 
            'educations' => fn ($query) => $query->orderBy('start_date', 'desc'),
            'experiences' => fn ($query) => $query->orderBy('is_current', 'desc')->orderBy('start_date', 'desc'),
            'skills' => fn ($query) => $query->orderBy('name'), // <-- FIXED (was skillSet)
            'portfolios' => fn ($query) => $query->orderBy('created_at', 'desc'),
            'documents' => fn ($query) => $query->with('documentType')
        ]);

        return Inertia::render('Profile/CvBuilder', [
            // Use 'userProfile' to be consistent with our other pages
            'userProfile' => $user, 
            'skills' => $user->skills, // Pass skills explicitly
        ]);
    }

    /**
     * Generate and download the user's CV as a PDF.
     */
    public function download(Request $request): HttpResponse
    {
        $user = Auth::user()->load([
            'profile',
            'educations' => fn ($query) => $query->orderBy('start_date', 'desc'),
            'experiences' => fn ($query) => $query->orderBy('is_current', 'desc')->orderBy('start_date', 'desc'),
            'skills' => fn ($query) => $query->orderBy('name'), // <-- FIXED (was skillSet)
            'portfolios' => fn ($query) => $query->orderBy('created_at', 'desc'),
        ]);

        // Pass data to the Blade view
        $data = [
            'user' => $user,
            'skills' => $user->skills, // Pass skills explicitly
        ]; 
        
        // Load the Blade view (we'll create this next)
        $pdf = Pdf::loadView('pdf.cv_template', $data);

        $filename = Str::slug($user->name) . '-cv-' . date('Ymd') . '.pdf';

        // Return the PDF as a download
        return $pdf->download($filename);
    }
}