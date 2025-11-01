<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language; // <-- Import Language
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Language::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%'.$search.'%')
                ->orWhere('code', 'like', '%'.$search.'%'); // Allow searching by code (e.g., 'en')
        }

        $languageToEdit = null;
        if ($request->has('edit_id')) {
            $languageToEdit = Language::find($request->edit_id);
        }

        $languages = $query->orderBy('name')->paginate(15)->withQueryString();

        return Inertia::render('Admin/Languages/Index', [ // <-- Adjusted view path
            'languages' => $languages,
            'filters' => $request->only(['search']),
            'languageToEdit' => $languageToEdit,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:languages,name',
            'code' => 'required|string|max:10|unique:languages,code', // Add code validation
            'is_active' => 'required|boolean',
        ]);

        Language::create($validated);

        return Redirect::route('admin.languages.index')->with('success', 'Language created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Language $language) // <-- Type hint model
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('languages', 'name')->ignore($language->id)],
            'code' => ['required', 'string', 'max:10', Rule::unique('languages', 'code')->ignore($language->id)], // Add code validation
            'is_active' => 'required|boolean',
        ]);

        $language->update($validated);

        return Redirect::route('admin.languages.index')->with('success', 'Language updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Language $language) // <-- Type hint model
    {
        // Optional: Check if in use
        // if ($language->userLanguages()->exists()) { ... }
        $language->delete();

        return Redirect::route('admin.languages.index')->with('success', 'Language deleted successfully.');
    }
}
