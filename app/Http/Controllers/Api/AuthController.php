<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Role;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Log; // <-- [RECOMMENDATION] Add Log facade

class AuthController extends Controller
{
    /**
     * Register a new user (General User role only).
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Find the 'user' role
        $userRole = Role::where('slug', 'user')->first(); // <-- [PATCH] Find by 'slug' for consistency
        if (!$userRole) {
             // --- [PATCH START] ---
             Log::error('Default user role "user" not found during API registration.');
             return response()->json(['message' => 'System configuration error: Default role not found.'], 500);
             // --- [PATCH END] ---
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $userRole->id,
            'is_active' => true, // <-- [PATCH] Explicitly set new API users to active
        ]);

        // Create an empty profile for the new user
        UserProfile::create(['user_id' => $user->id]);

        $token = $user->createToken('auth_token')->plainTextToken;

        // --- [PATCH START] ---
        // Load the 'profile' relationship (which we renamed in User.php)
        $user->load('role', 'profile'); 
        // --- [PATCH END] ---

        return response()->json([
            'message' => 'Registration successful! Profile created.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user // Send back user with relations
        ], 201);
    }

    /**
     * Log the user in.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid login credentials'], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        // Optional: Check if user is active
        if (!$user->is_active) {
             // --- [PATCH START] ---
             Auth::logout(); // Log out the user if they are inactive
             // --- [PATCH END] ---
             return response()->json(['message' => 'Your account is deactivated.'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('role') // Send back user data
        ]);
    }

    /**
     * Get the authenticated User.
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $user->load('role'); // Always load the role

        // --- [PATCH START] ---
        // Eager load relationships based on role SLUG
        // Use the renamed 'profile' relation
        if ($user->role->slug === 'user') {
            $user->load('profile'); 
        } elseif ($user->role->slug === 'agency') {
            $user->load('ownedAgency');
        } 
        elseif ($user->role->slug === 'consultant') {
            // Load relations defined in User.php
            $user->load('agenciesAsConsultant', 'clients');
        }
        // --- [PATCH END] ---

        return response()->json($user);
    }

    /**
     * Log the user out (Revoke the token).
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }
} // <-- [PATCH] The extra '}' brace was removed from here