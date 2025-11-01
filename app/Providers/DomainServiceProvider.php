<?php

// app/Providers/DomainServiceProvider.php

namespace App\Providers;

use App\Services\Admin\DashboardService;
use App\Services\Admin\JobPostingService;
// We are NOT adding UniversityService here, as it does not exist.
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // --- Admin Services ---
        $this->app->singleton(DashboardService::class);
        $this->app->singleton(JobPostingService::class);

        // When we eventually create UniversityService,
        // we will add it here.
        // $this->app->singleton(UniversityService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
