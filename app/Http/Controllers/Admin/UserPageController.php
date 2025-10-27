<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role; // Import Role model
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserPageController extends Controller
{
    /**
     * Display the user management index page.
     */
    public function index()
    {
        // Pass roles for filtering/dropdowns later if needed
        return Inertia::render('Admin/Users/Index', [
             'roles' => Role::select('id', 'name')->get(), // Pass roles for create/edit forms
        ]);
        // Note: Actual user data will be fetched via API call from the Vue component
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return Inertia::render('Admin/Users/Create', [
            'roles' => Role::select('id', 'name')->get(), // Pass roles for the dropdown
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
         // Eager load the role relationship for the specific user being edited
        $user->load('role');

        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
            'roles' => Role::select('id', 'name')->get(), // Pass roles for the dropdown
        ]);
    }
}