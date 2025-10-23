<?php

namespace App\Http\Controllers\Api\UserProfile;

use App\Http\Controllers\Controller;
use App\Models\UserPortfolio;
use Illuminate\Http\Request;

class UserPortfolioController extends Controller
{
    /**
     * Display a listing of the user's portfolio items.
     */
    public function index(Request $request)
    {
        // Fetch only portfolio items belonging to the authenticated user
        return $request->user()->portfolios()->orderBy('created_at', 'desc')->get();
    }

    /**
     * Store a new portfolio item for the user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_title' => 'required|string|max:255',
            'project_url' => 'required|url|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        // Create the portfolio item associated with the authenticated user
        $portfolio = $request->user()->portfolios()->create($validated);

        return response()->json($portfolio, 201);
    }
}