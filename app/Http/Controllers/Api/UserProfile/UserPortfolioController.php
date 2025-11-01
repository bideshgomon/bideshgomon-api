<?php

namespace App\Http\Controllers\Api\UserProfile;

use App\Http\Controllers\Controller;
use App\Models\UserPortfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPortfolioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $portfolios = Auth::user()->portfolios()->orderBy('created_at', 'desc')->get();

        return response()->json($portfolios);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|url',
            // Add validation for file uploads if you handle them here
        ]);

        $portfolio = Auth::user()->portfolios()->create($validated);

        return response()->json($portfolio, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserPortfolio $portfolio)
    {
        // ✅ [SECURITY FIX] Check ownership
        if ($portfolio->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|url',
        ]);

        $portfolio->update($validated);

        return response()->json($portfolio);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserPortfolio $portfolio)
    {
        // ✅ [SECURITY FIX] Check ownership
        if ($portfolio->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $portfolio->delete();

        return response()->json(null, 204);
    }
}
