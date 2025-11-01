<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill; // <-- Import Skill
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SkillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Skill::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%'.$search.'%');
        }

        $skillToEdit = null;
        if ($request->has('edit_id')) {
            $skillToEdit = Skill::find($request->edit_id);
        }

        $skills = $query->orderBy('name')->paginate(15)->withQueryString();

        return Inertia::render('Admin/Skills/Index', [ // <-- Adjusted view path
            'skills' => $skills,
            'filters' => $request->only(['search']),
            'skillToEdit' => $skillToEdit,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:skills,name',
            'is_active' => 'required|boolean',
        ]);

        Skill::create($validated);

        return Redirect::route('admin.skills.index')->with('success', 'Skill created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Skill $skill) // <-- Type hint model
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('skills', 'name')->ignore($skill->id)],
            'is_active' => 'required|boolean',
        ]);

        $skill->update($validated);

        return Redirect::route('admin.skills.index')->with('success', 'Skill updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Skill $skill) // <-- Type hint model
    {
        // Optional: Check if in use (e.g., user_skill pivot table)
        // if ($skill->users()->exists()) { ... }
        $skill->delete();

        return Redirect::route('admin.skills.index')->with('success', 'Skill deleted successfully.');
    }
}
