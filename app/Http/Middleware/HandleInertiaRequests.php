<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy; // YOUR CORRECT NAMESPACE
use App\Models\User;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            // 1. Authenticated user data
            'auth' => [
                'user' => $request->user() ? $this->transformUser($request->user()) : null,
            ],

            // 2. Ziggy route helper
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
                'query' => $request->query(),
            ],
        ]);
    }

    /**
     * Ensure the user model includes the 'role' relationship and sanitize output.
     * This is the FINAL FIX.
     */
    protected function transformUser(User $user): ?array
    {
        if (!$user->relationLoaded('role')) {
            $user->loadMissing('role');
        }

        // **THE FINAL FIX IS HERE**
        // We now check if the role is null.
        // If it is, we create a "fake" guest role object.
        // This prevents the frontend from ever crashing when trying to read 'role.name'.
        $role = $user->role ? [
            'id' => $user->role->id,
            'name' => $user->role->name,
        ] : (object)['id' => null, 'name' => 'guest']; // Send a guest object instead of null

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $role, // Pass the safe role object
        ];
    }
}