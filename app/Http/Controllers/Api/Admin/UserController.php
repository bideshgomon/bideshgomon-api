<?php

namespace App\Http\Controllers\Api\Admin; // <-- Corrected Namespace for API

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile; // <-- Import UserProfile
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request; // <-- Use JsonResponse for API
use Illuminate\Support\Facades\Hash; // <-- Import Hash facade
use Illuminate\Support\Facades\Log; // <-- Import Log facade
use Illuminate\Validation\Rule; // <-- Import Validation Rules
use Illuminate\Validation\Rules; // <-- Import Rule for unique constraint

class UserController extends Controller // <-- Renamed class from UserPageController if needed, or keep separate
{
    /**
     * Display a listing of the users (API version).
     * Reuse logic from PageController for consistency.
     */
    public function index(Request $request): JsonResponse // <-- Return JsonResponse
    {
        $query = User::with('role')->latest('created_at');

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('email', 'LIKE', "%{$searchTerm}%");
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('slug', $request->input('role'));
            });
        }

        $users = $query->paginate(15)->withQueryString();

        // Return JSON response for API
        return response()->json($users);
    }

    /**
     * Store a newly created user in storage. (CREATE)
     */
    public function store(Request $request): JsonResponse // <-- Added Method
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'phone' => 'nullable|string|max:20', // Add phone validation
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'required|boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'is_active' => $validated['is_active'],
            'email_verified_at' => now(), // Assume admin-created users are verified
        ]);

        // Create an empty profile for the new user
        UserProfile::create(['user_id' => $user->id]);

        // Return the created user with role, status 201
        return response()->json($user->load('role'), 201);
    }

    /**
     * Update the specified user in storage. (UPDATE)
     */
    public function update(Request $request, User $user): JsonResponse // <-- Added Method
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Ignore unique rule for the current user's email
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            // Password update is optional
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'required|boolean',
        ]);

        // Prepare data, only hash password if provided
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role_id' => $validated['role_id'],
            'is_active' => $validated['is_active'],
        ];

        // If a new password was entered, hash and include it
        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
            // Optionally: Force email re-verification if password changes?
            // $updateData['email_verified_at'] = null;
        }

        // If email changed, mark as unverified
        if ($user->email !== $validated['email']) {
            $updateData['email_verified_at'] = null;
            // Optionally: Send verification email here
        }

        $user->update($updateData);

        // Return the updated user with role
        return response()->json($user->load('role'));
    }

    /**
     * Remove the specified user from storage. (DELETE)
     */
    public function destroy(User $user): JsonResponse // <-- Added Method
    {
        // Add safety check: prevent deleting the last admin or oneself?
        // Example: if ($user->hasRole('admin') && User::whereHas('role', fn($q) => $q->where('slug', 'admin'))->count() <= 1) { ... }
        // Example: if ($user->id === auth()->id()) { ... }

        try {
            // Soft delete is handled automatically if SoftDeletes trait is used
            $user->delete();

            return response()->json(['message' => 'User deleted successfully.'], 200); // Or 204 No Content
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error deleting user: '.$user->id, ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Failed to delete user.'], 500);
        }
    }
}
