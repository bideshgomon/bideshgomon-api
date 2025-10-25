<?php

use App\Http\Controllers\Admin\CoursePageController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\JobCategoryPageController;
use App\Http\Controllers\Admin\JobPostingPageController;
use App\Http\Controllers\Admin\UniversityPageController;
use App\Http\Controllers\Profile\CvBuilderController;
use App\Http\Controllers\Profile\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPageController;
// Air Ticketing Controllers
use App\Http\Controllers\Admin\AirlinePageController;
use App\Http\Controllers\Admin\AirportPageController;
use App\Http\Controllers\Admin\FlightPageController;
// User & Settings Controllers
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

// --- Public Pages ---
Route::get('/', [PublicPageController::class, 'welcome'])->name('welcome');
Route::get('/find-universities', [PublicPageController::class, 'universities'])->name('public.universities');
// *** CORRECTED ROUTE NAME ***
Route::get('/university/{university}', [PublicPageController::class, 'universityDetail'])->name('public.universities.show');
Route::get('/find-courses', [PublicPageController::class, 'courses'])->name('public.courses');
// *** CORRECTED ROUTE NAME ***
Route::get('/course/{course}', [PublicPageController::class, 'courseDetail'])->name('public.courses.show');
Route::get('/find-jobs', [PublicPageController::class, 'jobs'])->name('public.jobs');
Route::get('/job/{jobPosting}', [PublicPageController::class, 'jobDetail'])->name('public.job.detail');

// --- User Dashboard & Profile (Authenticated) ---
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/cv-builder', [CvBuilderController::class, 'show'])->name('profile.cv.show');
    Route::get('/profile/cv-builder/download', [CvBuilderController::class, 'download'])->name('profile.cv.download');
});

// --- Admin Panel Routes ---
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn() => Redirect::route('admin.dashboard'));
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Universities
    Route::get('/universities', [UniversityPageController::class, 'index'])->name('universities.index');
    Route::get('/universities/create', [UniversityPageController::class, 'create'])->name('universities.create');
    Route::get('/universities/{university}/edit', [UniversityPageController::class, 'edit'])->name('universities.edit');

    // Courses
    Route::get('/courses', [CoursePageController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [CoursePageController::class, 'create'])->name('courses.create');
    Route::get('/courses/{course}/edit', [CoursePageController::class, 'edit'])->name('courses.edit');

    // Job Categories
    Route::get('/job-categories', [JobCategoryPageController::class, 'index'])->name('job-categories.index');
    Route::get('/job-categories/create', [JobCategoryPageController::class, 'create'])->name('job-categories.create');
    Route::get('/job-categories/{jobCategory}/edit', [JobCategoryPageController::class, 'edit'])->name('job-categories.edit');

    // Job Postings
    Route::get('/job-postings', [JobPostingPageController::class, 'index'])->name('job-postings.index');
    Route::get('/job-postings/create', [JobPostingPageController::class, 'create'])->name('job-postings.create');
    Route::get('/job-postings/{jobPosting}/edit', [JobPostingPageController::class, 'edit'])->name('job-postings.edit');

    // Air Ticketing
    Route::get('/airlines', [AirlinePageController::class, 'index'])->name('airlines.index');
    Route::get('/airlines/create', [AirlinePageController::class, 'create'])->name('airlines.create');
    Route::get('/airlines/{airline}/edit', [AirlinePageController::class, 'edit'])->name('airlines.edit');
    Route::get('/airports', [AirportPageController::class, 'index'])->name('airports.index');
    Route::get('/airports/create', [AirportPageController::class, 'create'])->name('airports.create');
    Route::get('/airports/{airport}/edit', [AirportPageController::class, 'edit'])->name('airports.edit');
    Route::get('/flights', [FlightPageController::class, 'index'])->name('flights.index');
    Route::get('/flights/create', [FlightPageController::class, 'create'])->name('flights.create');
    Route::get('/flights/{flight}/edit', [FlightPageController::class, 'edit'])->name('flights.edit');

    // Users & Settings
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
});

require __DIR__ . '/auth.php';