<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- CONTROLLER IMPORTS ---

// Auth + Shared
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DocumentTypeController;
use App\Http\Controllers\Api\PublicSearchController; // Assuming exists for dynamic selects

// Admin Controllers
use App\Http\Controllers\Api\Admin\CountryController;
use App\Http\Controllers\Api\Admin\StateController;
use App\Http\Controllers\Api\Admin\CityController;
use App\Http\Controllers\Api\Admin\UniversityController;
use App\Http\Controllers\Api\Admin\CourseController;
use App\Http\Controllers\Api\Admin\JobCategoryController; // Added based on context
use App\Http\Controllers\Api\Admin\JobPostingController; // Added based on context
use App\Http\Controllers\Api\Admin\AirlineController; // Added based on context
use App\Http\Controllers\Api\Admin\AirportController; // Added based on context
use App\Http\Controllers\Api\Admin\FlightController; // Added based on context
use App\Http\Controllers\Api\Admin\TouristVisaController; // Added based on context
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\WorkVisaApplicationController as AdminWorkVisaApiController;
use App\Http\Controllers\Api\Admin\StudentVisaApplicationController as AdminStudentVisaApiController;
use App\Http\Controllers\Api\Admin\ConsultationServiceController as AdminConsultationServiceApiController; // Added based on context

// User Profile Controllers (Using corrected names and removing redundant ones)
use App\Http\Controllers\Api\UserProfile\UserEducationController;
use App\Http\Controllers\Api\UserProfile\UserWorkExperienceController; // Corrected
use App\Http\Controllers\Api\UserProfile\UserSkillController;          // Corrected
use App\Http\Controllers\Api\UserProfile\UserDocumentController;
use App\Http\Controllers\Api\UserProfile\UserPortfolioController; // <-- ADDED import
use App\Http\Controllers\Api\UserProfile\UserLicenseController;
use App\Http\Controllers\Api\UserProfile\UserLanguageController;
use App\Http\Controllers\Api\UserProfile\UserTechnicalEducationController;
use App\Http\Controllers\Api\UserProfile\UserMembershipController;

// User Service Controllers
use App\Http\Controllers\Api\TravelInsuranceController; // Added based on context
use App\Http\Controllers\Api\WorkVisaApplicationController; // User-facing
use App\Http\Controllers\Api\StudentVisaApplicationController; // User-facing
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- Public Routes ---
Route::get('/document-types', [DocumentTypeController::class, 'index'])->name('api.public.document-types');
Route::prefix('public')->name('api.public.')->group(function() {
    // API endpoints for dynamic selects (e.g., in Student Visa form)
    Route::get('/universities', [PublicSearchController::class, 'searchUniversities'])->name('universities.search');
    Route::get('/courses', [PublicSearchController::class, 'searchCourses'])->name('courses.search');
    // Add other public search/lookup endpoints here
});
// Public details (assuming PublicSearchController handles these)
Route::get('/universities/{university}', [PublicSearchController::class, 'showUniversityDetail'])->name('api.public.universities.show');
Route::get('/courses/{course}', [PublicSearchController::class, 'showCourseDetail'])->name('api.public.courses.show');


// --- Authentication Routes ---
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/register', [AuthController::class, 'register'])->name('api.register');


// --- Authenticated Routes ---
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me'])->name('api.user.me'); // Renamed from 'user' route in api.php to avoid conflict
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    // --- User Profile Specific Updates ---
    Route::prefix('profile')->name('api.profile.')->group(function() { // Renamed from 'user-profile' for consistency
        Route::apiResource('education', UserEducationController::class);
        Route::apiResource('experience', UserWorkExperienceController::class); // Changed from UserExperienceController (Bug 9)
        Route::apiResource('skills', UserSkillController::class)->only(['index', 'store', 'destroy']); // Use correct controller (Bug 10), add destroy per Bug 6 (method exists)
        Route::apiResource('documents', UserDocumentController::class)->only(['index', 'store', 'destroy']);
        Route::apiResource('portfolios', UserPortfolioController::class); // <-- ADDED MISSING ROUTE (Bug 5)
        Route::apiResource('licenses', UserLicenseController::class);
        Route::apiResource('languages', UserLanguageController::class);
        Route::apiResource('technical-educations', UserTechnicalEducationController::class);
        Route::apiResource('memberships', UserMembershipController::class);
    });

    // --- User Actions for their own Applications ---
    Route::apiResource('work-visa-applications', WorkVisaApplicationController::class)
          ->names('api.work-visa-applications');
    Route::apiResource('student-visa-applications', StudentVisaApplicationController::class)
          ->names('api.student-visa-applications');

    // --- Payment Initiation ---
    Route::post('/payment/initiate/travel-insurance', [PaymentController::class, 'initiateTravelInsurancePayment'])
         ->name('api.payment.initiate.travel-insurance');
    // Add other payment initiation routes here (e.g., for appointments)
});


// --- Admin API Routes ---
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->name('api.admin.')->group(function () {
    // Lookups
    Route::post('countries/bulk', [CountryController::class, 'bulkUpload'])->name('countries.bulk');
    Route::apiResource('countries', CountryController::class);
    Route::apiResource('states', StateController::class);
    Route::apiResource('cities', CityController::class);
    // Management
    Route::apiResource('universities', UniversityController::class);
    Route::apiResource('courses', CourseController::class);
    Route::apiResource('job-categories', JobCategoryController::class); // Added based on context
    Route::apiResource('job-postings', JobPostingController::class); // Added based on context
    Route::apiResource('airlines', AirlineController::class); // Added based on context
    Route::apiResource('airports', AirportController::class); // Added based on context
    Route::apiResource('flights', FlightController::class); // Added based on context
    Route::apiResource('consultation-services', AdminConsultationServiceApiController::class); // Added based on context
    Route::apiResource('tourist-visas', TouristVisaController::class)->except(['store', 'show']); // Added based on context
    Route::patch('/tourist-visa-documents/{document}', [TouristVisaController::class, 'updateDocumentStatus'])->name('tourist-visa-documents.update'); // Added based on context
    Route::apiResource('users', AdminUserController::class);
    Route::apiResource('work-visa-applications', AdminWorkVisaApiController::class)
        ->names('api.admin.work-visa-applications');
    Route::apiResource('student-visa-applications', AdminStudentVisaApiController::class)
        ->names('api.admin.student-visa-applications');
});