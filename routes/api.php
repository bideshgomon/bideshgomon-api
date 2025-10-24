<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// Removed unused App\Models\User import

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

// User Profile Controllers (ensure namespaces match your project, e.g., App\Http\Controllers\Profile\*)
// Assuming the controllers exist in the specified Api\UserProfile namespace
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
| PUBLIC API ROUTES (No Authentication Required)
|--------------------------------------------------------------------------
*/

// Public Search Routes
Route::get('/search/universities', [PublicSearchController::class, 'searchUniversities'])
    ->name('api.public.search.universities');

Route::get('/search/courses', [PublicSearchController::class, 'searchCourses'])
    ->name('api.public.search.courses');

Route::get('/search/jobs', [PublicSearchController::class, 'searchJobPostings'])
    ->name('api.public.search.jobs'); // Renamed from job-postings for consistency

// Public Detail Routes
Route::get('/universities/{university}', [PublicSearchController::class, 'showUniversityDetail'])
    ->name('api.public.universities.show');

Route::get('/courses/{course}', [PublicSearchController::class, 'showCourseDetail'])
    ->name('api.public.courses.show');

// Public Auth Routes (Register/Login)
Route::post('/register', [AuthController::class, 'register'])->name('api.public.register'); // Added name
Route::post('/login', [AuthController::class, 'login'])->name('api.public.login'); // Added name


/*
|--------------------------------------------------------------------------
| AUTHENTICATED API ROUTES (Require 'auth:sanctum' Middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum'])->group(function () {

    // Get Authenticated User Details
    Route::get('/user', function (Request $request) {
        return $request->user()->load('role'); // Eager load role
    })->name('api.user'); // Consistent name

    // General Authenticated Routes
    Route::get('/me', [AuthController::class, 'me'])->name('api.me'); // Added name
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout'); // Added name
    Route::get('/prebuilt-data', [PrebuiltDataController::class, 'getAll'])->name('api.prebuilt-data'); // Added name

    // Document Types (Authenticated)
    Route::get('/document-types', [DocumentTypeController::class, 'index'])
        ->name('api.document-types.index');

    // User Profile API Resources (Already Prefixed & Named Correctly)
    Route::prefix('profile')->name('api.profile.')->group(function() { // Name includes 'api.' prefix
        Route::apiResource('educations', UserEducationController::class)->except(['show']);
        // Note: Check if 'work-experience' or 'experiences' should be used based on your model/controller
        Route::apiResource('work-experience', UserWorkExperienceController::class)->except(['show']);
        Route::apiResource('documents', UserDocumentController::class)->only(['index', 'store', 'destroy']);
        Route::apiResource('skills', UserSkillController::class)->only(['index', 'store', 'destroy']); // Changed from post/delete to apiResource
        Route::apiResource('licenses', UserLicenseController::class)->except(['show']);
        Route::apiResource('languages', UserLanguageController::class)->except(['show']);
        Route::apiResource('technical-education', UserTechnicalEducationController::class)->except(['show']);
        Route::apiResource('memberships', UserMembershipController::class)->except(['show']);
        Route::apiResource('portfolios', UserPortfolioController::class)->except(['show']); // Included from fix
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN-ONLY API ROUTES (Require 'auth:sanctum' and 'role:admin' Middleware)
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->middleware(['role:admin'])->name('api.admin.')->group(function () { // Name includes 'api.' prefix
        // Bulk upload for countries
        Route::post('countries/bulk', [CountryController::class, 'bulkUpload'])->name('countries.bulk');

        // Admin CRUD API routes
        Route::apiResource('countries', CountryController::class);
        Route::apiResource('states', StateController::class);
        Route::apiResource('cities', CityController::class);
        Route::apiResource('universities', UniversityController::class);
        Route::apiResource('courses', CourseController::class);
        Route::apiResource('job-categories', JobCategoryController::class);
        Route::apiResource('job-postings', JobPostingController::class);
        // Add other admin-specific API routes here if needed
    });

}); // End of auth:sanctum middleware group