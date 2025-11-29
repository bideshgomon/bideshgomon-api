<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TouristVisaApplicationController;
use App\Http\Controllers\Api\StudentVisaApplicationController;
use App\Http\Controllers\Api\WorkVisaApplicationController;
use App\Http\Controllers\Api\TranslationApplicationController;
use App\Http\Controllers\Api\AttestationApplicationController;
use App\Http\Controllers\Api\HajjUmrahApplicationController;
use App\Http\Controllers\API\SearchController;
use App\Http\Controllers\API\AuthController;

// Public API routes
Route::prefix('v1')->group(function () {
    // Authentication endpoints
    Route::post('/login', [AuthController::class, 'login'])->middleware('api.rate_limit:strict');
    Route::post('/register', [AuthController::class, 'register'])->middleware('api.rate_limit:strict');
});

// Protected API routes
Route::prefix('v1')->middleware(['auth:sanctum', 'api.rate_limit:relaxed'])->group(function () {
    // Auth management
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

// Legacy Sanctum route
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Search API routes
Route::middleware(['auth:web'])->prefix('search')->name('api.search.')->group(function () {
    Route::get('/suggestions', [SearchController::class, 'suggestions'])->name('suggestions');
    Route::get('/search', [SearchController::class, 'search'])->name('search');
});

// Tourist Visa Application API routes
Route::middleware(['auth:web'])->prefix('tourist-visa-applications')->name('api.tourist-visa-applications.')->group(function () {
    Route::get('/', [TouristVisaApplicationController::class, 'index'])->name('index');
    Route::post('/', [TouristVisaApplicationController::class, 'store'])->name('store');
    Route::get('/{touristVisaApplication}', [TouristVisaApplicationController::class, 'show'])->name('show');
    Route::put('/{touristVisaApplication}', [TouristVisaApplicationController::class, 'update'])->name('update');
    Route::delete('/{touristVisaApplication}', [TouristVisaApplicationController::class, 'destroy'])->name('destroy');
});

// Student Visa Application API routes
Route::middleware(['auth:web'])->prefix('student-visa-applications')->name('api.student-visa-applications.')->group(function () {
    Route::get('/', [StudentVisaApplicationController::class, 'index'])->name('index');
    Route::post('/', [StudentVisaApplicationController::class, 'store'])->name('store');
    Route::get('/{studentVisa}', [StudentVisaApplicationController::class, 'show'])->name('show');
    Route::put('/{studentVisa}', [StudentVisaApplicationController::class, 'update'])->name('update');
    Route::delete('/{studentVisa}', [StudentVisaApplicationController::class, 'destroy'])->name('destroy');
});

// Work Visa Application API routes
Route::middleware(['auth:web'])->prefix('work-visa-applications')->name('api.work-visa-applications.')->group(function () {
    Route::get('/', [WorkVisaApplicationController::class, 'index'])->name('index');
    Route::post('/', [WorkVisaApplicationController::class, 'store'])->name('store');
    Route::get('/{workVisa}', [WorkVisaApplicationController::class, 'show'])->name('show');
    Route::put('/{workVisa}', [WorkVisaApplicationController::class, 'update'])->name('update');
    Route::delete('/{workVisa}', [WorkVisaApplicationController::class, 'destroy'])->name('destroy');
});

// Translation Application API routes
Route::middleware(['auth:web'])->prefix('translation-applications')->name('api.translation-applications.')->group(function () {
    Route::get('/', [TranslationApplicationController::class, 'index'])->name('index');
    Route::post('/', [TranslationApplicationController::class, 'store'])->name('store');
    Route::get('/{translation}', [TranslationApplicationController::class, 'show'])->name('show');
    Route::put('/{translation}', [TranslationApplicationController::class, 'update'])->name('update');
    Route::delete('/{translation}', [TranslationApplicationController::class, 'destroy'])->name('destroy');
});

// Attestation Application API routes
Route::middleware(['auth:web'])->prefix('attestation-applications')->name('api.attestation-applications.')->group(function () {
    Route::get('/', [AttestationApplicationController::class, 'index'])->name('index');
    Route::post('/', [AttestationApplicationController::class, 'store'])->name('store');
    Route::get('/{attestation}', [AttestationApplicationController::class, 'show'])->name('show');
    Route::put('/{attestation}', [AttestationApplicationController::class, 'update'])->name('update');
    Route::delete('/{attestation}', [AttestationApplicationController::class, 'destroy'])->name('destroy');
});

// Hajj & Umrah Application API routes
Route::middleware(['auth:web'])->prefix('hajj-umrah-applications')->name('api.hajj-umrah-applications.')->group(function () {
    Route::get('/', [HajjUmrahApplicationController::class, 'index'])->name('index');
    Route::post('/', [HajjUmrahApplicationController::class, 'store'])->name('store');
    Route::get('/{hajjUmrah}', [HajjUmrahApplicationController::class, 'show'])->name('show');
    Route::put('/{hajjUmrah}', [HajjUmrahApplicationController::class, 'update'])->name('update');
    Route::delete('/{hajjUmrah}', [HajjUmrahApplicationController::class, 'destroy'])->name('destroy');
});
