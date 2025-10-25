<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- CONTROLLER IMPORTS ---

// Auth + Shared
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PrebuiltDataController;
use App\Http\Controllers\Api\DocumentTypeController;

// Public Search
use App\Http\Controllers\Api\PublicSearchController;

// Admin Controllers
use App\Http\Controllers\Api\Admin\CountryController;
use App\Http\Controllers\Api\Admin\StateController;
use App\Http\Controllers\Api\Admin\CityController;
use App\Http\Controllers\Api\Admin\UniversityController;
use App\Http\Controllers\Api\Admin\CourseController;
use App\Http\Controllers\Api\Admin\JobCategoryController;
use App\Http\Controllers\Api\Admin\JobPostingController;
use App\Http\Controllers\Api\Admin\AirlineController;
use App\Http\Controllers\Api\Admin\AirportController;
use App\Http\Controllers\Api\Admin\FlightController;

// User Profile Controllers
use App\Http\Controllers\Api\UserProfile\UserEducationController;
use App\Http\Controllers\Api\UserProfile\UserPortfolioController;
use App\Http\Controllers\Api\UserProfile\UserDocumentController;
use App\Http\Controllers\Api\UserProfile\UserWorkExperienceController;
use App\Http\Controllers\Api\UserProfile\UserSkillController;
use App\Http\Controllers\Api\UserProfile\UserLicenseController;
use App\Http\Controllers\Api\UserProfile\UserLanguageController;
use App\Http\Controllers\Api\UserProfile\UserTechnicalEducationController;
use App\Http\Controllers\Api\UserProfile\UserMembershipController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (No Authentication Required)
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::prefix('search')->name('api.public.search.')->group(function() {
    Route::get('/universities', [PublicSearchController::class, 'searchUniversities'])->name('universities');
    Route::get('/courses', [PublicSearchController::class, 'searchCourses'])->name('courses');
    Route::get('/jobs', [PublicSearchController::class, 'searchJobs'])->name('jobs');
});

Route::get('/universities/{university}', [PublicSearchController::class, 'showUniversityDetail'])->name('api.public.universities.show');
Route::get('/courses/{course}', [PublicSearchController::class, 'showCourseDetail'])->name('api.public.courses.show');


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES (Require Sanctum Token)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // --- CORE AUTH ACTIONS ---
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/user', [AuthController::class, 'user'])->name('api.user');

    // --- SHARED AUTHENTICATED DATA ---
    Route::get('/prebuilt-data', [PrebuiltDataController::class, 'getAll'])->name('api.prebuilt-data');
    Route::get('/document-types', [DocumentTypeController::class, 'index'])->name('api.document-types.index');

    // --- USER PROFILE (CV) ROUTES ---
    Route::prefix('profile')->name('api.profile.')->group(function() {
        Route::apiResource('education', UserEducationController::class);
        Route::apiResource('work-experience', UserWorkExperienceController::class);
        Route::apiResource('documents', UserDocumentController::class)->only(['index', 'store', 'destroy']);
        Route::apiResource('skills', UserSkillController::class)->only(['index', 'store', 'destroy']); // Assumes M2M skills
        Route::apiResource('licenses', UserLicenseController::class);
        Route::apiResource('languages', UserLanguageController::class);
        Route::apiResource('technical-education', UserTechnicalEducationController::class);
        Route::apiResource('memberships', UserMembershipController::class);
        Route::apiResource('portfolios', UserPortfolioController::class);
    });
});


/*
|--------------------------------------------------------------------------
| ADMIN-ONLY PROTECTED ROUTES (Require Sanctum Token + Admin Role)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->name('api.admin.')->group(function () {
    // Bulk upload
    Route::post('countries/bulk', [CountryController::class, 'bulkUpload'])->name('countries.bulk');

    // CRUD resources
    Route::apiResource('countries', CountryController::class);
    Route::apiResource('states', StateController::class);
    Route::apiResource('cities', CityController::class);
    Route::apiResource('universities', UniversityController::class);
    Route::apiResource('courses', CourseController::class);
    Route::apiResource('job-categories', JobCategoryController::class);
    Route::apiResource('job-postings', JobPostingController::class);
    Route::apiResource('airlines', AirlineController::class);
    Route::apiResource('airports', AirportController::class);
    Route::apiResource('flights', FlightController::class);
});

// --- CRITICAL VULNERABILITY REMOVED ---
// Ensure the /get-admin-token route (mentioned previously) remains removed.
// ----------------------------------------