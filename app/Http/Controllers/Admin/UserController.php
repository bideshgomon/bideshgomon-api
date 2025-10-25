<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role; // <-- Import Role model from version 1
use Illuminate\Http\Request; // <-- Keep standard Request import if needed later
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse; // <-- Import RedirectResponse from version 1
use App\Http\Requests\Admin\UpdateUserRequest; // <-- Import UpdateUserRequest from version 1


class UserController extends Controller
{
    /**
     * Display a listing of all users.
     */
    public function index(): Response
    {
        $users = User::with('role') // Eager load role
            ->orderBy('name')
            ->paginate(15); // Paginate results

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): Response // Route model binding from version 1
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user->load('role'), // Pass the user data, ensuring role is loaded
            'roles' => Role::orderBy('name')->get(), // Pass all available roles from version 1
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse // Method signature from version 1
    {
        $validatedData = $request->validated(); // Use validated data from UpdateUserRequest

        // Optional: Handle password update logic (from version 1)
        // if (!empty($validatedData['password'])) {
        //     $validatedData['password'] = bcrypt($validatedData['password']);
        // } else {
        //     unset($validatedData['password']); // Don't update password if empty
        // }

        $user->update($validatedData); // Update user with validated data

        // Redirect back to index with success message (from version 1)
        return redirect()->route('admin.users.index')
                         ->with('success', 'User updated successfully.');
    }

    // Add destroy method later if needed (placeholder from version 1)
    // public function destroy(User $user): RedirectResponse { ... }
}