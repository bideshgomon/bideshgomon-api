<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DegreeLevel; // <-- Import DegreeLevel
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class DegreeLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = DegreeLevel::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        $degreeLevelToEdit = null;
        if ($request->has('edit_id')) {
            $degreeLevelToEdit = DegreeLevel::find($request->edit_id);
        }

        $degreeLevels = $query->orderBy('name')->paginate(15)->withQueryString();

        return Inertia::render('Admin/DegreeLevels/Index', [
            'degreeLevels' => $degreeLevels,
            'filters' => $request->only(['search']),
            'degreeLevelToEdit' => $degreeLevelToEdit,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:degree_levels,name',
            'is_active' => 'required|boolean',
        ]);

        DegreeLevel::create($validated);

        return Redirect::route('admin.degree-levels.index')->with('success', 'Degree Level created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DegreeLevel $degreeLevel)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('degree_levels', 'name')->ignore($degreeLevel->id)],
            'is_active' => 'required|boolean',
        ]);

        $degreeLevel->update($validated);

        return Redirect::route('admin.degree-levels.index')->with('success', 'Degree Level updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DegreeLevel $degreeLevel)
    {
        // Optional: Check if the degree level is in use before deleting
        // if ($degreeLevel->degrees()->exists() || $degreeLevel->courses()->exists()) {
        //     return Redirect::back()->with('error', 'Cannot delete Degree Level as it is currently in use.');
        // }
        $degreeLevel->delete();

        return Redirect::route('admin.degree-levels.index')->with('success', 'Degree Level deleted successfully.');
    }
}