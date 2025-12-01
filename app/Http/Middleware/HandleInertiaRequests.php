<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        
        if ($user) {
            $user->loadMissing('role');
        }

        $impersonator = null;
        if (session()->has('impersonator_id')) {
            $originalId = session('impersonator_id');
            if ($user && $user->id !== $originalId) {
                // Fetch minimal original admin data
                $impersonatorModel = \App\Models\User::query()->with('role')->select(['id','name','email','role_id'])->find($originalId);
                if ($impersonatorModel) {
                    $impersonator = [
                        'id' => $impersonatorModel->id,
                        'name' => $impersonatorModel->name,
                        'email' => $impersonatorModel->email,
                        'role' => $impersonatorModel->role ? [
                            'id' => $impersonatorModel->role->id,
                            'name' => $impersonatorModel->role->name,
                            'slug' => $impersonatorModel->role->slug,
                        ] : null,
                    ];
                }
            }
        }
        
        return [
            ...parent::share($request),
            'locale' => app()->getLocale(),
            'available_locales' => config('app.available_locales', ['en', 'bn']),
            'translations' => function () {
                $locale = app()->getLocale();
                return [
                    'ui' => \Illuminate\Support\Facades\Lang::get('ui', [], $locale),
                    'auth' => \Illuminate\Support\Facades\Lang::get('auth', [], $locale),
                    'validation' => \Illuminate\Support\Facades\Lang::get('validation', [], $locale),
                ];
            },
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role ? [
                        'id' => $user->role->id,
                        'name' => $user->role->name,
                        'slug' => $user->role->slug,
                    ] : null,
                    'impersonating' => session()->has('impersonator_id'),
                    'impersonator_id' => session('impersonator_id'),
                    'impersonator' => $impersonator,
                    'unread_notifications' => $user ? \App\Models\UserNotification::where('user_id', $user->id)->whereNull('read_at')->count() : 0,
                ] : null,
            ],
            // Global Settings - Available in all components via usePage().props.settings
            'settings' => get_public_settings(),
            // Dynamic Menus - Available via usePage().props.menus
            'menus' => [
                'header_main' => \App\Models\Menu::getMenuByLocation('header_main'),
                'footer_column_1' => \App\Models\Menu::getMenuByLocation('footer_column_1'),
                'footer_column_2' => \App\Models\Menu::getMenuByLocation('footer_column_2'),
                'footer_column_3' => \App\Models\Menu::getMenuByLocation('footer_column_3'),
            ],
        ];
    }
}
