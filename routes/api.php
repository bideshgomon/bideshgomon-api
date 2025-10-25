<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User; // Keep if needed, though not directly used in routes here

// --- COMBINED CONTROLLER IMPORTS ---

// Auth + Shared
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PrebuiltDataController; // From first file
use App\Http\Controllers\Api\DocumentTypeController;

// 🧭 Public Search
use App\Http\Controllers\Api\PublicSearchController;

// 🏛️ Admin Controllers
use App\Http\Controllers\Api\Admin\CountryController;
use App\Http\Controllers\Api\Admin\StateController;
use App\Http\Controllers\Api\Admin\CityController;
use App\Http\Controllers\Api\Admin\UniversityController;
use App\Http\Controllers\Api\Admin\CourseController;
use App\Http\Controllers\Api\Admin\JobCategoryController; // From second file
use App\Http\Controllers\Api\Admin\JobPostingController; // From second file
use App\Http\Controllers\Api\Admin\AirlineController; // From second file
use App\Http\Controllers\Api\Admin\AirportController; // From second file
use App\Http\Controllers\Api\Admin\FlightController; // From second file

// 👤 User Profile Controllers
use App\Http\Controllers\Api\UserProfile\UserEducationController;
use App\Http\Controllers\Api\UserProfile\UserPortfolioController;
use App\Http\Controllers\Api\UserProfile\UserDocumentController;
use App\Http\Controllers\Api\UserProfile\UserWorkExperienceController; // Preferred name
use App\Http\Controllers\Api\UserProfile\UserSkillController;
use App\Http\Controllers\Api\UserProfile\UserLicenseController; // From first file
use App\Http\Controllers\Api\UserProfile\UserLanguageController; // From first file
use App\Http\Controllers\Api\UserProfile\UserTechnicalEducationController; // From first file
use App\Http\Controllers\Api\UserProfile\UserMembershipController; // From first file
// Note: UserExperienceController (from 2nd file) is likely superseded by UserWorkExperienceController
// Note: UserSkillsController (handling JSON skills) might be superseded or coexist with UserSkillController (M2M)

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Merged API routes from both provided files.
*/

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (No Authentication Required)
|--------------------------------------------------------------------------
*/

// --- PUBLIC AUTH ROUTES ---
Route::post('/register', [AuthController::class, 'register'])->name('api.register'); // Added names for consistency
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// --- PUBLIC SEARCH ROUTES ---
Route::prefix('search')->name('api.public.search.')->group(function() {
    Route::get('/universities', [PublicSearchController::class, 'searchUniversities'])->name('universities');
    Route::get('/courses', [PublicSearchController::class, 'searchCourses'])->name('courses');
    Route::get('/jobs', [PublicSearchController::class, 'searchJobs'])->name('jobs'); // Added from second file
});

// --- PUBLIC DETAIL ROUTES ---
// Using names consistent with public search if applicable
Route::get('/universities/{university}', [PublicSearchController::class, 'showUniversityDetail'])
    ->name('api.public.universities.show'); // From first file, adjusted name

Route::get('/courses/{course}', [PublicSearchController::class, 'showCourseDetail'])
    ->name('api.public.courses.show'); // From first file, adjusted name


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES (Require Sanctum Token)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // --- CORE AUTH ACTIONS ---
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/user', [AuthController::class, 'user'])->name('api.user'); // Using /user from second file (more standard than /me)
    // Route::get('/me', [AuthController::class, 'me']); // This is equivalent to /user, choose one

    // --- SHARED AUTHENTICATED DATA ---
    Route::get('/prebuilt-data', [PrebuiltDataController::class, 'getAll'])->name('api.prebuilt-data'); // From first file
    Route::get('/document-types', [DocumentTypeController::class, 'index'])->name('api.document-types.index'); // From first file

    // --- USER PROFILE (CV) ROUTES ---
    Route::prefix('profile')->name('api.profile.')->group(function() { // Added 'api.' prefix to name
        // Using more comprehensive list from first file, adjusted to standard apiResource where applicable
        Route::apiResource('education', UserEducationController::class); // Default apiResource actions
        Route::apiResource('work-experience', UserWorkExperienceController::class); // Default apiResource actions (using preferred name)
        Route::apiResource('documents', UserDocumentController::class)->only(['index', 'store', 'destroy']); // Keep specific actions
        Route::apiResource('skills', UserSkillController::class)->only(['index', 'store', 'destroy']); // Assumes M2M skills, adjust if using JSON field
        Route::apiResource('licenses', UserLicenseController::class); // Default apiResource actions
        Route::apiResource('languages', UserLanguageController::class); // Default apiResource actions
        Route::apiResource('technical-education', UserTechnicalEducationController::class); // Default apiResource actions
        Route::apiResource('memberships', UserMembershipController::class); // Default apiResource actions
        Route::apiResource('portfolios', UserPortfolioController::class); // Default apiResource actions
    });
});


/*
|--------------------------------------------------------------------------
| ADMIN-ONLY PROTECTED ROUTES (Require Sanctum Token + Admin Role)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->name('api.admin.')->group(function () { // Consistent naming 'api.admin.'
    // Bulk upload for countries (from first file)
    Route::post('countries/bulk', [CountryController::class, 'bulkUpload'])->name('countries.bulk');

    // CRUD routes (combined from both, using apiResource)
    Route::apiResource('countries', CountryController::class);
    Route::apiResource('states', StateController::class);
    Route::apiResource('cities', CityController::class);
    Route::apiResource('universities', UniversityController::class);
    Route::apiResource('courses', CourseController::class);
    Route::apiResource('job-categories', JobCategoryController::class); // From second file
    Route::apiResource('job-postings', JobPostingController::class); // From second file

    // Air Ticketing API Routes (from second file)
    Route::apiResource('airlines', AirlineController::class);
    Route::apiResource('airports', AirportController::class);
    Route::apiResource('flights', FlightController::class);
});

// --- CRITICAL VULNERABILITY REMOVED ---
// Ensure the /get-admin-token route (mentioned in first file's comments) remains removed.
// ----------------------------------------