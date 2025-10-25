<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\University;
use Inertia\Inertia;
use Inertia\Response;

class CoursePageController extends Controller
{
    /**
     * Display the list of courses.
     */
    public function index(): Response
    {
        $courses = Course::with('university')
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Courses/Index', [
            'courses' => $courses,
        ]);
    }

    /**
     * Show the form for creating a new course.
     */
    public function create(): Response
    {
        $universities = University::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/Courses/Create', [
            'universities' => $universities,
        ]);
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course): Response
    {
        $universities = University::orderBy('name')->get(['id', 'name']);

        // Eager load the university relationship for the course being edited
        $course->load('university');

        return Inertia::render('Admin/Courses/Edit', [
            'course' => $course,
            'universities' => $universities,
        ]);
    }
}
