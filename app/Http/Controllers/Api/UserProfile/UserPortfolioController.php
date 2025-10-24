<?php

namespace App\Http\Controllers\Api\UserProfile;

use App\Http\Controllers\Controller;
use App\Models\UserPortfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // <-- Import Storage facade

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
        // Note: Image upload is not handled in this store method in the original file.
        // It might be handled via a separate update/upload endpoint.
        $validated = $request->validate([
            'project_title' => 'required|string|max:255',
            'project_url' => 'required|url|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        // Create the portfolio item associated with the authenticated user
        $portfolio = $request->user()->portfolios()->create($validated);

        return response()->json($portfolio, 201);
    }

    // --- ADDED DESTROY METHOD WITH FIXES ---
    /**
     * Remove the specified portfolio item.
     */
    public function destroy(Request $request, UserPortfolio $portfolio)
    {
        // --- ADDED AUTHORIZATION CHECK ---
        // Check if the authenticated user owns this portfolio item
        if ($request->user()->id !== $portfolio->user_id) {
            // Using direct ID check for simplicity, Policies are another option.
            return response()->json(['message' => 'Forbidden'], 403);
        }
        // --- END AUTHORIZATION CHECK ---


        // --- ADDED FILE DELETION LOGIC ---
        // Delete the image file from storage if it exists
        // Assumes 'image_path' is the column storing the file path
        if ($portfolio->image_path && Storage::disk('public')->exists($portfolio->image_path)) {
            Storage::disk('public')->delete($portfolio->image_path);
        }
        // --- END FILE DELETION LOGIC ---

        // Delete the database record
        $portfolio->delete();

        return response()->json(['message' => 'Portfolio item deleted successfully']);
    }
    // --- END ADDED DESTROY METHOD ---
}