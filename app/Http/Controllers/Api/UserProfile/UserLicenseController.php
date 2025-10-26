<?php
namespace App\Http\Controllers\Api\UserProfile;
use App\Http\Controllers\Controller;
use App\Models\UserLicense;
use App\Models\LicenseType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserLicenseController extends Controller
{
    public function index(Request $request): JsonResponse {
        $licenses = $request->user()->licenses()->with('licenseType')->latest()->get();
        $licenseTypes = LicenseType::orderBy('name')->get(['id', 'name']);
        return response()->json(['licenses' => $licenses, 'licenseTypes' => $licenseTypes]);
    }
    public function store(Request $request): JsonResponse {
        $validated = $request->validate([
            'license_type_id' => 'required|exists:license_types,id',
            'license_number' => 'required|string|max:255',
            'issuing_authority' => 'required|string|max:255',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
        ]);
        $license = $request->user()->licenses()->create($validated);
        return response()->json($license->load('licenseType'), 201);
    }
    public function update(Request $request, UserLicense $license): JsonResponse {
        if ($license->user_id !== $request->user()->id) { abort(403); }
        $validated = $request->validate([
            'license_type_id' => 'required|exists:license_types,id',
            'license_number' => 'required|string|max:255',
            'issuing_authority' => 'required|string|max:255',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
        ]);
        $license->update($validated);
        return response()->json($license->load('licenseType'));
    }
    public function destroy(Request $request, UserLicense $license): JsonResponse {
        if ($license->user_id !== $request->user()->id) { abort(403); }
        $license->delete();
        return response()->json(null, 204);
    }
}