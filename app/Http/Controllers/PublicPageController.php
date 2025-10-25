<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\University;
use App\Models\Course;
use App\Models\JobCategory;
use App\Models\JobPosting;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route; // <-- Add this

class PublicPageController extends Controller
{
    /**
     * 🚀 Display the public welcome/home page.
     * THIS IS THE MISSING METHOD.
     */
    public function welcome(): Response
    {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
        ]);
    }

    /**
     * 🏛 Display the public university search page.
     */
    public function universities(): Response // Renamed from showUniversitySearch
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
    public function courses(): Response // Renamed from showCourseSearch
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
    public function universityDetail(University $university): Response // Renamed from showUniversityDetail
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
    public function courseDetail(Course $course): Response // Renamed from showCourseDetail
    {
        // Load related university and its country
        $courseData = $course->load('university.country');

        // Render the Course Detail page
        return Inertia::render('Public/CourseDetail', [
            'course' => $courseData,
        ]);
    }

    /**
     * 💼 Display the public job search page.
     */
    public function jobs(): Response // Renamed from showJobSearch
    {
        // Fetch data needed for search filters
        $categories = JobCategory::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $countries = Country::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Public/Jobs', [
            'categories' => $categories,
            'countries' => $countries,
        ]);
    }
    
    /**
     * 💼 Display details for a specific job posting.
     * (Assuming you have a JobPosting model and a 'Public/JobDetail' Vue page)
     */
    public function jobDetail(JobPosting $jobPosting): Response
    {
        $jobPosting->load(['jobCategory', 'country']);

        return Inertia::render('Public/JobDetail', [
            'job' => $jobPosting,
        ]);
    }
}