<?php

// --- Use statements with namespaces ---
use App\Http\Controllers\Profile\CvBuilderController;
use App\Http\Controllers\Profile\DashboardController;
use App\Http\Controllers\ProfileController;

// --- ADD CONTROLLER IMPORTS FOR YOUR API ENDPOINTS ---
// NOTE: You will need to create these controllers and methods or adjust the paths
// to match your existing controllers.
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\EducationController;
use App\Http\Controllers\Api\ExperienceController;
use App\Http\Controllers\Api\LicenseController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\PrebuiltDataController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\TechnicalEducationController;
// --- END OF NEW IMPORTS ---

use App\Http\Controllers\Public\CourseController as PublicCourseController;
use App\Http\Controllers\Public\JobController as PublicJobController;
use App\Http\Controllers\Public\UniversityController as PublicUniversityController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\JobCategoryController as AdminJobCategoryController;
use App\Http\Controllers\Admin\JobController as AdminJobController;
use App\Http\Controllers\Admin\UniversityController as AdminUniversityController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
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

// --- Public Facing Content Routes ---
Route::prefix('public')->name('public.')->group(function () {
    Route::get('/universities', [PublicUniversityController::class, 'index'])->name('universities.index');
    Route::get('/universities/{university}', [PublicUniversityController::class, 'show'])->name('universities.show');
    Route::get('/courses', [PublicCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [PublicCourseController::class, 'show'])->name('courses.show');
    Route::get('/jobs', [PublicJobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/{job}', [PublicJobController::class, 'show'])->name('jobs.show');
});

// --- Authenticated User Routes ---
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Edit (Web Interface)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Profile -> CV Builder (Web Interface & Download)
    Route::get('/profile/cv-builder', [CvBuilderController::class, 'show'])->name('profile.cv.show');
    Route::get('/profile/cv-builder/download', [CvBuilderController::class, 'download'])->name('profile.cv.download');

    //======================================================================
    // --- API ENDPOINTS FOR PROFILE COMPONENTS (NEWLY ADDED SECTION) ---
    //======================================================================
    // These routes provide data to the Vue components on the profile page.
    // They are protected by the same 'auth' middleware, so they use the
    // user's web session for authentication.

    // Education
    Route::apiResource('profile/education', EducationController::class)->names('profile.education');

    // Work Experience
    Route::apiResource('profile/work-experience', ExperienceController::class)->names('profile.work-experience');

    // Skills & Prebuilt Data
    Route::get('api/prebuilt-data', [PrebuiltDataController::class, 'getSkillsAndLevels'])->name('api.prebuilt-data');
    Route::get('profile/skills', [SkillController::class, 'index'])->name('profile.skills.index');
    Route::post('profile/skills', [SkillController::class, 'sync'])->name('profile.skills.sync');

    // Portfolio
    Route::apiResource('api/profile/portfolio', PortfolioController::class)->names('api.profile.portfolio');

    // Documents
    Route::get('api/document-types', [DocumentController::class, 'getDocumentTypes'])->name('api.document-types.index');
    Route::apiResource('api/profile/documents', DocumentController::class)->names('api.profile.documents');
    
    // Technical Education
    Route::apiResource('api/profile/technical-education', TechnicalEducationController::class)->names('api.profile.technical-education');

    // Licenses
    Route::apiResource('api/profile/licenses', LicenseController::class)->names('api.profile.licenses');

    // Memberships
    Route::apiResource('api/profile/memberships', MembershipController::class)->names('api.profile.memberships');


    // --- ADMIN ROUTES ---
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
         Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('universities', AdminUniversityController::class)->except(['show']);
        Route::resource('courses', AdminCourseController::class)->except(['show']);
        Route::resource('job-categories', AdminJobCategoryController::class)->except(['show']);
        Route::resource('jobs', AdminJobController::class)->except(['show']);
    });
});

// --- Auth Routes (Login, Register, Password Reset, etc.) ---
require __DIR__.'/auth.php';