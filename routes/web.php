<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\KasusController;

Route::get('/', function () {
    return view('welcome');
});

// API Routes
Route::prefix('api')->group(function () {
    Route::apiResource('pasien', PasienController::class);
    Route::apiResource('kasus', KasusController::class);
    Route::get('kasus/kategori/list', [KasusController::class, 'getKategori']);
});

// Catch-all untuk Vue Router
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api).*$')->name('spa');

