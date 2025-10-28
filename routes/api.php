<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Controller Imports (API ONLY)
|--------------------------------------------------------------------------
*/

// --- Auth & Public ---
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PrebuiltDataController;
use App\Http\Controllers\Api\DocumentTypeController;
use App\Http\Controllers\Api\PublicSearchController;

// --- Admin API ---
use App\Http\Controllers\Api\Admin\CountryController;
use App\Http\Controllers\Api\Admin\StateController;
use App\Http\Controllers\Api\Admin\CityController;
use App\Http\Controllers\Api\Admin\UniversityController;
use App\Http\Controllers\Api\Admin\CourseController;

// --- User Profile API (CV Builder) ---
use App\Http\Controllers\Api\UserProfileController; // For updating UserProfile details
use App\Http\Controllers\Api\UserProfile\UserEducationController;
use App\Http\Controllers\Api\UserProfile\UserSkillsController;
use App\Http\Controllers\Api\UserProfile\UserPortfolioController;
use App\Http\Controllers\Api\UserProfile\UserDocumentController;
use App\Http\Controllers\Api\UserProfile\UserWorkExperienceController;
use App\Http\Controllers\Api\UserProfile\UserLicenseController;
use App\Http\Controllers\Api\UserProfile\UserLanguageController;
use App\Http\Controllers\Api\UserProfile\UserTechnicalEducationController;
use App\Http\Controllers\Api\UserProfile\UserMembershipController;
use App\Http\Controllers\Api\UserProfile\UserTravelHistoryController; // <-- IMPORT ADDED

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- Test Route ---
Route::get('/get-admin-token', function () {
    $user = User::where('email', 'admin@bideshgomon.com')->first();
    if (!$user) {
        return response()->json(['message' => 'Admin user not found. Please run: php artisan migrate:fresh --seed'], 404);
    }
    $user->tokens()->delete();
    $token = $user->createToken('api-token');
    return response()->json(['token' => $token->plainTextToken]);
});


// --- PUBLIC SEARCH API ---
Route::get('/search/universities', [PublicSearchController::class, 'searchUniversities']);
Route::get('/search/courses', [PublicSearchController::class, 'searchCourses']);
Route::get('/universities/{university}', [PublicSearchController::class, 'showUniversityDetail']);
Route::get('/courses/{course}', [PublicSearchController::class, 'showCourseDetail']);


// --- AUTH API ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- PROTECTED API ROUTES ---
Route::middleware('auth:sanctum')->group(function () {

    // Auth & User
    Route::get('/user', fn(Request $request) => $request->user()->load('role'));
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Prebuilt Data (for forms)
    Route::get('/prebuilt-data', [PrebuiltDataController::class, 'getAll'])->name('api.prebuilt-data'); // Added name for SkillsSection
    Route::get('/document-types', [DocumentTypeController::class, 'index'])->name('api.document-types.index'); // Keep name

    // Route for updating UserProfile details (Personal Info section)
    Route::put('/profile/details', [UserProfileController::class, 'update'])->name('profile.details.update');

    // 🧑‍🎓 USER PROFILE (CV) API Sections
    Route::prefix('profile')->name('profile.')->group(function() {
        Route::apiResource('education', UserEducationController::class)->except(['show']);
        Route::apiResource('work-experience', UserWorkExperienceController::class)->except(['show']);
        Route::apiResource('documents', UserDocumentController::class)->only(['index', 'store', 'destroy']);
        Route::apiResource('skills', UserSkillsController::class)->only(['index', 'store']); // Store handles replacing all skills
        Route::apiResource('licenses', UserLicenseController::class)->except(['show']);
        Route::apiResource('languages', UserLanguageController::class)->except(['show']);
        Route::apiResource('technical-education', UserTechnicalEducationController::class)->except(['show']);
        Route::apiResource('memberships', UserMembershipController::class)->except(['show']);
        Route::apiResource('travel-history', UserTravelHistoryController::class)->except(['show']); // <-- ROUTE ADDED
        // Note: Removed redundant/incorrect UserSkillController routes from previous file version
    });
});


// --- ADMIN-ONLY PROTECTED API ROUTES ---
Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->name('admin.')->group(function () {

    Route::post('countries/bulk', [CountryController::class, 'bulkUpload'])->name('countries.bulk');

    // CRUD API routes
    Route::apiResource('countries', CountryController::class);
    Route::apiResource('states', StateController::class);
    Route::apiResource('cities', CityController::class);
    Route::apiResource('universities', UniversityController::class);
    Route::apiResource('courses', CourseController::class);
    // Add other Admin API resources here if needed (e.g., Skills, Languages)
});