<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasienController;

Route::get('/', function () {
    return view('welcome');
});

// API Routes untuk Pasien
Route::prefix('api')->group(function () {
    Route::apiResource('pasien', PasienController::class);
});

// Catch-all untuk Vue Router
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api).*$')->name('spa');

