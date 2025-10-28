<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| TEMPORARY ADMIN TOKEN ROUTE
|--------------------------------------------------------------------------
*/
Route::get('/get-admin-token', function () {
    $user = User::where('email', 'admin@bideshgomon.com')->first();
    if (!$user) {
        return response()->json([
            'message' => 'Admin user not found. Run: php artisan migrate:fresh --seed'
        ], 404);
    }

    $user->tokens()->delete();
    $token = $user->createToken('postman-test-token');

    return response()->json([
        'message' => 'Use this token in your Authorization header',
        'token' => $token->plainTextToken,
    ]);
});

/*
|--------------------------------------------------------------------------
| CONTROLLER IMPORTS
|--------------------------------------------------------------------------
*/

// --- AUTH & COMMON ---
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PrebuiltDataController;
use App\Http\Controllers\Api\DocumentTypeController;

// --- PUBLIC SEARCH ---
use App\Http\Controllers\Api\PublicSearchController;

// --- ADMIN CONTROLLERS ---
use App\Http\Controllers\Api\Admin\StateController;
use App\Http\Controllers\Api\Admin\CityController;
use App\Http\Controllers\Api\Admin\UniversityController;
use App\Http\Controllers\Api\Admin\CourseController;
use App\Http\Controllers\Api\Admin\JobCategoryController;
use App\Http\Controllers\Api\Admin\CountryController;
use App\Http\Controllers\Api\Admin\JobPostingController;


// --- USER PROFILE CONTROLLERS ---
use App\Http\Controllers\Api\UserProfile\UserEducationController;
use App\Http\Controllers\Api\UserProfile\UserWorkExperienceController;
use App\Http\Controllers\Api\UserProfile\UserDocumentController;
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

Route::prefix('search')->name('api.public.search.')->group(function () {
    Route::get('/universities', [PublicSearchController::class, 'searchUniversities'])->name('universities');
    Route::get('/courses', [PublicSearchController::class, 'searchCourses'])->name('courses');
    Route::get('/jobs', [PublicSearchController::class, 'searchJobPostings'])->name('jobs');
});

Route::get('/universities/{university}', [PublicSearchController::class, 'showUniversityDetail'])
    ->name('api.public.universities.show');

Route::get('/courses/{course}', [PublicSearchController::class, 'showCourseDetail'])
    ->name('api.public.courses.show');

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

// Public Auth
Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {

    // --- Current User ---
    Route::get('/user', function (Request $request) {
        return $request->user()->load('role');
    })->name('api.user');

    Route::get('/me', [AuthController::class, 'me'])->name('api.me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    // --- Shared Data ---
    Route::get('/prebuilt-data', [PrebuiltDataController::class, 'getAll'])->name('api.prebuilt-data');
    Route::get('/document-types', [DocumentTypeController::class, 'index'])->name('api.document-types.index');

    /*
    |--------------------------------------------------------------------------
    | USER PROFILE (CV BUILDER)
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->name('api.profile.')->group(function () {
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
| ADMIN-ONLY ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['auth:sanctum', 'role:admin'])
    ->name('api.admin.')
    ->group(function () {

        // --- UNIVERSITIES & COURSES ---
       // Route::apiResource('universities', UniversityController::class);
        Route::apiResource('courses', CourseController::class);

        // --- JOBS & CATEGORIES ---
        Route::apiResource('job-categories', JobCategoryController::class);
        Route::apiResource('job-postings', JobPostingController::class);

        // --- GEOGRAPHICAL DATA ---
        Route::apiResource('countries', CountryController::class)->except(['create', 'edit']);
        Route::apiResource('states', StateController::class)->except(['create', 'edit']);
        Route::get('cities/get-states', [CityController::class, 'getStatesForCountry'])
            ->name('cities.getStates');
        Route::apiResource('cities', CityController::class)->except(['create', 'edit']);
    });
