<?php

// --- Controller Imports ---
use App\Http\Controllers\Profile\CvBuilderController;
use App\Http\Controllers\Profile\DashboardController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Public\CourseController as PublicCourseController;
use App\Http\Controllers\Public\JobController as PublicJobController;
use App\Http\Controllers\Public\UniversityController as PublicUniversityController;
use App\Http\Controllers\Public\ConsultationBookingController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\JobCategoryController as AdminJobCategoryController;
use App\Http\Controllers\Admin\JobController as AdminJobController;
use App\Http\Controllers\Admin\UniversityController as AdminUniversityController;
use App\Http\Controllers\Admin\ConsultationServiceController;
use App\Http\Controllers\Admin\ConsultantController;
// --- Other Imports ---
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

// Public Facing Routes
Route::get('/jobs', [PublicJobController::class, 'index'])->name('jobs.index');
Route::get('/universities', [PublicUniversityController::class, 'index'])->name('universities.index');
Route::get('/universities/{university}', [PublicUniversityController::class, 'show'])->name('universities.show');
Route::get('/courses', [PublicCourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course}', [PublicCourseController::class, 'show'])->name('courses.show');
Route::get('/consultations', [ConsultationBookingController::class, 'index'])->name('consultations.index');
Route::get('/consultations/book/{service}', [ConsultationBookingController::class, 'showBookingForm'])->name('consultations.book.form');

// No payment routes needed here initially for SSLCommerz redirect

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- ADDED GEMINI TEST ROUTE ---
    Route::get('/test-gemini', [DashboardController::class, 'testGemini'])->name('test.gemini');
    // -------------------------------

    // Profile Management, CV Builder, etc.
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/details', [ProfileController::class, 'updateDetails'])->name('profile.updateDetails');
    Route::get('/profile/cv-builder', [CvBuilderController::class, 'show'])->name('profile.cv.show');
    Route::get('/profile/cv-builder/download', [CvBuilderController::class, 'download'])->name('profile.cv.download');

    // Consultation Booking Submission
    Route::post('/consultations/book', [ConsultationBookingController::class, 'storeAppointment'])->name('consultations.book.store');

    // Admin Routes
    Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', fn() => Redirect::route('admin.dashboard'));
        Route::get('/dashboard', fn() => Inertia::render('Admin/Dashboard'))->name('dashboard');
        Route::resource('universities', AdminUniversityController::class)->except(['show']);
        Route::resource('courses', AdminCourseController::class)->except(['show']);
        Route::resource('job-categories', AdminJobCategoryController::class)->except(['show']);
        Route::resource('jobs', AdminJobController::class)->except(['show']);
        Route::resource('consultation-services', ConsultationServiceController::class)->except(['show']);
        Route::resource('consultants', ConsultantController::class)->only(['index', 'edit', 'update']);
    });
});

require __DIR__.'/auth.php';