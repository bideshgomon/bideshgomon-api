<?php

use App\Http\Controllers\Admin\CountryPageController; // <-- IMPORT NEW CONTROLLER
use App\Http\Controllers\Admin\CoursePageController;
use App\Http\Controllers\Admin\UniversityPageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPageController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public Routes
Route::get('/', [PublicPageController::class, 'welcome'])->name('welcome');
Route::get('/universities', [PublicPageController::class, 'universities'])->name('public.universities.search');
Route::get('/courses', [PublicPageController::class, 'courses'])->name('public.courses.search');


// Authenticated User Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->name('admin.')->prefix('admin')->group(function () {
    
    Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard'); 
    })->name('dashboard');

    // University CRUD (Single-Page)
    Route::get('/universities', [UniversityPageController::class, 'index'])->name('universities.index');

    // Course CRUD (Single-Page)
    Route::get('/courses', [CoursePageController::class, 'index'])->name('courses.index');

    // --- NEW ROUTE ---
    // Country CRUD (Single-Page)
    Route::get('/countries', [CountryPageController::class, 'index'])->name('countries.index');
    // --- END NEW ROUTE ---

});


require __DIR__ . '/auth.php';