<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User; // Import Role model
use Illuminate\Http\Request; // Import Request
use Inertia\Inertia;
use Inertia\Response; // Import Response

class UserPageController extends Controller
{
    /**
     * Display the user management index page.
     */
    public function index(Request $request): Response // <-- Add Request and Response type hints
    {
        // Start building the query to fetch users
        $query = User::with('role') // Eager load the role relationship
            ->latest('created_at'); // Order by newest first

        // Add filtering based on request parameters (optional for now)
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('email', 'LIKE', "%{$searchTerm}%");
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('slug', $request->input('role')); // Filter by role slug
            });
        }

        // Paginate the results
        $users = $query->paginate(15)->withQueryString(); // Use withQueryString to preserve filters

        // Pass users and roles to the Inertia view
        return Inertia::render('Admin/Users/Index', [
            'users' => $users, // <-- Pass the paginated users
            'filters' => $request->only(['search', 'role']), // Pass current filters back
            'roles' => Role::select('id', 'name', 'slug')->get(), // Pass roles for filter dropdown
        ]);
        // Note: Removed the comment about API call, data is now passed directly.
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): Response // <-- Add Response type hint
    {
        return Inertia::render('Admin/Users/Create', [
            'roles' => Role::select('id', 'name')->get(), // Pass roles for the dropdown
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): Response // <-- Add Response type hint
    {
        // Eager load the role relationship for the specific user being edited
        $user->load('role');

        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
            'roles' => Role::select('id', 'name')->get(), // Pass roles for the dropdown
        ]);
    }
}
