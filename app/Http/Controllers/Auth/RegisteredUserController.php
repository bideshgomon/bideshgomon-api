<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ReferralService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    protected $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * Display the registration view.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Auth/Register', [
            'referralCode' => $request->query('ref'),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'phone' => 'required|string|max:20',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'referral_code' => 'nullable|string|size:8|exists:users,referral_code',
        ]);

        // Combine name parts for user.name field
        $fullName = trim(implode(' ', array_filter([
            $request->first_name,
            $request->middle_name,
            $request->last_name,
        ])));

        // Get default 'user' role
        $userRole = \App\Models\Role::where('slug', 'user')->first();
        if (!$userRole) {
            throw new \Exception('Default user role not found. Please run seeders.');
        }

        $user = User::create([
            'name' => $fullName,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => $userRole->id,
        ]);

        // Create profile with passport-standard name fields
        $user->profile()->create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'name_as_per_passport' => strtoupper($fullName),
        ]);

        event(new Registered($user));

        // Track referral if referral code provided
        if ($request->filled('referral_code')) {
            try {
                $this->referralService->trackReferral($request->referral_code, $user->id);
            } catch (\Exception $e) {
                // Log error but don't stop registration
                Log::error('Referral tracking failed: ' . $e->getMessage());
            }
        }

        Auth::login($user);

        // Redirect new users to onboarding welcome page
        return redirect(route('onboarding.welcome', absolute: false));
    }
}
