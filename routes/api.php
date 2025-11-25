<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TouristVisaApplicationController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Tourist Visa Application API routes
Route::middleware(['auth:web'])->prefix('tourist-visa-applications')->name('api.tourist-visa-applications.')->group(function () {
    Route::get('/', [TouristVisaApplicationController::class, 'index'])->name('index');
    Route::post('/', [TouristVisaApplicationController::class, 'store'])->name('store');
    Route::get('/{touristVisaApplication}', [TouristVisaApplicationController::class, 'show'])->name('show');
    Route::put('/{touristVisaApplication}', [TouristVisaApplicationController::class, 'update'])->name('update');
    Route::delete('/{touristVisaApplication}', [TouristVisaApplicationController::class, 'destroy'])->name('destroy');
});
