<?php
namespace App\Http\Controllers\Api\UserProfile;
use App\Http\Controllers\Controller;
use App\Models\UserTechnicalEducation;
use App\Models\TechnicalEducationType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserTechnicalEducationController extends Controller
{
    public function index(Request $request): JsonResponse {
        $educations = $request->user()->technicalEducations()->with('educationType')->latest()->get();
        $educationTypes = TechnicalEducationType::orderBy('name')->get(['id', 'name']);
        return response()->json(['educations' => $educations, 'educationTypes' => $educationTypes]);
    }
    public function store(Request $request): JsonResponse {
        $validated = $request->validate([
            'education_type_id' => 'required|exists:technical_education_types,id',
            'institution_name' => 'required|string|max:255',
            'certification_name' => 'required|string|max:255',
            'completion_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);
        $education = $request->user()->technicalEducations()->create($validated);
        return response()->json($education->load('educationType'), 201);
    }
    public function update(Request $request, UserTechnicalEducation $technicalEducation): JsonResponse {
        if ($technicalEducation->user_id !== $request->user()->id) { abort(403); }
        $validated = $request->validate([
            'education_type_id' => 'required|exists:technical_education_types,id',
            'institution_name' => 'required|string|max:255',
            'certification_name' => 'required|string|max:255',
            'completion_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);
        $technicalEducation->update($validated);
        return response()->json($technicalEducation->load('educationType'));
    }
    public function destroy(Request $request, UserTechnicalEducation $technicalEducation): JsonResponse {
        if ($technicalEducation->user_id !== $request->user()->id) { abort(403); }
        $technicalEducation->delete();
        return response()->json(null, 204);
    }
}