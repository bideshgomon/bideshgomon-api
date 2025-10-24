<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\University;
use App\Models\Course;
use App\Models\JobCategory; // <-- Included from merge
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Http; // ✅ For potential API calls (future use)

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

    /**
     * 🏫 Display details for a specific university.
     */
    public function showUniversityDetail(University $university): Response
    {
        // Load related data for full details
        $universityData = $university->load('country', 'courses');

        // Render the University Detail page
        return Inertia::render('Public/UniversityDetail', [
            'university' => $universityData,
        ]);
    }

    /**
     * 📘 Display details for a specific course.
     */
    public function showCourseDetail(Course $course): Response
    {
        // Load related university and its country
        $courseData = $course->load('university.country');

        // Render the Course Detail page
        return Inertia::render('Public/CourseDetail', [
            'course' => $courseData,
        ]);
    }

    /**
     * Display the public job search page.
     */
    public function showJobSearch(): Response // <-- Included from merge
    {
        // Fetch data needed for search filters
        $categories = JobCategory::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $countries = Country::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Public/Jobs', [
            'categories' => $categories,
            'countries' => $countries,
        ]);
    }
}