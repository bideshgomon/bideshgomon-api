<?php

namespace App\Http\Controllers;

use App\Models\SmartSuggestion;
use App\Services\SmartSuggestionsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SmartSuggestionsController extends Controller
{
    protected $suggestionsService;

    public function __construct(SmartSuggestionsService $suggestionsService)
    {
        $this->suggestionsService = $suggestionsService;
    }

    /**
     * Display user's smart suggestions dashboard
     */
    public function index()
    {
        $user = auth()->user();

        // Generate fresh suggestions
        $this->suggestionsService->generateSuggestionsForUser($user);

        // Get active suggestions grouped by priority
        $suggestions = $this->suggestionsService->getActiveSuggestions($user);

        // Get counts by priority
        $counts = $this->suggestionsService->getSuggestionsCountByPriority($user);

        // Get latest assessment
        $assessment = $user->profileAssessments()->latest('assessed_at')->first();

        return Inertia::render('SmartSuggestions/Index', [
            'suggestions' => $suggestions,
            'counts' => $counts,
            'assessment' => $assessment,
            'profileStrength' => $assessment ? $assessment->overall_score : 0,
        ]);
    }

    /**
     * Mark suggestion as completed
     */
    public function complete(SmartSuggestion $suggestion)
    {
        // Verify ownership
        if ($suggestion->user_id !== auth()->id()) {
            abort(403);
        }

        $suggestion->markAsCompleted();

        return back()->with('success', 'Suggestion marked as completed!');
    }

    /**
     * Dismiss a suggestion
     */
    public function dismiss(SmartSuggestion $suggestion)
    {
        // Verify ownership
        if ($suggestion->user_id !== auth()->id()) {
            abort(403);
        }

        $suggestion->dismiss();

        return back()->with('success', 'Suggestion dismissed.');
    }

    /**
     * Refresh suggestions for current user
     */
    public function refresh()
    {
        $user = auth()->user();
        $this->suggestionsService->generateSuggestionsForUser($user);

        return back()->with('success', 'Suggestions refreshed!');
    }

    /**
     * Get suggestions via API (for AJAX requests)
     */
    public function apiIndex()
    {
        $user = auth()->user();
        $suggestions = $this->suggestionsService->getActiveSuggestions($user);
        $counts = $this->suggestionsService->getSuggestionsCountByPriority($user);

        return response()->json([
            'suggestions' => $suggestions,
            'counts' => $counts,
        ]);
    }
}
