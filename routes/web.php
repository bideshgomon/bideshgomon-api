<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GuidanceController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\Admin\UniversityPageController;
use App\Http\Controllers\Admin\CoursePageController;
use App\Http\Controllers\Admin\JobCategoryPageController; // <-- ADDED FROM MERGE

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| These routes handle both public and authenticated sections of
| the BideshGomon application.
|--------------------------------------------------------------------------
*/

// 🏠 --- PUBLIC ROUTES ---
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'    => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('welcome');

// 🎓 University Search & Detail (Public)
Route::get('/universities', [PublicPageController::class, 'showUniversitySearch'])
    ->name('public.universities.search');

Route::get('/universities/{university}', [PublicPageController::class, 'showUniversityDetail'])
    ->name('public.universities.show'); // ✅ Added detail route

// 📚 Course Search & Detail (Public)
Route::get('/courses', [PublicPageController::class, 'showCourseSearch'])
    ->name('public.courses.search');

Route::get('/courses/{course}', [PublicPageController::class, 'showCourseDetail'])
    ->name('public.courses.show'); // ✅ Added detail route


// 👤 --- AUTHENTICATED USER ROUTES ---
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard for logged-in users
    Route::get('/dashboard', [GuidanceController::class, 'dashboard'])->name('dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
});


// 🧭 --- ADMIN PANEL ROUTES ---
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // 🏫 University Management
        Route::get('/universities', [UniversityPageController::class, 'index'])->name('universities.index');
        Route::get('/universities/create', [UniversityPageController::class, 'create'])->name('universities.create');
        Route::get('/universities/{university}/edit', [UniversityPageController::class, 'edit'])->name('universities.edit');

        // 🎓 Course Management
        Route::get('/courses', [CoursePageController::class, 'index'])->name('courses.index');
        Route::get('/courses/create', [CoursePageController::class, 'create'])->name('courses.create');
        Route::get('/courses/{course}/edit', [CoursePageController::class, 'edit'])->name('courses.edit');
        
        // 💼 Job Category Management <-- ADDED FROM MERGE
        Route::get('/job-categories', [JobCategoryPageController::class, 'index'])->name('job-categories.index');
        Route::get('/job-categories/create', [JobCategoryPageController::class, 'create'])->name('job-categories.create');
        Route::get('/job-categories/{jobCategory}/edit', [JobCategoryPageController::class, 'edit'])->name('job-categories.edit');
    });


// 🔐 --- AUTH ROUTES (Login, Register, etc.) ---
require __DIR__ . '/auth.php';