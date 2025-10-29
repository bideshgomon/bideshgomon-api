<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Degree;
use App\Models\FieldOfStudy;
use App\Models\University;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{
    /**
     * Display the list of courses.
     * This single page will handle list, create, and edit.
     */
    public function index()
    {
        // We need all data for the modals to be available on this page.
        return Inertia::render('Admin/Courses/Index', [
            'courses' => Course::with('university', 'degree', 'fieldOfStudy')
                ->latest()
                ->paginate(10),
            'universities' => University::orderBy('name')->get(),
            'degrees' => Degree::orderBy('name')->get(),
            'fieldsOfStudy' => FieldOfStudy::orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * THIS METHOD IS NO LONGER NEEDED.
     */
    // public function create()
    // {
    //     return Inertia::render('Admin/Courses/Create', [
    //         'universities' => University::all(),
    //         'degrees' => Degree::all(),
    //         'fieldsOfStudy' => FieldOfStudy::all(),
    //     ]);
    // }

    /**
     * Show the form for editing the specified resource.
     * THIS METHOD IS NO LONGER NEEDED.
     */
    // public function edit(Course $course)
    // {
    //     return Inertia::render('Admin/Courses/Edit', [
    //         'course' => $course->load('university', 'degree', 'fieldOfStudy'),
    //         'universities' => University::all(),
    //         'degrees' => Degree::all(),
    //         'fieldsOfStudy' => FieldOfStudy::all(),
    //     ]);
    // }
}