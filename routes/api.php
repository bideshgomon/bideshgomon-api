<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

// --- TEMPORARY TEST ROUTE ---
Route::get('/get-admin-token', function () {
    $user = User::where('email', 'admin@bideshgomon.com')->first();
    if (!$user) {
        return response()->json(['message' => 'Admin user not found. Please run: php artisan migrate:fresh --seed'], 404);
    }
    $user->tokens()->delete();
    $token = $user->createToken('postman-test-token');
    return response()->json([
        'message' => 'Use this token in your "Authorization" header',
        'token' => $token->plainTextToken,
    ]);
});
// ------------------------------


// --- IMPORT ALL CONTROLLERS ---

// Auth + Shared
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PrebuiltDataController;
use App\Http\Controllers\Api\DocumentTypeController;

// 🧭 Public Search
use App\Http\Controllers\Api\PublicSearchController;

// 🏛️ Admin Controllers
use App\Http\Controllers\Api\Admin\CountryController;
use App\Http\Controllers\Api\Admin\StateController;
use App\Http\Controllers\Api\Admin\CityController;
use App\Http\Controllers\Api\Admin\UniversityController;
use App\Http\Controllers\Api\Admin\CourseController;
use App\Http\Controllers\Api\Admin\JobCategoryController;
use App\Http\Controllers\Api\Admin\JobPostingController;

// 👤 User Profile Controllers
use App\Http\Controllers\Api\UserProfile\UserEducationController;
use App\Http\Controllers\Api\UserProfile\UserExperienceController;
use App\Http\Controllers\Api\UserProfile\UserSkillsController;
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
| PUBLIC SEARCH ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/search/universities', [PublicSearchController::class, 'searchUniversities'])
    ->name('api.public.search.universities');

Route::get('/search/courses', [PublicSearchController::class, 'searchCourses'])
    ->name('api.public.search.courses');

Route::get('/universities/{university}', [PublicSearchController::class, 'showUniversityDetail'])
    ->name('api.public.universities.show'); 
    
Route::get('/courses/{course}', [PublicSearchController::class, 'showCourseDetail'])
    ->name('api.public.courses.show');

// --- ADDED FROM MERGE ---
Route::get('/search/jobs', [PublicSearchController::class, 'searchJobPostings'])->name('api.public.search.jobs');


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/

// Default authenticated user route
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user()->load('role'); // Eager load role
})->name('api.user');


// --- PUBLIC AUTH ROUTES ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// --- PROTECTED ROUTES ---
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/prebuilt-data', [PrebuiltDataController::class, 'getAll']);

    // 📄 Document Types
    Route::get('/document-types', [DocumentTypeController::class, 'index'])
        ->name('api.document-types.index');

    // 🧑‍🎓 USER PROFILE (CV) ROUTES
    Route::prefix('profile')->name('profile.')->group(function() {
        Route::apiResource('education', UserEducationController::class)->except(['show']);
        Route::apiResource('work-experience', UserWorkExperienceController::class)->except(['show']);
        Route::apiResource('documents', UserDocumentController::class)->only(['index', 'store', 'destroy']);
        Route::apiResource('skills', UserSkillController::class)->only(['index', 'store', 'destroy']);
        Route::apiResource('licenses', UserLicenseController::class)->except(['show']);
        Route::apiResource('languages', UserLanguageController::class)->except(['show']);
        Route::apiResource('technical-education', UserTechnicalEducationController::class)->except(['show']);
        Route::apiResource('memberships', UserMembershipController::class)->except(['show']);
    });
});


/*
|--------------------------------------------------------------------------
| ADMIN-ONLY PROTECTED ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->name('api.admin.')->group(function () {
    // Bulk upload for countries
    Route::post('countries/bulk', [CountryController::class, 'bulkUpload'])->name('countries.bulk');

    // CRUD routes
    Route::apiResource('countries', CountryController::class);
    Route::apiResource('states', StateController::class);
    Route::apiResource('cities', CityController::class);

    // ✅ NEW ADMIN ENDPOINTS
    Route::apiResource('universities', UniversityController::class);
    Route::apiResource('courses', CourseController::class);
    Route::apiResource('job-categories', JobCategoryController::class);
    Route::apiResource('job-postings', JobPostingController::class);
});