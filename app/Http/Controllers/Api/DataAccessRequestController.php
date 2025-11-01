<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataAccessRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DataAccessRequestController extends Controller
{
    /**
     * Store a new data access request (Consultant initiates).
     */
    public function store(Request $request, User $user)
    {
        // 1. Authorization: Only 'consultant' can request
        $consultant = $request->user();
        if (! $consultant->hasRole('consultant')) {
            return response()->json(['message' => 'Unauthorized: Only consultants can request data access.'], 403);
        }

        // 2. Target User Check: Cannot request access to self or non-'user' roles
        if ($user->id === $consultant->id || ! $user->hasRole('user')) {
            return response()->json(['message' => 'Invalid target user.'], 400);
        }

        // 3. Prevent Duplicate Pending Requests: Check if a pending request already exists
        $existingPending = DataAccessRequest::where('consultant_id', $consultant->id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingPending) {
            return response()->json(['message' => 'A pending request already exists for this user.'], 409); // 409 Conflict
        }

        // 4. Prevent Request if Already Approved: Check if an approved request exists
        $existingApproved = DataAccessRequest::where('consultant_id', $consultant->id)
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->first();

        if ($existingApproved) {
            return response()->json(['message' => 'You already have approved access to this user\'s data.'], 409);
        }

        // 5. Create the Request
        $dataAccessRequest = DataAccessRequest::create([
            'consultant_id' => $consultant->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'requested_at' => Carbon::now(),
        ]);

        // Optional: Notify the user (e.g., via email or in-app notification) - Implementation later

        return response()->json($dataAccessRequest, 201);
    }

    /**
     * Display pending data access requests for the authenticated user.
     */
    public function index(Request $request)
    {
        // 1. Authorization: Only 'user' can view their requests
        $user = $request->user();
        if (! $user->hasRole('user')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // 2. Fetch Pending Requests: Eager load consultant details
        $pendingRequests = DataAccessRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with('consultant:id,name,email') // Load consultant info
            ->orderBy('requested_at', 'desc')
            ->get();

        return response()->json($pendingRequests);
    }

    /**
     * Approve a data access request (User approves).
     */
    public function approve(Request $request, DataAccessRequest $dataAccessRequest)
    {
        // 1. Authorization: Ensure the logged-in user is the target of the request
        $user = $request->user();
        if ($dataAccessRequest->user_id !== $user->id || ! $user->hasRole('user')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // 2. Check Status: Only pending requests can be approved
        if ($dataAccessRequest->status !== 'pending') {
            return response()->json(['message' => 'This request is not pending approval.'], 400);
        }

        // 3. Update Status
        $dataAccessRequest->update([
            'status' => 'approved',
            'responded_at' => Carbon::now(),
        ]);

        // Optional: Notify the consultant - Implementation later

        return response()->json(['message' => 'Access request approved successfully.', 'request' => $dataAccessRequest]);
    }

    /**
     * Deny a data access request (User denies).
     */
    public function deny(Request $request, DataAccessRequest $dataAccessRequest)
    {
        // 1. Authorization: Ensure the logged-in user is the target of the request
        $user = $request->user();
        if ($dataAccessRequest->user_id !== $user->id || ! $user->hasRole('user')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // 2. Check Status: Only pending requests can be denied
        if ($dataAccessRequest->status !== 'pending') {
            return response()->json(['message' => 'This request is not pending denial.'], 400);
        }

        // 3. Update Status
        $dataAccessRequest->update([
            'status' => 'denied',
            'responded_at' => Carbon::now(),
        ]);

        // Optional: Notify the consultant - Implementation later

        return response()->json(['message' => 'Access request denied successfully.', 'request' => $dataAccessRequest]);
    }
}
