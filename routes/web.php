<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request; // Keep this if needed elsewhere, though not used in the provided snippet

/*
|--------------------------------------------------------------------------
| Controller Imports
|--------------------------------------------------------------------------
*/

// --- CORE & PROFILE CONTROLLERS ---
use App\Http\Controllers\ProfileController; // Use the correct ProfileController
use App\Http\Controllers\GuidanceController; // For the user dashboard
use App\Http\Controllers\PublicPageController;

// --- ADMIN CONTROLLERS ---
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UniversityPageController;
use App\Http\Controllers\Admin\CoursePageController;
use App\Http\Controllers\Admin\SkillPageController; // For the 'Manage Skills' CRUD page
// API Controllers (Renamed to avoid conflict if web controllers with same name exist)
use App\Http\Controllers\Admin\UniversityController as AdminUniversityApiController;
use App\Http\Controllers\Admin\CourseController as AdminCourseApiController;
use App\Http\Controllers\Admin\JobCategoryController as AdminJobCategoryController;
use App\Http\Controllers\Admin\JobController as AdminJobController;
use App\Http\Controllers\Admin\CountryController as AdminCountryController;
use App\Http\Controllers\Admin\StateController as AdminStateController;
use App\Http\Controllers\Admin\CityController as AdminCityController;
use App\Http\Controllers\Admin\UserPageController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TouristVisaPageController;
use App\Http\Controllers\Admin\WorkVisaApplicationPageController as AdminWorkVisaPageController;
use App\Http\Controllers\Admin\StudentVisaApplicationPageController as AdminStudentVisaPageController;
use App\Http\Controllers\Admin\DegreeLevelController as AdminDegreeLevelController;
use App\Http\Controllers\Admin\FieldOfStudyController as AdminFieldOfStudyController;
use App\Http\Controllers\Admin\LanguageController as AdminLanguageController;
// Note: AdminSkillController is likely the API one, SkillPageController handles the web view
// use App\Http\Controllers\Admin\SkillController as AdminSkillApiController;

// --- PUBLIC CONTROLLERS (If you have them) ---
// use App\Http\Controllers\Public\CourseController as PublicCourseController;
// use App\Http\Controllers\Public\JobController as PublicJobController;
// use App\Http\Controllers\Public\UniversityController as PublicUniversityController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- WELCOME PAGE ---
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');


// --- PUBLIC ROUTES ---
// Pointing to methods that exist in your PublicPageController
Route::get('/universities', [PublicPageController::class, 'showUniversitySearch'])->name('public.universities.search'); // Corrected route name
Route::get('/courses', [PublicPageController::class, 'showCourseSearch'])->name('public.courses.search'); // Corrected route name
// Note: If you have dedicated public controllers, use them instead.
// Route::get('/universities', [PublicUniversityController::class, 'index'])->name('public.universities');
// Route::get('/courses', [PublicCourseController::class, 'index'])->name('public.courses');


/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // --- USER DASHBOARD ---
    Route::get('/dashboard', [GuidanceController::class, 'dashboard'])->name('dashboard'); // Corrected controller

    // --- PROFILE MANAGEMENT ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit'); // Corrected controller
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update'); // Corrected controller
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); // Corrected controller

    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'role:admin']) // Ensure 'role:admin' middleware exists and is correct
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get('/', fn() => redirect()->route('admin.dashboard'));
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

            // --- MAIN CRUD MODULES ---
            Route::resource('universities', UniversityPageController::class)->except(['show']); // Uses Page Controller
            Route::resource('courses', CoursePageController::class)->except(['show']); // Uses Page Controller
            Route::resource('job-categories', AdminJobCategoryController::class)->except(['show']);
            Route::resource('jobs', AdminJobController::class)->except(['show']);

            // --- GEOGRAPHY MODULE ---
            Route::resource('countries', AdminCountryController::class)->except(['show']);
            Route::resource('states', AdminStateController::class)->except(['show']);
            Route::resource('cities', AdminCityController::class)->except(['show']);

            // --- PRE-BUILT DATA MODULE ---
            Route::resource('degree-levels', AdminDegreeLevelController::class)->except(['show']);
            Route::resource('fields-of-study', AdminFieldOfStudyController::class)->except(['show']);
            Route::resource('languages', AdminLanguageController::class)->except(['show']);
            // Uses SkillPageController for the web UI as defined earlier
            Route::resource('skills', SkillPageController::class)->except(['show']);

            // --- USERS ---
            Route::resource('users', UserPageController::class); // Uses Page Controller


            // --- VISA MANAGEMENT ---
            Route::resource('tourist-visas', AdminTouristVisaPageController::class)->only(['index']);
            Route::resource('work-visas', AdminWorkVisaPageController::class)->only(['index']);
            Route::resource('student-visas', AdminStudentVisaPageController::class)->only(['index']);

            // --- SETTINGS ---
            Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
            Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
        });
});

// --- AUTH ROUTES ---
require __DIR__ . '/auth.php'; // Standard Breeze auth routes