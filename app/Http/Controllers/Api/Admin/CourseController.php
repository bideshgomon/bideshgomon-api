<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

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

        $courses = $query->latest()->paginate(10);

        // ✅ Return an Inertia view, not JSON
        return Inertia::render('Admin/Courses/Index', [
            'courses' => $courses,
            'filters' => [
                'university_id' => $request->university_id,
            ],
        ]);
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

        Course::create($validated);

        // ✅ FIX: redirect instead of JSON
        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course created successfully!');
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course)
    {
        // ✅ Use Inertia view for details
        return Inertia::render('Admin/Courses/Show', [
            'course' => $course->load('university'),
        ]);
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'university_id' => 'required|exists:universities,id',
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('courses')->ignore($course->id)
            ],
            'degree_level' => 'required|string|max:100',
            'field_of_study' => 'required|string|max:100',
            'tuition_fee' => 'nullable|numeric|min:0',
            'duration_years' => 'nullable|numeric|min:0.5|max:10',
            'description' => 'nullable|string',
            'application_deadline' => 'nullable|date',
        ]);

        $course->update($validated);

        // ✅ FIX: redirect for Inertia flow
        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course updated successfully!');
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Course $course)
    {
        $course->delete();

        // ✅ FIX: redirect instead of JSON
        return redirect()
            ->back()
            ->with('success', 'Course deleted successfully!');
    }
}
