<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\JobPosting;
use App\Models\University; // <-- ADDED
use Illuminate\Http\Request;

class PublicSearchController extends Controller
{
    /**
     * Search for universities based on filters.
     */
    public function searchUniversities(Request $request)
    {
        $query = University::with('country') // Eager load country
            ->orderBy('name', 'asc');

        // Filter by Country ID
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->input('country_id'));
        }

        // Filter by Search Term (Name or City)
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('city', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Add more filters later (e.g., ranking, intake_months)

        return $query->paginate(12); // Use a different pagination size for public view
    }

    /**
     * Search for courses based on filters.
     */
    public function searchCourses(Request $request)
    {
        $query = Course::with('university.country') // Eager load university and its country
            ->orderBy('name', 'asc');

        // Filter by University ID
        if ($request->filled('university_id')) {
            $query->where('university_id', $request->input('university_id'));
        }

        // Filter by Degree Level
        if ($request->filled('degree_level')) {
            $query->where('degree_level', $request->input('degree_level'));
        }

        // Filter by Field of Study
        if ($request->filled('field_of_study')) {
            $query->where('field_of_study', $request->input('field_of_study'));
        }

        // Filter by Search Term (Course Name)
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'LIKE', "%{$searchTerm}%");
        }

        // Add more filters later (e.g., tuition fee range, duration)

        return $query->paginate(15);
    }

    /**
     * Show details for a specific university.
     */
    public function showUniversityDetail(University $university)
    {
        // Eager load country and the list of courses offered
        return $university->load('country', 'courses');
    }

    /**
     * Show details for a specific course.
     */
    public function showCourseDetail(Course $course)
    {
        // Eager load the university and the university's country
        return $course->load('university.country');
    }

    /**
     * Search for job postings based on filters.
     */
    public function searchJobPostings(Request $request) // <-- ADDED THIS METHOD
    {
        $query = JobPosting::with(['jobCategory', 'country']) // Eager load relationships
            ->where('status', 'active') // Only show active jobs
            ->orderBy('is_featured', 'desc') // Show featured first
            ->latest(); // Then by newest

        // Filter by Job Category ID
        if ($request->filled('job_category_id')) {
            $query->where('job_category_id', $request->input('job_category_id'));
        }

        // Filter by Country ID
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->input('country_id'));
        }

        // Filter by Search Term (Title, Company, Skills)
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('company_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('skills_required', 'LIKE', "%{$searchTerm}%"); // Basic skill search
            });
        }

        // Add more filters later (e.g., employment_type, salary range)

        return $query->paginate(15);
    }
}
