<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role; // Import Role model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Import Hash facade
use Illuminate\Validation\Rules; // Import validation rules
use Illuminate\Validation\Rule; // Import Rule for unique validation

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Add pagination and search/filtering later if needed
        $users = User::with('role')->latest()->paginate(15); // Eager load role
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => 'required|exists:roles,id', // Ensure role exists
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        // Default is_active to true if not provided
        $validated['is_active'] = $request->boolean('is_active', true);

        $user = User::create($validated);

        return response()->json($user->load('role'), 201); // Return created user with role
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return response()->json($user->load('role')); // Eager load role
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id), // Ignore current user's email
            ],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()], // Allow password to be optional
            'role_id' => 'sometimes|required|exists:roles,id',
            'is_active' => 'sometimes|boolean',
        ]);

        // Only hash and update password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']); // Don't update password if empty
        }

        $user->update($validated);

        return response()->json($user->load('role'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Add protection: prevent deleting the logged-in admin or the last admin?
        // Example: if (auth()->id() === $user->id) { abort(403, 'Cannot delete yourself.'); }

        $user->delete();
        return response()->json(null, 204); // No content on successful delete
    }
}