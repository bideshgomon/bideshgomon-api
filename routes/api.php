<?php

use Illuminate\Http\Request; // <-- CORRECTED Import
use Illuminate\Support\Facades\Route;

// --- CONTROLLER IMPORTS ---

// Auth + Shared
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DocumentTypeController;
use App\Http\Controllers\Api\PublicSearchController;
// use App\Http\Controllers\Api\PrebuiltDataController; // Removed

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

// User Profile Controllers (CORRECTED & ADDED)
use App\Http\Controllers\Api\UserProfile\UserEducationController;
use App\Http\Controllers\Api\UserProfile\UserExperienceController;      // <-- CORRECTED Namespace/Name if needed
use App\Http\Controllers\Api\UserProfile\UserSkillController;          // <-- CORRECTED Namespace/Name if needed
use App\Http\Controllers\Api\UserProfile\UserDocumentController;
use App\Http\Controllers\Api\UserProfile\UserPortfolioController;
use App\Http\Controllers\Api\UserProfile\UserLicenseController;        // <-- ADDED
use App\Http\Controllers\Api\UserProfile\UserLanguageController;       // <-- ADDED
use App\Http\Controllers\Api\UserProfile\UserTechnicalEducationController; // <-- ADDED
use App\Http\Controllers\Api\UserProfile\UserMembershipController;     // <-- ADDED

// User Service Controllers
use App\Http\Controllers\Api\TravelInsuranceController;
use App\Http\Controllers\PaymentController; // Note: In App\Http\Controllers

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- Auth Routes (Sanctum) ---
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout'])->name('api.logout');
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user'])->name('api.user');

// --- Public Search API ---
Route::prefix('search')->name('api.public.search.')->group(function() {
    Route::get('/universities', [PublicSearchController::class, 'searchUniversities'])->name('universities');
    Route::get('/courses', [PublicSearchController::class, 'searchCourses'])->name('courses');
    Route::get('/jobs', [PublicSearchController::class, 'searchJobs'])->name('jobs');
});
Route::get('/universities/{university}', [PublicSearchController::class, 'showUniversityDetail'])->name('api.public.universities.show');
Route::get('/courses/{course}', [PublicSearchController::class, 'showCourseDetail'])->name('api.public.courses.show');


// --- Authenticated User Routes ---
// *** CORRECTED: Uses Route::middleware([...]) ***
Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    Route::get('/document-types', [DocumentTypeController::class, 'index'])->name('api.document-types.index');

    // Profile Management (All apiResources)
    Route::prefix('profile')->name('api.profile.')->group(function() {
        Route::apiResource('education', UserEducationController::class);
        Route::apiResource('experience', UserExperienceController::class); // <-- CORRECTED Controller if needed
        Route::apiResource('skills', UserSkillController::class);          // <-- CORRECTED Controller if needed
        Route::apiResource('documents', UserDocumentController::class)->only(['index', 'store', 'destroy']);
        Route::apiResource('portfolios', UserPortfolioController::class);
        Route::apiResource('licenses', UserLicenseController::class);        // <-- ADDED
        Route::apiResource('languages', UserLanguageController::class);      // <-- ADDED
        Route::apiResource('technical-educations', UserTechnicalEducationController::class); // <-- ADDED (Note: plural resource name)
        Route::apiResource('memberships', UserMembershipController::class);  // <-- ADDED
    });

    // Travel Insurance
    Route::prefix('travel-insurance')->name('api.travel-insurance.')->group(function () {
        Route::get('/packages', [TravelInsuranceController::class, 'getPackages'])->name('packages');
        Route::post('/calculate-premium', [TravelInsuranceController::class, 'calculatePremium'])->name('calculate-premium');
        Route::post('/issue-policy', [TravelInsuranceController::class, 'issuePolicy'])->name('issue-policy');
    });

    // Payment Initiation
    Route::post('/payment/initiate/travel-insurance', [PaymentController::class, 'initiateTravelInsurancePayment'])
         ->name('api.payment.initiate.travel-insurance');
});

// --- Admin API Routes ---
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->name('api.admin.')->group(function () {
    Route::post('countries/bulk', [CountryController::class, 'bulkUpload'])->name('countries.bulk');
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
    Route::apiResource('tourist-visas', TouristVisaController::class)->except(['store', 'show']);
    Route::patch('/tourist-visa-documents/{document}', [TouristVisaController::class, 'updateDocumentStatus'])->name('tourist-visa-documents.update');
});