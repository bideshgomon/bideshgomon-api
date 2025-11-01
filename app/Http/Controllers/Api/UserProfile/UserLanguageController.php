<?php

namespace App\Http\Controllers\Api\UserProfile;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\UserLanguage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserLanguageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $languages = $request->user()->languages()->with('language')->latest()->get();
        $allLanguages = Language::orderBy('name')->get(['id', 'name']);

        return response()->json(['languages' => $languages, 'allLanguages' => $allLanguages]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'language_id' => 'required|exists:languages,id',
            'proficiency_level' => 'required|string|max:50',
            'test_taken' => 'nullable|string|max:100',
            'test_score' => 'nullable|string|max:50',
            'test_date' => 'nullable|date',
        ]);
        $language = $request->user()->languages()->create($validated);

        return response()->json($language->load('language'), 201);
    }

    public function update(Request $request, UserLanguage $language): JsonResponse
    {
        if ($language->user_id !== $request->user()->id) {
            abort(403);
        }
        $validated = $request->validate([
            'language_id' => 'required|exists:languages,id',
            'proficiency_level' => 'required|string|max:50',
            'test_taken' => 'nullable|string|max:100',
            'test_score' => 'nullable|string|max:50',
            'test_date' => 'nullable|date',
        ]);
        $language->update($validated);

        return response()->json($language->load('language'));
    }

    public function destroy(Request $request, UserLanguage $language): JsonResponse
    {
        if ($language->user_id !== $request->user()->id) {
            abort(403);
        }
        $language->delete();

        return response()->json(null, 204);
    }
}
