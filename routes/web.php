<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPageController; // Make sure this is imported
use App\Http\Controllers\Admin\UniversityPageController;
use App\Http\Controllers\Admin\CoursePageController;
use App\Http\Controllers\PageController; // For Dashboard
use App\Http\Controllers\GuidanceController; // For Guidance
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public Pages
Route::get('/', [PublicPageController::class, 'welcome'])->name('welcome');

// **** THIS IS THE LINE TO FIX ****
Route::get('/find-universities', [PublicPageController::class, 'searchUniversities']) // Changed from showUniversitySearch
    ->name('public.universities.search');
// **********************************

Route::get('/university/{university}', [PublicPageController::class, 'showUniversity'])
    ->name('public.universities.show');

Route::get('/find-courses', [PublicPageController::class, 'searchCourses'])
    ->name('public.courses.search');

Route::get('/course/{course}', [PublicPageController::class, 'showCourse'])
    ->name('public.courses.show');

// Example Job Search Route (adjust as needed)
Route::get('/find-jobs', function () {
    // Replace with a controller method later
    return Inertia::render('Public/Jobs');
})->name('public.jobs.search');


// Authenticated User Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CV Builder Example Routes
    Route::get('/profile/cv-builder', [ProfileController::class, 'showCvBuilder'])->name('profile.cv.show');
    Route::get('/profile/cv-builder/download', [ProfileController::class, 'downloadCv'])->name('profile.cv.download');

    // AI Guidance Dashboard
    Route::get('/guidance', [GuidanceController::class, 'dashboard'])->name('guidance.dashboard');

});

// Admin Routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () { // Redirect /admin to /admin/dashboard
        return redirect()->route('admin.dashboard');
    });
    Route::get('/dashboard', [PageController::class, 'adminDashboard'])->name('dashboard');

    // University Admin Pages (Using dedicated Page Controllers)
    Route::get('/universities', [UniversityPageController::class, 'index'])->name('universities.index');
    Route::get('/universities/create', [UniversityPageController::class, 'create'])->name('universities.create');
    Route::post('/universities', [UniversityPageController::class, 'store'])->name('universities.store');
    Route::get('/universities/{university}/edit', [UniversityPageController::class, 'edit'])->name('universities.edit');
    Route::put('/universities/{university}', [UniversityPageController::class, 'update'])->name('universities.update'); // Changed PATCH to PUT for convention
    Route::delete('/universities/{university}', [UniversityPageController::class, 'destroy'])->name('universities.destroy');


    // Course Admin Pages (Using dedicated Page Controllers)
    Route::get('/courses', [CoursePageController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [CoursePageController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CoursePageController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}/edit', [CoursePageController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [CoursePageController::class, 'update'])->name('courses.update'); // Changed PATCH to PUT for convention
    Route::delete('/courses/{course}', [CoursePageController::class, 'destroy'])->name('courses.destroy');

    // Add other Admin routes (Users, Settings, Countries, etc.) here

});


require __DIR__.'/auth.php';

// Fallback storage link route (if needed, ensure 'php artisan storage:link' was run)
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path("app/public/{$path}");
    if (!file_exists($filePath)) {
        abort(404);
    }
    return response()->file($filePath);
})->where('path', '.*')->name('storage.local');