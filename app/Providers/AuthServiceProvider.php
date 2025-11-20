<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('impersonate', function (User $admin, User $target) {
            if (! $admin->role || strtolower($admin->role->slug) !== 'admin') {
                return false;
            }
            if ($target->role && strtolower($target->role->slug) === 'admin' && $target->id !== $admin->id) {
                return false; // cannot impersonate other admins
            }
            return true;
        });
    }
}
