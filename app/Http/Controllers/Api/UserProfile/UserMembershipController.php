<?php
namespace App\Http\Controllers\Api\UserProfile;
use App\Http\Controllers\Controller;
use App\Models\UserMembership;
use App\Models\OrganizationType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserMembershipController extends Controller
{
    public function index(Request $request): JsonResponse {
        $memberships = $request->user()->memberships()->with('organizationType')->latest()->get();
        $organizationTypes = OrganizationType::orderBy('name')->get(['id', 'name']);
        return response()->json(['memberships' => $memberships, 'organizationTypes' => $organizationTypes]);
    }
    public function store(Request $request): JsonResponse {
        $validated = $request->validate([
            'organization_type_id' => 'required|exists:organization_types,id',
            'organization_name' => 'required|string|max:255',
            'membership_id' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        $membership = $request->user()->memberships()->create($validated);
        return response()->json($membership->load('organizationType'), 201);
    }
    public function update(Request $request, UserMembership $membership): JsonResponse {
        if ($membership->user_id !== $request->user()->id) { abort(403); }
        $validated = $request->validate([
            'organization_type_id' => 'required|exists:organization_types,id',
            'organization_name' => 'required|string|max:255',
            'membership_id' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        $membership->update($validated);
        return response()->json($membership->load('organizationType'));
    }
    public function destroy(Request $request, UserMembership $membership): JsonResponse {
        if ($membership->user_id !== $request->user()->id) { abort(403); }
        $membership->delete();
        return response()->json(null, 204);
    }
}