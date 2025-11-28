<?php

use Illuminate\Support\Facades\Route;

Route::get('/offline', function () {
    return inertia('Offline');
})->name('offline');
