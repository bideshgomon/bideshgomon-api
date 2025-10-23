<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\University;
use App\Models\Course;
use Inertia\Inertia;
use Inertia\Response;

class PublicPageController extends Controller
{
    /**
     * 🏛 Display the public university search page.
     */
    public function showUniversitySearch(): Response
    {
        // Fetch countries for filter dropdown
        $countries = Country::orderBy('name')->get(['id', 'name']);

        // Render the public-facing University Search page
        return Inertia::render('Public/Universities', [
            'countries' => $countries,
        ]);
    }

    /**
     * 🎓 Display the public course search page.
     */
    public function showCourseSearch(): Response
    {
        // Fetch all universities for filters
        $universities = University::orderBy('name')->get(['id', 'name']);

        // Get distinct filter options for degree levels & fields
        $degreeLevels = Course::distinct()->pluck('degree_level')->filter()->sort()->values();
        $fieldsOfStudy = Course::distinct()->pluck('field_of_study')->filter()->sort()->values();

        // Render the public-facing Course Search page
        return Inertia::render('Public/Courses', [
            'universities' => $universities,
            'degreeLevels' => $degreeLevels,
            'fieldsOfStudy' => $fieldsOfStudy,
        ]);
    }
}
