<?php

namespace App\Http\Controllers\Api\UserProfile;

use App\Http\Controllers\Controller;
use App\Models\UserLicense;
use Illuminate\Http\Request;

class UserLicenseController extends Controller
{
    /**
     * Display a listing of the user's licenses.
     */
    public function index(Request $request)
    {
        return $request->user()->licenses()->orderBy('issue_date', 'desc')->get();
    }

    /**
     * Store a newly created license in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'issuing_organization' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
            'credential_id' => 'nullable|string|max:255',
        ]);

        $license = $request->user()->licenses()->create($validated);

        return response()->json($license, 201);
    }

    /**
     * Update the specified license in storage.
     */
    public function update(Request $request, UserLicense $license)
    {
        // Authorize
        if ($request->user()->id !== $license->user_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'issuing_organization' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
            'credential_id' => 'nullable|string|max:255',
        ]);

        $license->update($validated);

        return response()->json($license);
    }

    /**
     * Remove the specified license from storage.
     */
    public function destroy(Request $request, UserLicense $license)
    {
        // Authorize
        if ($request->user()->id !== $license->user_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $license->delete();

        return response()->json(null, 204);
    }
}
