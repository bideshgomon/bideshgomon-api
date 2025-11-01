<?php

// --- Use statements with namespaces ---
use App\Http\Controllers\Admin\CityPageController;
use App\Http\Controllers\Admin\CountryPageController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\JobCategoryController as AdminJobCategoryController;
use App\Http\Controllers\Admin\JobController as AdminJobController;
use App\Http\Controllers\Admin\StatePageController;
use App\Http\Controllers\Admin\UniversityController as AdminUniversityController;
use App\Http\Controllers\Profile\CvBuilderController;
use App\Http\Controllers\Profile\DashboardController;
use App\Http\Controllers\Profile\EducationController;
use App\Http\Controllers\Profile\ExperienceController;
use App\Http\Controllers\Profile\PortfolioController;
use App\Http\Controllers\Profile\SkillController;
use App\Http\Controllers\Profile\UserDocumentController;
use App\Http\Controllers\ProfileController;
// --- FIXED: Import the new CityPageController ---
use App\Http\Controllers\PublicPageController;
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
Route::get('/jobs', [PublicPageController::class, 'showJobSearch'])->name('jobs.index');
Route::get('/universities', [PublicPageController::class, 'showUniversitySearch'])->name('universities.index');
Route::get('/universities/{university}', [PublicPageController::class, 'showUniversityDetail'])->name('universities.show');
Route::get('/courses', [PublicPageController::class, 'showCourseSearch'])->name('courses.index');
Route::get('/courses/{course}', [PublicPageController::class, 'showCourseDetail'])->name('courses.show');

// --- Authenticated User Routes ---
Route::middleware(['auth', 'verified'])->group(function () {

    // --- Role-Specific Dashboards ---
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:user')
        ->name('dashboard');
    Route::get('/agency/dashboard', function () {
        return Inertia::render('Agency/Dashboard');
    })->middleware('role:agency')
        ->name('agency.dashboard');
    Route::get('/consultant/dashboard', function () {
        return Inertia::render('Consultant/Dashboard');
    })->middleware('role:consultant')
        ->name('consultant.dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/details', [ProfileController::class, 'updateDetails'])->name('profile.updateDetails');

    // CV Builder
    Route::get('/profile/cv-builder', [CvBuilderController::class, 'show'])->name('profile.cv.show');
    Route::get('/profile/cv-builder/download', [CvBuilderController::class, 'download'])->name('profile.cv.download');

    // Profile API Resources
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
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Admin Resource Controllers
        Route::resource('universities', AdminUniversityController::class)->except(['show']);
        Route::resource('courses', AdminCourseController::class)->except(['show']);
        Route::resource('job-categories', AdminJobCategoryController::class)->except(['show']);
        Route::resource('jobs', AdminJobController::class)->except(['show']);

        // Page-Rendering Routes
        Route::get('countries', [CountryPageController::class, 'index'])->name('countries.index');
        Route::get('states', [StatePageController::class, 'index'])->name('states.index');

        // --- FIXED: Add the 'cities' route to fix the Ziggy error ---
        Route::get('cities', [CityPageController::class, 'index'])->name('cities.index');
        // --- End of Fix ---
    });
});

// --- Auth Routes ---
require __DIR__.'/auth.php';
