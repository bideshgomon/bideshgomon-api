<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    /**
     * Display a listing of all available skills.
     */
    public function index(Request $request)
    {
        $query = Skill::query();

        // Filter by category if provided
        if ($request->has('category') && $request->category !== 'All') {
            $query->where('category', $request->category);
        }

        // Search by name if provided
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $skills = $query->orderBy('category')->orderBy('name')->get();

        return response()->json($skills);
    }
}
