<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\University;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;

class PublicPageController extends Controller
{
    /**
     * Display the welcome page.
     */
    public function welcome(Request $request)
    {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            // Removed Laravel/PHP versions as they are not used in the new design
        ]);
    }

    /**
     * Display the public university search page.
     */
    public function searchUniversities(Request $request)
    {
        // Fetch initial set of universities for page load
        $universities = University::with(['country', 'city']) // Eager load relationships
            ->orderBy('name', 'asc')
            ->paginate(12); // Paginate results (e.g., 12 per page)

        return Inertia::render('Public/Universities', [
            'initialUniversities' => $universities,
            // We can add filters like countries later if needed
            // 'countries' => \App\Models\Country::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Display the public course search page.
     */
    public function searchCourses(Request $request)
    {
         // Fetch initial set of courses for page load
         $courses = Course::with(['university.country', 'degreeLevel']) // Eager load relationships
            ->orderBy('name', 'asc')
            ->paginate(12);

        return Inertia::render('Public/Courses', [
             'initialCourses' => $courses,
             // Add filters later
        ]);
    }

    /**
     * Display university details (Example - needs more work)
     */
    public function showUniversity(University $university)
    {
        $university->load(['country', 'city', 'courses.degreeLevel']); // Load necessary data
        // Render a detail view component (we'll create this later)
        return Inertia::render('Public/UniversityDetail', [
            'university' => $university,
        ]);
    }

     /**
     * Display course details (Example - needs more work)
     */
    public function showCourse(Course $course)
    {
        $course->load(['university.country', 'degreeLevel']); // Load necessary data
        // Render a detail view component (we'll create this later)
        return Inertia::render('Public/CourseDetail', [
            'course' => $course,
        ]);
    }
}