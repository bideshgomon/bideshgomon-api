<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

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
            'skills' => fn ($query) => $query->orderBy('name'),
            'portfolios' => fn ($query) => $query->orderBy('created_at', 'desc'),
            'travelHistories.country',
            'languages' => fn ($query) => $query->orderBy('language'),
            'licenses' => fn ($query) => $query->orderBy('issue_date', 'desc'),
            'technicalEducations' => fn ($query) => $query->orderBy('start_date', 'desc'),
            'memberships' => fn ($query) => $query->orderBy('start_date', 'desc'), // <-- NEWLY ADDED
        ]);

        return Inertia::render('Profile/CvBuilder', [
            'user' => $user,
        ]);
    }

    /**
     * Generate and download the user's CV as a PDF.
     */
    public function download(): HttpResponse
    {
        $user = Auth::user()->load([
            'profile',
            'educations' => fn ($query) => $query->orderBy('start_date', 'desc'),
            'experiences' => fn ($query) => $query->orderBy('is_current', 'desc')->orderBy('start_date', 'desc'),
            'skills' => fn ($query) => $query->orderBy('name'),
            'portfolios' => fn ($query) => $query->orderBy('created_at', 'desc'),
            'travelHistories.country',
            'languages' => fn ($query) => $query->orderBy('language'),
            'licenses' => fn ($query) => $query->orderBy('issue_date', 'desc'),
            'technicalEducations' => fn ($query) => $query->orderBy('start_date', 'desc'),
            'memberships' => fn ($query) => $query->orderBy('start_date', 'desc'), // <-- NEWLY ADDED
        ]);

        $data = ['user' => $user];
        $pdf = Pdf::loadView('pdf.cv_template', $data);
        $filename = Str::slug($user->name).'-cv-'.date('Ymd').'.pdf';

        return $pdf->download($filename);
    }
}
