<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User; // Keep if needed for test route

// --- IMPORT ALL CONTROLLERS ---

// Auth + Shared
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PrebuiltDataController; // Assuming this exists or is needed
use App\Http\Controllers\Api\DocumentTypeController;

// Public Search
use App\Http\Controllers\Api\PublicSearchController;

// Admin Controllers
use App\Http\Controllers\Api\Admin\CountryController;
use App\Http\Controllers\Api\Admin\StateController;
use App\Http\Controllers\Api\Admin\CityController;
use App\Http\Controllers\Api\Admin\UniversityController;
use App\Http\Controllers\Api\Admin\CourseController;
use App\Http\Controllers\Api\Admin\SkillController as AdminSkillController; // Renamed alias
use App\Http\Controllers\Api\Admin\JobCategoryController;
use App\Http\Controllers\Api\Admin\JobPostingController;
// Add other admin controllers as needed

// User Profile Controllers
use App\Http\Controllers\Api\UserProfile\UserEducationController;
use App\Http\Controllers\Api\UserProfile\UserWorkExperienceController;
use App\Http\Controllers\Api\UserProfile\UserDocumentController;
use App\Http\Controllers\Api\UserProfile\UserSkillController; // For linking existing skills
// use App\Http\Controllers\Api\UserProfile\UserSkillsController; // For updating JSON field (Choose one approach)
use App\Http\Controllers\Api\UserProfile\UserPortfolioController;
use App\Http\Controllers\Api\UserProfile\UserLicenseController; // Added
use App\Http\Controllers\Api\UserProfile\UserLanguageController; // Added
use App\Http\Controllers\Api\UserProfile\UserTechnicalEducationController; // Added
use App\Http\Controllers\Api\UserProfile\UserMembershipController; // Added

// NEW: Data Access Request Controller
use App\Http\Controllers\Api\DataAccessRequestController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- TEMPORARY TEST ROUTE (Remove in production) ---
Route::get('/get-admin-token', function () {
    $user = User::where('email', 'admin@bideshgomon.com')->first();
    if (!$user) {
        return response()->json(['message' => 'Admin user not found. Please run: php artisan migrate:fresh --seed'], 404);
    }
    $user->tokens()->delete();
    $token = $user->createToken('api-test-token'); // Consider a more descriptive name
    return response()->json([
        'message' => 'Use this token in your "Authorization: Bearer <token>" header',
        'token' => $token->plainTextToken,
    ]);
});
// ------------------------------


// --- PUBLIC ROUTES (No Authentication Needed) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public Search Endpoints
Route::prefix('search')->name('public.search.')->group(function () {
    Route::get('/universities', [PublicSearchController::class, 'searchUniversities'])->name('universities');
    Route::get('/courses', [PublicSearchController::class, 'searchCourses'])->name('courses');
    Route::get('/jobs', [PublicSearchController::class, 'searchJobs'])->name('jobs'); // Assuming method exists
});

// Public Detail View Endpoints (Example)
Route::get('/universities/{university}', [PublicSearchController::class, 'showUniversityDetail'])->name('public.universities.show');
Route::get('/courses/{course}', [PublicSearchController::class, 'showCourseDetail'])->name('public.courses.show');
// Add Job detail later


// --- AUTHENTICATED ROUTES (Require Sanctum Token) ---
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        // Eager load role for frontend checks
        return $request->user()->load('role');
    })->name('api.user');

    // Prebuilt/Static Data (Examples)
    Route::get('/prebuilt-data', [PrebuiltDataController::class, 'index'])->name('prebuilt.data'); // Example route
    Route::get('/document-types', [DocumentTypeController::class, 'index'])->name('document-types.index');


    // --- User Profile Management ---
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::apiResource('education', UserEducationController::class)->except(['show']);
        Route::apiResource('work-experience', UserWorkExperienceController::class)->except(['show']);
        Route::apiResource('documents', UserDocumentController::class)->only(['index', 'store', 'destroy']); // Keep it simple first
        Route::apiResource('skills', UserSkillController::class)->except(['show', 'update']); // Adjust based on Skill approach
        Route::apiResource('licenses', UserLicenseController::class)->except(['show']);
        Route::apiResource('languages', UserLanguageController::class)->except(['show']);
        Route::apiResource('technical-education', UserTechnicalEducationController::class)->except(['show']);
        Route::apiResource('memberships', UserMembershipController::class)->except(['show']);
        // Add UserPortfolioController routes if needed
        // Route::apiResource('portfolio', UserPortfolioController::class)->except(['show']);
    });


    // --- NEW: Data Access Request Management ---
    Route::prefix('data-access')->name('data-access.')->group(function () {
        // Consultant requests access to a user's data
        Route::post('/request/{user}', [DataAccessRequestController::class, 'store'])
            // ->middleware('role:consultant') // Apply role middleware later if needed, ensure it works first
            ->name('request');

        // User views their pending requests
        Route::get('/requests', [DataAccessRequestController::class, 'index'])
            // ->middleware('role:user')
            ->name('list');

        // User approves a specific request
        Route::post('/requests/{dataAccessRequest}/approve', [DataAccessRequestController::class, 'approve'])
            // ->middleware('role:user')
            ->name('approve');

        // User denies a specific request
        Route::post('/requests/{dataAccessRequest}/deny', [DataAccessRequestController::class, 'deny'])
            // ->middleware('role:user')
            ->name('deny');
    });
    // --- END Data Access Request Routes ---


    /*
    |--------------------------------------------------------------------------
    | ADMIN-ONLY PROTECTED ROUTES
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->middleware('role:admin')->name('api.admin.')->group(function () {
        // Bulk upload routes
        Route::post('countries/bulk', [CountryController::class, 'bulkUpload'])->name('countries.bulk'); // Assuming bulkUpload method exists
        Route::post('states/bulk', [StateController::class, 'bulkUpload'])->name('states.bulk'); // Assuming bulkUpload method exists
        Route::post('cities/bulk', [CityController::class, 'bulkUpload'])->name('cities.bulk'); // Assuming bulkUpload method exists
        Route::post('skills/bulk', [AdminSkillController::class, 'bulkUpload'])->name('skills.bulk'); // Assuming bulkUpload method exists
        Route::post('job-categories/bulk', [JobCategoryController::class, 'bulkUpload'])->name('job-categories.bulk'); // Assuming bulkUpload method exists

        // CRUD routes
        Route::apiResource('countries', CountryController::class);
        Route::apiResource('states', StateController::class);
        Route::apiResource('cities', CityController::class);
        Route::apiResource('skills', AdminSkillController::class)->except(['show']);
        Route::apiResource('universities', UniversityController::class);
        Route::apiResource('courses', CourseController::class);
        Route::apiResource('job-categories', JobCategoryController::class);
        Route::apiResource('job-postings', JobPostingController::class);
        // Add apiResource for Users, Roles, Permissions etc. later
    });

}); // End of auth:sanctum middleware group