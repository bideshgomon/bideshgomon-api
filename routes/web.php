<?php

// --- CONTROLLER IMPORTS (Keep all your existing ones) ---
use App\Http\Controllers\Profile\CvBuilderController;
use App\Http\Controllers\Profile\DashboardController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UniversityPageController;
use App\Http\Controllers\Admin\CoursePageController;
use App\Http\Controllers\Admin\JobCategoryPageController;
use App\Http\Controllers\Admin\JobPostingPageController;
use App\Http\Controllers\Admin\AirlinePageController;
use App\Http\Controllers\Admin\AirportPageController;
use App\Http\Controllers\Admin\FlightPageController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

// --- PUBLIC PAGES ---
Route::get('/', [PublicPageController::class, 'welcome'])->name('welcome');
Route::get('/find-universities', [PublicPageController::class, 'universities'])->name('public.universities');
Route::get('/university/{university}', [PublicPageController::class, 'universityDetail'])->name('public.universities.show');
Route::get('/find-courses', [PublicPageController::class, 'courses'])->name('public.courses');
Route::get('/course/{course}', [PublicPageController::class, 'courseDetail'])->name('public.courses.show');
Route::get('/find-jobs', [PublicPageController::class, 'jobs'])->name('public.jobs');
Route::get('/job/{jobPosting}', [PublicPageController::class, 'jobDetail'])->name('public.job.detail');

// --- USER DASHBOARD & PROFILE ---
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Profile update/delete are handled in auth.php
    Route::get('/profile/cv-builder', [CvBuilderController::class, 'show'])->name('profile.cv.show');
    Route::get('/profile/cv-builder/download', [CvBuilderController::class, 'download'])->name('profile.cv.download');
});

// --- ADMIN PANEL ROUTES ---
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', fn() => Redirect::route('admin.dashboard'));
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Use resource controllers for standard CRUD operations
        Route::resource('universities', UniversityPageController::class)->except(['show']);
        Route::resource('courses', CoursePageController::class)->except(['show']);
        Route::resource('job-categories', JobCategoryPageController::class)->except(['show']);
        Route::resource('job-postings', JobPostingPageController::class)->except(['show']);
        Route::resource('airlines', AirlinePageController::class)->except(['show']); // <-- THIS HANDLES ALL AIRLINE ACTIONS
        Route::resource('airports', AirportPageController::class)->except(['show']);
        Route::resource('flights', FlightPageController::class)->except(['show']);

        // Users & Settings
        Route::resource('users', UserController::class)->except(['create', 'store', 'show']); // Assuming create/store aren't needed here
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

// --- AUTH ROUTES ---
require __DIR__ . '/auth.php';