<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- CONTROLLER IMPORTS ---

// Auth + Shared
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DocumentTypeController;
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
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\WorkVisaApplicationController as AdminWorkVisaApiController;
// *** ADDED: Admin Student Visa API Controller Import ***
use App\Http\Controllers\Api\Admin\StudentVisaApplicationController as AdminStudentVisaApiController;

// User Profile Controllers (Using corrected names)
use App\Http\Controllers\Api\UserProfile\UserEducationController;
use App\Http\Controllers\Api\UserProfile\UserExperienceController;
use App\Http\Controllers\Api\UserProfile\UserSkillController;
use App\Http\Controllers\Api\UserProfile\UserDocumentController;
use App\Http\Controllers\Api\UserProfile\UserPortfolioController;
use App\Http\Controllers\Api\UserProfile\UserLicenseController;
use App\Http\Controllers\Api\UserProfile\UserLanguageController;
use App\Http\Controllers\Api\UserProfile\UserTechnicalEducationController;
use App\Http\Controllers\Api\UserProfile\UserMembershipController;

// User Service Controllers
use App\Http\Controllers\Api\TravelInsuranceController;
use App\Http\Controllers\Api\WorkVisaApplicationController; // User-facing
// *** ADDED: User Student Visa Application Controller ***
use App\Http\Controllers\Api\StudentVisaApplicationController; // User-facing
use App\Http\Controllers\PaymentController;

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

// --- Public Search & Lookup API ---
// Moved document types here as it's likely needed publicly too
Route::get('/document-types', [DocumentTypeController::class, 'index'])->name('api.public.document-types');
Route::prefix('public')->name('api.public.')->group(function() {
    // API endpoints for dynamic selects (e.g., in Student Visa form)
    Route::get('/universities', [PublicSearchController::class, 'searchUniversities'])->name('universities.search');
    Route::get('/courses', [PublicSearchController::class, 'searchCourses'])->name('courses.search');
    // Add other public search routes (jobs, etc.) if needed via API
});
Route::get('/universities/{university}', [PublicSearchController::class, 'showUniversityDetail'])->name('api.public.universities.show');
Route::get('/courses/{course}', [PublicSearchController::class, 'showCourseDetail'])->name('api.public.courses.show');


// --- Authenticated User Routes ---
Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    // Profile Management
    Route::prefix('profile')->name('api.profile.')->group(function() {
        Route::apiResource('education', UserEducationController::class);
        Route::apiResource('experience', UserExperienceController::class); // Corrected
        Route::apiResource('skills', UserSkillController::class);          // Corrected
        Route::apiResource('documents', UserDocumentController::class)->only(['index', 'store', 'destroy']);
        Route::apiResource('portfolios', UserPortfolioController::class);
        Route::apiResource('licenses', UserLicenseController::class);
        Route::apiResource('languages', UserLanguageController::class);
        Route::apiResource('technical-educations', UserTechnicalEducationController::class);
        Route::apiResource('memberships', UserMembershipController::class);
    });

    // Travel Insurance
    Route::prefix('travel-insurance')->name('api.travel-insurance.')->group(function () { /* ... routes ... */ });

    // Payment Initiation
    Route::post('/payment/initiate/travel-insurance', [PaymentController::class, 'initiateTravelInsurancePayment'])
         ->name('api.payment.initiate.travel-insurance');

    // Work Visa Application (User manages their own)
    Route::apiResource('work-visa-applications', WorkVisaApplicationController::class)
        ->names('api.work-visa-applications');

    // *** ADDED: Student Visa Application API Routes (User manages their own) ***
    Route::apiResource('student-visa-applications', StudentVisaApplicationController::class)
        ->names('api.student-visa-applications');
});

// --- Admin API Routes ---
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->name('api.admin.')->group(function () {
    Route::post('countries/bulk', [CountryController::class, 'bulkUpload'])->name('countries.bulk');
    Route::apiResource('countries', CountryController::class); // Consider making readonly for admin if needed
    Route::apiResource('states', StateController::class);     // Consider making readonly
    Route::apiResource('cities', CityController::class);      // Consider making readonly
    Route::apiResource('universities', UniversityController::class);
    Route::apiResource('courses', CourseController::class);
    Route::apiResource('job-categories', JobCategoryController::class);
    Route::apiResource('job-postings', JobPostingController::class);
    Route::apiResource('airlines', AirlineController::class);
    Route::apiResource('airports', AirportController::class);
    Route::apiResource('flights', FlightController::class);
    Route::apiResource('tourist-visas', TouristVisaController::class)->except(['store', 'show']);
    Route::patch('/tourist-visa-documents/{document}', [TouristVisaController::class, 'updateDocumentStatus'])->name('tourist-visa-documents.update');
    Route::apiResource('users', AdminUserController::class);
    Route::apiResource('work-visa-applications', AdminWorkVisaApiController::class)
        ->names('api.admin.work-visa-applications'); // Admin manages all

    // *** ADDED: Admin Student Visa API Routes ***
    Route::apiResource('student-visa-applications', AdminStudentVisaApiController::class)
        ->names('api.admin.student-visa-applications'); // Admin manages all
});