<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response; // Import Skill model

class SkillPageController extends Controller
{
    /**
     * Display the skill management index page.
     */
    public function index(Request $request): Response
    {
        // Build query for skills
        $query = Skill::query()->orderBy('name', 'asc');

        // Filter by Search Term (Skill Name or Category)
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('category', 'LIKE', "%{$searchTerm}%"); // Allow searching category too
            });
        }

        // Paginate results
        $skills = $query->paginate(25)->withQueryString();

        // Pass data to the Inertia view
        return Inertia::render('Admin/Skills/Index', [
            'skills' => $skills,
            'filters' => $request->only(['search']), // Current filters
        ]);
    }
}
