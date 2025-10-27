<?php

// --- CONTROLLER IMPORTS (All controllers) ---
use App\Http\Controllers\Profile\CvBuilderController;
use App\Http\Controllers\Profile\DashboardController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\WorkVisaApplicationPageController as ProfileWorkVisaPageController;
use App\Http\Controllers\Profile\StudentVisaApplicationPageController as ProfileStudentVisaPageController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UniversityPageController;
use App\Http\Controllers\Admin\CoursePageController;
use App\Http\Controllers\Admin\JobCategoryPageController; // <-- Already imported
use App\Http\Controllers\Admin\JobPostingPageController;
use App\Http\Controllers\Admin\AirlinePageController;
use App\Http\Controllers\Admin\AirportPageController;
use App\Http\Controllers\Admin\FlightPageController;
use App\Http\Controllers\Admin\UserPageController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TouristVisaPageController;
use App\Http\Controllers\Admin\WorkVisaApplicationPageController as AdminWorkVisaPageController;
use App\Http\Controllers\Admin\StudentVisaApplicationPageController as AdminStudentVisaPageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\GuidanceController;

// --- Location & Skill Page Controller Imports ---
use App\Http\Controllers\Admin\CountryPageController;
use App\Http\Controllers\Admin\StatePageController;
use App\Http\Controllers\Admin\CityPageController;
use App\Http\Controllers\Admin\SkillPageController;
// ---

// --- FACADES & MODELS ---
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Models\Country;
use Illuminate\Http\Request;

// --- PUBLIC PAGES ---
Route::get('/', [PublicPageController::class, 'welcome'])->name('welcome');
Route::get('/find-universities', [PublicPageController::class, 'showUniversitySearch'])->name('public.universities.search');
Route::get('/university/{university}', [PublicPageController::class, 'showUniversityDetail'])->name('public.universities.show');
Route::get('/find-courses', [PublicPageController::class, 'showCourseSearch'])->name('public.courses.search');
Route::get('/course/{course}', [PublicPageController::class, 'showCourseDetail'])->name('public.courses.show');
Route::get('/find-jobs', [PublicPageController::class, 'showJobSearch'])->name('public.jobs.search');
// Route::get('/job/{jobPosting}', [PublicPageController::class, 'jobDetail'])->name('public.job.detail');


// --- USER DASHBOARD & PROFILE ---
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // CV Builder Routes
    Route::get('/profile/cv-builder', [CvBuilderController::class, 'show'])->name('profile.cv.show');
    Route::get('/profile/cv-builder/download', [CvBuilderController::class, 'download'])->name('profile.cv.download');
    // Guidance Route
    Route::get('/guidance', [GuidanceController::class, 'dashboard'])->name('guidance.dashboard');
});

// --- ADMIN PANEL ROUTES ---
Route::middleware(['auth', 'verified', 'role:admin']) // Ensure 'role:admin' middleware works
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', fn() => Redirect::route('admin.dashboard'));
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Resource Controllers for Admin Pages (Web Interface)
        Route::resource('universities', UniversityPageController::class)->except(['show']); // We will refactor this later
        Route::resource('courses', CoursePageController::class)->except(['show']); // We will refactor this later
        
        // --- MODIFIED JOB CATEGORY ROUTE ---
        Route::resource('job-categories', JobCategoryPageController::class)->only(['index'])->names([
             'index' => 'job-categories.index', // Explicitly name the index route
        ]);
        // --- END MODIFIED JOB CATEGORY ROUTE ---

        Route::resource('job-postings', JobPostingPageController::class)->except(['show']); // We will refactor this later
        
        Route::resource('users', UserPageController::class)->only(['index', 'create', 'edit'])->names([
             'index' => 'users.index',
             'create' => 'users.create',
             'edit' => 'users.edit',
        ]);

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

        // Location Management
        Route::resource('countries', CountryPageController::class)->only(['index'])->names(['index' => 'countries.index']);
        Route::resource('states', StatePageController::class)->only(['index'])->names(['index' => 'states.index']);
        Route::resource('cities', CityPageController::class)->only(['index'])->names(['index' => 'cities.index']);

        // Skill Management
        Route::resource('skills', SkillPageController::class)->only(['index'])->names([
             'index' => 'skills.index',
        ]);

    });

// --- AUTH ROUTES ---
require __DIR__ . '/auth.php';