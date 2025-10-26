<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TouristVisa; // Import the model
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TouristVisaPageController extends Controller
{
    /**
     * Display a listing of the tourist visa applications.
     */
    public function index(): Response
    {
        // We'll fetch data via API, so just render the page component
        return Inertia::render('Admin/TouristVisas/Index');
    }

    /**
     * Display the specified tourist visa application details.
     */
    public function show(TouristVisa $touristVisa): Response
    {
        // Eager load related data needed for the detail view
        $touristVisa->load([
            'user:id,name,email', // Load basic user info
            'destinationCountry:id,name', // Load destination country
            'documents.documentType:id,name', // Load documents and their types
            'documents.userDocument:id,file_path,file_name' // Load linked uploaded document info
        ]);

        return Inertia::render('Admin/TouristVisas/Show', [
            'visaApplication' => $touristVisa
        ]);
    }

    // We might not need create/edit pages initially if admins primarily manage status/docs
}