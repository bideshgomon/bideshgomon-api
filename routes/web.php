<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Controller Imports
|--------------------------------------------------------------------------
*/

// --- PROFILE CONTROLLERS ---
use App\Http\Controllers\Profile\DashboardController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\CvBuilderController;
use App\Http\Controllers\Profile\EducationController;
use App\Http\Controllers\Profile\ExperienceController;
use App\Http\Controllers\Profile\PortfolioController;
use App\Http\Controllers\Profile\UserDocumentController;
use App\Http\Controllers\Profile\SkillController;
use App\Http\Controllers\Profile\WorkVisaApplicationPageController as ProfileWorkVisaPageController;
use App\Http\Controllers\Profile\StudentVisaApplicationPageController as ProfileStudentVisaPageController;

// --- PUBLIC CONTROLLERS ---
use App\Http\Controllers\Public\CourseController as PublicCourseController;
use App\Http\Controllers\Public\JobController as PublicJobController;
use App\Http\Controllers\Public\UniversityController as PublicUniversityController;
use App\Http\Controllers\PublicPageController;

// --- ADMIN CONTROLLERS ---
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UniversityController as AdminUniversityController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\JobCategoryController as AdminJobCategoryController;
use App\Http\Controllers\Admin\JobController as AdminJobController;
use App\Http\Controllers\Admin\CountryController as AdminCountryController;
use App\Http\Controllers\Admin\StateController as AdminStateController;
use App\Http\Controllers\Admin\CityController as AdminCityController;
use App\Http\Controllers\Admin\SkillPageController;
use App\Http\Controllers\Admin\UserPageController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TouristVisaPageController;
use App\Http\Controllers\Admin\WorkVisaApplicationPageController as AdminWorkVisaPageController;
use App\Http\Controllers\Admin\StudentVisaApplicationPageController as AdminStudentVisaPageController;
use App\Http\Controllers\Admin\DegreeLevelController as AdminDegreeLevelController; 
use App\Http\Controllers\Admin\FieldOfStudyController as AdminFieldOfStudyController; 
use App\Http\Controllers\Admin\LanguageController as AdminLanguageController; 
use App\Http\Controllers\Admin\SkillController as AdminSkillController;

// --- EXTRA CONTROLLERS ---
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\GuidanceController;

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

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/jobs', [PublicJobController::class, 'index'])->name('jobs.index');
Route::get('/universities', [PublicUniversityController::class, 'index'])->name('universities.index');
Route::get('/universities/{university}', [PublicUniversityController::class, 'show'])->name('universities.show');
Route::get('/courses', [PublicCourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course}', [PublicCourseController::class, 'show'])->name('courses.show');

// Legacy public routes (for compatibility)
Route::get('/find-universities', [PublicPageController::class, 'searchUniversities'])->name('public.universities.search');
Route::get('/find-courses', [PublicPageController::class, 'searchCourses'])->name('public.courses.search');
Route::get('/find-jobs', [PublicPageController::class, 'searchJobs'])->name('public.jobs.search');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // --- USER DASHBOARD ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- PROFILE MANAGEMENT ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/details', [ProfileController::class, 'updateDetails'])->name('profile.updateDetails');

    // --- CV BUILDER ---
    Route::get('/profile/cv-builder', [CvBuilderController::class, 'show'])->name('profile.cv.show');
    Route::get('/profile/cv-builder/download', [CvBuilderController::class, 'download'])->name('profile.cv.download');

    // --- PROFILE DATA API (AJAX) ---
    Route::prefix('api')->name('api.profile.')->group(function () {
        Route::apiResource('profile/educations', EducationController::class);
        Route::apiResource('profile/experiences', ExperienceController::class);
        Route::apiResource('profile/portfolios', PortfolioController::class);
        Route::apiResource('profile/documents', UserDocumentController::class)->only(['index', 'store', 'destroy']);
        Route::post('profile/skills', [SkillController::class, 'store'])->name('skills.store');
        Route::delete('profile/skills/{skill}', [SkillController::class, 'destroy'])->name('skills.destroy');
    });

    // --- AI GUIDANCE DASHBOARD ---
    Route::get('/guidance', [GuidanceController::class, 'dashboard'])->name('guidance.dashboard');

    // --- VISA APPLICATION PAGES ---
    Route::get('/work-visa-application', [ProfileWorkVisaPageController::class, 'show'])->name('work-visa.show');
    Route::get('/student-visa-application', [ProfileStudentVisaPageController::class, 'show'])->name('student-visa.show');

    // --- PAYMENT GATEWAY ---
    Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');

    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'role:admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // Redirect /admin → /admin/dashboard
            Route::get('/', fn() => redirect()->route('admin.dashboard'));

            // --- DASHBOARD ---
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

            // --- MAIN CRUD MODULES ---
            Route::resource('universities', AdminUniversityController::class)->except(['show']);
            Route::resource('courses', AdminCourseController::class)->except(['show']);
            Route::resource('job-categories', AdminJobCategoryController::class)->except(['show']);
            Route::resource('jobs', AdminJobController::class)->except(['show']);

            // --- GEOGRAPHY MODULE ---
            Route::post('countries/bulk-upload', [AdminCountryController::class, 'bulkUpload'])->name('countries.bulk-upload');
            Route::resource('countries', AdminCountryController::class)->except(['show']);
            Route::resource('states', AdminStateController::class)->except(['show']);
            Route::get('cities/get-states', [AdminCityController::class, 'getStatesForCountry'])->name('cities.getStates');
            Route::resource('cities', AdminCityController::class)->except(['show']);

            // --- PRE-BUILT DATA MODULE ---
            Route::resource('degree-levels', AdminDegreeLevelController::class)->except(['show']); 
            Route::resource('fields-of-study', AdminFieldOfStudyController::class)->except(['show']); 
            Route::resource('languages', AdminLanguageController::class)->except(['show']);
            Route::resource('skills', AdminSkillController::class)->except(['show']);
            // Add future pre-built datasets here (fields-of-study, skills, etc.)

            // --- SKILLS & USERS ---
            Route::resource('skills', SkillPageController::class)->only(['index'])->names(['index' => 'skills.index']);
            Route::resource('users', UserPageController::class)->only(['index', 'create', 'edit'])->names([
                'index' => 'users.index',
                'create' => 'users.create',
                'edit' => 'users.edit',
            ]);

            // --- VISA MANAGEMENT ---
            Route::resource('tourist-visas', TouristVisaPageController::class)->only(['index']);
            Route::resource('work-visas', AdminWorkVisaPageController::class)->only(['index']);
            Route::resource('student-visas', AdminStudentVisaPageController::class)->only(['index']);

            // --- SETTINGS ---
            Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
            Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        });
});

// --- AUTH ROUTES ---
require __DIR__ . '/auth.php';
