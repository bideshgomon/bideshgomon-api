<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role; // <-- [PATCH] Import Role model
use App\Models\UserProfile; // <-- [PATCH] Import UserProfile model
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // --- [PATCH START] ---
        // Find the 'user' role
        // We use slug 'user' as it's more reliable than the name 'user' which was in your RoleSeeder
        $userRole = Role::where('slug', 'user')->first(); 
        if (!$userRole) {
            // This is a critical error if seeders haven't run
            throw new \Exception('Default user role not found. Please run database seeders.');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $userRole->id, // Assign the role_id
            'is_active' => true, // Default new web users to active
        ]);

        // Create an empty profile for the new user
        UserProfile::create(['user_id' => $user->id]);
        // --- [PATCH END] ---

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}