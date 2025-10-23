<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function index(Request $request)
    {
        // Allow filtering by university_id
        $query = Course::with('university');

        if ($request->has('university_id')) {
            $query->where('university_id', $request->university_id);
        }

        return $query->latest()->paginate(10);
    }

    /**
     * Store a new course.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'university_id' => 'required|exists:universities,id',
            'name' => 'required|string|max:255',
            'degree_level' => 'required|string|max:100',
            'field_of_study' => 'required|string|max:100',
            'tuition_fee' => 'nullable|numeric|min:0',
            'duration_years' => 'nullable|numeric|min:0.5|max:10',
            'description' => 'nullable|string',
            'application_deadline' => 'nullable|date',
        ]);

        $course = Course::create($validated);

        return response()->json($course->load('university'), 201);
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course)
    {
        return $course->load('university');
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'university_id' => 'required|exists:universities,id',
            'name' => 'required|string|max:255',
            'degree_level' => 'required|string|max:100',
            'field_of_study' => 'required|string|max:100',
            'tuition_fee' => 'nullable|numeric|min:0',
            'duration_years' => 'nullable|numeric|min:0.5|max:10',
            'description' => 'nullable|string',
            'application_deadline' => 'nullable|date',
        ]);

        $course->update($validated);

        return response()->json($course->load('university'), 200);
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Course $course)
    {
        $course->delete();

        return response()->json(null, 204); // No Content
    }
}
