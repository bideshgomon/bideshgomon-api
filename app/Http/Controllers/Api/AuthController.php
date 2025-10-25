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
        $userRole = Role::where('name', 'user')->first();
        if (!$userRole) {
             return response()->json(['message' => 'Default user role not found.'], 500);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $userRole->id,
        ]);

        // Create an empty profile for the new user
        UserProfile::create(['user_id' => $user->id]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful! Profile created.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('role', 'userProfile') // Send back user data
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

        // Eager load relationships based on role
        if ($user->role->name === 'user') {
            $user->load('userProfile');
        } elseif ($user->role->name === 'agency') {
            $user->load('ownedAgency');
        } 
        // ✅ [BUG FIX] Added logic for 'consultant' role
        elseif ($user->role->name === 'consultant') {
            $user->load('agenciesAsConsultant', 'clients');
        }
        // Admin role typically doesn't need extra profile data loaded here

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
}