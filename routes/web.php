<?php

// --- CONTROLLER IMPORTS (All controllers) ---
use App\Http\Controllers\Profile\CvBuilderController;
use App\Http\Controllers\Profile\DashboardController; // <-- Ensure this is imported
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\WorkVisaApplicationPageController as ProfileWorkVisaPageController;
use App\Http\Controllers\Profile\StudentVisaApplicationPageController as ProfileStudentVisaPageController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UniversityPageController;
use App\Http\Controllers\Admin\CoursePageController;
use App\Http\Controllers\Admin\JobCategoryPageController;
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

// --- Laravel & Inertia ---
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- PUBLIC ROUTES ---
Route::get('/', [PublicPageController::class, 'welcome'])->name('welcome');
Route::get('/find-universities', [PublicPageController::class, 'searchUniversities'])->name('public.universities.search');
Route::get('/university/{university}', [PublicPageController::class, 'showUniversity'])->name('public.universities.show');
Route::get('/find-courses', [PublicPageController::class, 'searchCourses'])->name('public.courses.search');
Route::get('/course/{course}', [PublicPageController::class, 'showCourse'])->name('public.courses.show');
Route::get('/find-jobs', [PublicPageController::class, 'searchJobs'])->name('public.jobs.search'); // Assuming searchJobs exists


// --- AUTHENTICATED USER ROUTES ---
Route::middleware(['auth', 'verified'])->group(function () {
    // --- CORRECTED DASHBOARD ROUTE ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard'); // <-- FIXED LINE (using index method)
    // --- END CORRECTION ---

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CV Builder
    Route::get('/profile/cv-builder', [CvBuilderController::class, 'show'])->name('profile.cv.show');
    Route::get('/profile/cv-builder/download', [CvBuilderController::class, 'downloadCv'])->name('profile.cv.download');

    // AI Guidance
    Route::get('/guidance', [GuidanceController::class, 'dashboard'])->name('guidance.dashboard');

    // Application Pages (Specific Controllers)
    Route::get('/work-visa-application', [ProfileWorkVisaPageController::class, 'show'])->name('work-visa.show');
    Route::get('/student-visa-application', [ProfileStudentVisaPageController::class, 'show'])->name('student-visa.show');

    // Payment Routes (Example)
    Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');

});


// --- ADMIN ROUTES ---
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', function () { // Redirect /admin to /admin/dashboard
            return redirect()->route('admin.dashboard');
        });
        Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');

        // CRUD for Universities, Courses, etc.
        Route::resource('universities', UniversityPageController::class)->except(['show']);
        Route::resource('courses', CoursePageController::class)->except(['show']);

        // Job Categories (Index only)
        Route::resource('job-categories', JobCategoryPageController::class)->only(['index'])->names([
             'index' => 'job-categories.index',
        ]);

        Route::resource('job-postings', JobPostingPageController::class)->except(['show']);

        // Users (Index, Create, Edit only)
        Route::resource('users', UserPageController::class)->only(['index', 'create', 'edit'])->names([
             'index' => 'users.index',
             'create' => 'users.create',
             'edit' => 'users.edit',
        ]);

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

        // Location Management (Index only)
        Route::resource('countries', CountryPageController::class)->only(['index'])->names(['index' => 'countries.index']);
        Route::resource('states', StatePageController::class)->only(['index'])->names(['index' => 'states.index']);
        Route::resource('cities', CityPageController::class)->only(['index'])->names(['index' => 'cities.index']);

        // Skill Management (Index only)
        Route::resource('skills', SkillPageController::class)->only(['index'])->names([
             'index' => 'skills.index',
        ]);

    });

// --- AUTH ROUTES ---
require __DIR__ . '/auth.php';