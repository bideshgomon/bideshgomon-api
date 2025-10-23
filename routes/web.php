<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GuidanceController;
use App\Http\Controllers\PublicPageController; // ✅ Added for public pages
use App\Http\Controllers\Admin\UniversityPageController;
use App\Http\Controllers\Admin\CoursePageController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you define your application's web routes.
| These are loaded by the RouteServiceProvider and assigned to the "web" middleware group.
|
*/


// 🏠 --- PUBLIC ROUTES ---
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('welcome');

// 🎓 University Search (Public)
Route::get('/universities', [PublicPageController::class, 'showUniversitySearch'])
    ->name('public.universities.search');

// 📚 Course Search (Public) <-- ADDED FROM MERGE
Route::get('/courses', [PublicPageController::class, 'showCourseSearch'])
    ->name('public.courses.search');


// 👤 --- AUTHENTICATED USER ROUTES ---
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard (for students / consultants)
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
    });


// 🔐 --- AUTH ROUTES (Login, Register, etc.) ---
require __DIR__ . '/auth.php';