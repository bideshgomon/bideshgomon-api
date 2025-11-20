<?php

namespace App\Http\Controllers\Api\UserProfile;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\LanguageTest;
use App\Models\UserLanguage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserLanguageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $languages = $request->user()->languages()->with(['language', 'languageTest'])->latest()->get();
        $allLanguages = Language::orderBy('name')->get(['id', 'name']);
        $languageTests = LanguageTest::with('language:id,name')->orderBy('name')->get(['id', 'name', 'language_id']);

        return response()->json([
            'languages' => $languages,
            'allLanguages' => $allLanguages,
            'languageTests' => $languageTests,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'language_id' => 'required|exists:languages,id',
            'proficiency_level' => 'required_without:proficiency|string|max:50',
            'proficiency' => 'nullable|string|max:50', // legacy support

            // Language Skills
            'can_read' => 'nullable|boolean',
            'can_write' => 'nullable|boolean',
            'can_speak' => 'nullable|boolean',
            'can_understand' => 'nullable|boolean',
            'is_native' => 'nullable|boolean',

            // Language Test Details
            'language_test_id' => 'nullable|exists:language_tests,id',
            'overall_score' => 'nullable|numeric|min:0|max:120',
            'reading_score' => 'nullable|numeric|min:0|max:30',
            'writing_score' => 'nullable|numeric|min:0|max:30',
            'listening_score' => 'nullable|numeric|min:0|max:30',
            'speaking_score' => 'nullable|numeric|min:0|max:30',
            'test_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:test_date',
            'test_reference_number' => 'nullable|string|max:100',

            // Legacy fields
            'test_taken' => 'nullable|string|max:100',
            'test_score' => 'nullable|string|max:50',

            // Certificate file
            'certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // Max 2MB
        ]);

        $payload = [
            'language_id' => $validated['language_id'],
            'proficiency_level' => $validated['proficiency_level'] ?? $validated['proficiency'],
            'can_read' => $validated['can_read'] ?? false,
            'can_write' => $validated['can_write'] ?? false,
            'can_speak' => $validated['can_speak'] ?? false,
            'can_understand' => $validated['can_understand'] ?? false,
            'is_native' => $validated['is_native'] ?? false,
            'language_test_id' => $validated['language_test_id'] ?? null,
            'overall_score' => $validated['overall_score'] ?? null,
            'reading_score' => $validated['reading_score'] ?? null,
            'writing_score' => $validated['writing_score'] ?? null,
            'listening_score' => $validated['listening_score'] ?? null,
            'speaking_score' => $validated['speaking_score'] ?? null,
            'test_date' => $validated['test_date'] ?? null,
            'expiry_date' => $validated['expiry_date'] ?? null,
            'test_reference_number' => $validated['test_reference_number'] ?? null,
            'test_taken' => $validated['test_taken'] ?? null,
            'test_score' => $validated['test_score'] ?? null,
        ];

        // Handle certificate file upload
        if ($request->hasFile('certificate')) {
            $file = $request->file('certificate');
            $userId = $request->user()->id;
            $filename = 'language_cert_'.time().'_'.$file->getClientOriginalName();
            $path = $file->storeAs("language-certificates/{$userId}", $filename, 'public');
            $payload['file_path'] = $path;
        }

        $language = $request->user()->languages()->create($payload);

        return response()->json($language->load(['language', 'languageTest']), 201);
    }

    public function update(Request $request, UserLanguage $language): JsonResponse
    {
        if ($language->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'language_id' => 'required|exists:languages,id',
            'proficiency_level' => 'required_without:proficiency|string|max:50',
            'proficiency' => 'nullable|string|max:50', // legacy support

            // Language Skills
            'can_read' => 'nullable|boolean',
            'can_write' => 'nullable|boolean',
            'can_speak' => 'nullable|boolean',
            'can_understand' => 'nullable|boolean',
            'is_native' => 'nullable|boolean',

            // Language Test Details
            'language_test_id' => 'nullable|exists:language_tests,id',
            'overall_score' => 'nullable|numeric|min:0|max:120',
            'reading_score' => 'nullable|numeric|min:0|max:30',
            'writing_score' => 'nullable|numeric|min:0|max:30',
            'listening_score' => 'nullable|numeric|min:0|max:30',
            'speaking_score' => 'nullable|numeric|min:0|max:30',
            'test_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:test_date',
            'test_reference_number' => 'nullable|string|max:100',

            // Legacy fields
            'test_taken' => 'nullable|string|max:100',
            'test_score' => 'nullable|string|max:50',

            // Certificate file
            'certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $payload = [
            'language_id' => $validated['language_id'],
            'proficiency_level' => $validated['proficiency_level'] ?? $validated['proficiency'],
            'can_read' => $validated['can_read'] ?? false,
            'can_write' => $validated['can_write'] ?? false,
            'can_speak' => $validated['can_speak'] ?? false,
            'can_understand' => $validated['can_understand'] ?? false,
            'is_native' => $validated['is_native'] ?? false,
            'language_test_id' => $validated['language_test_id'] ?? null,
            'overall_score' => $validated['overall_score'] ?? null,
            'reading_score' => $validated['reading_score'] ?? null,
            'writing_score' => $validated['writing_score'] ?? null,
            'listening_score' => $validated['listening_score'] ?? null,
            'speaking_score' => $validated['speaking_score'] ?? null,
            'test_date' => $validated['test_date'] ?? null,
            'expiry_date' => $validated['expiry_date'] ?? null,
            'test_reference_number' => $validated['test_reference_number'] ?? null,
            'test_taken' => $validated['test_taken'] ?? null,
            'test_score' => $validated['test_score'] ?? null,
        ];

        // Handle certificate file upload
        if ($request->hasFile('certificate')) {
            // Delete old certificate if exists
            if ($language->file_path) {
                Storage::disk('public')->delete($language->file_path);
            }

            $file = $request->file('certificate');
            $userId = $request->user()->id;
            $filename = 'language_cert_'.time().'_'.$file->getClientOriginalName();
            $path = $file->storeAs("language-certificates/{$userId}", $filename, 'public');
            $payload['file_path'] = $path;
        }

        $language->update($payload);

        return response()->json($language->load(['language', 'languageTest']));
    }

    public function destroy(Request $request, UserLanguage $language): JsonResponse
    {
        if ($language->user_id !== $request->user()->id) {
            abort(403);
        }

        // Delete certificate file if exists
        if ($language->file_path) {
            Storage::disk('public')->delete($language->file_path);
        }

        $language->delete();

        return response()->json(null, 204);
    }
}
