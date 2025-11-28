<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TouristVisaApplicationController;
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
