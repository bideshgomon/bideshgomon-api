<?php

// --- Use statements with namespaces ---
use App\Http\Controllers\Profile\CvBuilderController;
use App\Http\Controllers\Profile\DashboardController;
// REMOVED: Education, Experience, Portfolio, Skill, UserDocument controllers
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Public\CourseController as PublicCourseController;
use App\Http\Controllers\Public\JobController as PublicJobController;
use App\Http\Controllers\Public\UniversityController as PublicUniversityController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\JobCategoryController as AdminJobCategoryController;
use App\Http\Controllers\Admin\JobController as AdminJobController;
use App\Http\Controllers\Admin\UniversityController as AdminUniversityController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController; // Added alias
// --- Framework classes ---
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Welcome Route ---
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

// --- Public Facing Routes ---
Route::get('/jobs', [PublicJobController::class, 'index'])->name('jobs.index');
Route::get('/universities', [PublicUniversityController::class, 'index'])->name('universities.index');
Route::get('/universities/{university}', [PublicUniversityController::class, 'show'])->name('universities.show');
Route::get('/courses', [PublicCourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course}', [PublicCourseController::class, 'show'])->name('courses.show');


// --- Authenticated User Routes ---
Route::middleware(['auth', 'verified'])->group(function () {
    // User Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management (Web Interface)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update'); // For user model updates
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // NOTE: 'profile.updateDetails' was removed as 'profile.update' handles this
    // via the ProfileUpdateRequest and ProfileController update method logic we added.

    // Profile -> CV Builder (Web Interface & Download)
    Route::get('/profile/cv-builder', [CvBuilderController::class, 'show'])->name('profile.cv.show');
    Route::get('/profile/cv-builder/download', [CvBuilderController::class, 'download'])->name('profile.cv.download');

    // --- ADMIN ROUTES ---
    Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
         Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Admin Resource Controllers for CRUD
        Route::resource('universities', AdminUniversityController::class)->except(['show']);
        Route::resource('courses', AdminCourseController::class)->except(['show']);
        Route::resource('job-categories', AdminJobCategoryController::class)->except(['show']);
        Route::resource('jobs', AdminJobController::class)->except(['show']);
    });
});

// --- Auth Routes (Login, Register, Password Reset, etc.) ---
require __DIR__.'/auth.php';