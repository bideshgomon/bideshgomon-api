<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FieldOfStudy; // <-- Import FieldOfStudy
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class FieldOfStudyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = FieldOfStudy::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        $fieldOfStudyToEdit = null;
        if ($request->has('edit_id')) {
            $fieldOfStudyToEdit = FieldOfStudy::find($request->edit_id);
        }

        $fieldsOfStudy = $query->orderBy('name')->paginate(15)->withQueryString();

        return Inertia::render('Admin/FieldsOfStudy/Index', [ // <-- Adjusted view path
            'fieldsOfStudy' => $fieldsOfStudy,
            'filters' => $request->only(['search']),
            'fieldOfStudyToEdit' => $fieldOfStudyToEdit,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:fields_of_study,name',
            'is_active' => 'required|boolean',
        ]);

        FieldOfStudy::create($validated);

        return Redirect::route('admin.fields-of-study.index')->with('success', 'Field of Study created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FieldOfStudy $fieldOfStudy) // <-- Type hint model
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('fields_of_study', 'name')->ignore($fieldOfStudy->id)],
            'is_active' => 'required|boolean',
        ]);

        $fieldOfStudy->update($validated);

        return Redirect::route('admin.fields-of-study.index')->with('success', 'Field of Study updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FieldOfStudy $fieldOfStudy) // <-- Type hint model
    {
        // Optional: Check if in use before deleting
        // if ($fieldOfStudy->degrees()->exists() || $fieldOfStudy->courses()->exists()) { ... }
        $fieldOfStudy->delete();

        return Redirect::route('admin.fields-of-study.index')->with('success', 'Field of Study deleted successfully.');
    }
}