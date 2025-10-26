<?php

use Illuminate\HttpRequest;
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
use App\Http\Controllers\Api\Admin\TouristVisaController;

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

// Travel Insurance API Controller
use App\Http\Controllers\Api\TravelInsuranceController;


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
    Route::get('/user', [AuthController::class, 'user'])->name('api.user'); // Changed from /me for consistency

    // --- SHARED AUTHENTICATED DATA ---
    // *** MERGED: Commented out PrebuiltData route as requested ***
    // Route::get('/prebuilt-data', [PrebuiltDataController::class, 'getAll'])->name('api.prebuilt-data');
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
    
    // --- USER-FACING SERVICES ---
    
    // Travel Insurance API Routes
    Route::prefix('travel-insurance')->name('api.travel-insurance.')->group(function () {
        Route::get('/packages', [TravelInsuranceController::class, 'getPackages'])->name('packages');
        Route::post('/calculate-premium', [TravelInsuranceController::class, 'calculatePremium'])->name('calculate-premium');
        Route::post('/issue-policy', [TravelInsuranceController::class, 'issuePolicy'])->name('issue-policy');
        // Route::get('/my-policies', [TravelInsuranceController::class, 'getUserPolicies'])->name('my-policies');
    });

    // Add user-facing routes for Tourist Visas here when built
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
    
    // Tourist Visa Management
    Route::apiResource('tourist-visas', TouristVisaController::class)->except(['store', 'show']);
    Route::patch('/tourist-visa-documents/{document}', [TouristVisaController::class, 'updateDocumentStatus'])->name('tourist-visa-documents.update');
});