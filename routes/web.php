<?php

// --- Use statements with namespaces ---
use App\Http\Controllers\Profile\CvBuilderController;
use App\Http\Controllers\Profile\DashboardController;
use App\Http\Controllers\Profile\EducationController;
use App\Http\Controllers\Profile\ExperienceController;
use App\Http\Controllers\Profile\PortfolioController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\SkillController;
use App\Http\Controllers\Profile\UserDocumentController;
use App\Http\Controllers\Public\CourseController as PublicCourseController;
use App\Http\Controllers\Public\JobController as PublicJobController;
use App\Http\Controllers\Public\UniversityController as PublicUniversityController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\JobCategoryController as AdminJobCategoryController;
use App\Http\Controllers\Admin\JobController as AdminJobController; // Assuming this is for Admin Job Postings
use App\Http\Controllers\Admin\UniversityController as AdminUniversityController;
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
})->name('welcome'); // Added name

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
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); // User deletion
    Route::patch('/profile/details', [ProfileController::class, 'updateDetails'])->name('profile.updateDetails'); // For profile model updates

    // Profile -> CV Builder (Web Interface & Download)
    Route::get('/profile/cv-builder', [CvBuilderController::class, 'show'])->name('profile.cv.show');
    Route::get('/profile/cv-builder/download', [CvBuilderController::class, 'download'])->name('profile.cv.download');

    // Profile API Resources (for AJAX updates within profile page)
    Route::prefix('api')->name('api.profile.')->group(function () {
        Route::apiResource('profile/educations', EducationController::class);
        Route::apiResource('profile/experiences', ExperienceController::class);
        Route::apiResource('profile/portfolios', PortfolioController::class);
        Route::apiResource('profile/documents', UserDocumentController::class)->only(['index', 'store', 'destroy']);
        Route::post('profile/skills', [SkillController::class, 'store'])->name('skills.store');
        Route::delete('profile/skills/{skill}', [SkillController::class, 'destroy'])->name('skills.destroy');
    });

    // --- Admin Routes ---
    Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // Admin Dashboard - Assuming you have an Admin Dashboard Controller now
        // If not, keep the Closure: Route::get('/dashboard', fn() => Inertia::render('Admin/Dashboard'))->name('dashboard');
        // If you created App\Http\Controllers\Admin\DashboardController:
         Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard'); // Use namespaced Admin Dashboard Controller

        // Admin Resource Controllers for CRUD
        Route::resource('universities', AdminUniversityController::class)->except(['show']);
        Route::resource('courses', AdminCourseController::class)->except(['show']);
        Route::resource('job-categories', AdminJobCategoryController::class)->except(['show']);
        Route::resource('jobs', AdminJobController::class)->except(['show']); // Assumes AdminJobController handles job postings
    });
});

// --- Auth Routes (Login, Register, Password Reset, etc.) ---
require __DIR__.'/auth.php';