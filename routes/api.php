<?php

use App\Http\Middleware\AuthOnly;
use Illuminate\Support\Facades\Route;

Route::post('login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('register', [App\Http\Controllers\AuthController::class, 'register']);

Route::middleware([AuthOnly::class])->group(function () {
    Route::get('me', [App\Http\Controllers\AuthController::class, 'me']);
    Route::post('logout', [App\Http\Controllers\AuthController::class, 'logout']);
    Route::post('refresh', [App\Http\Controllers\AuthController::class, 'refresh']);

    Route::apiResource('listings', App\Http\Controllers\ListingController::class);
});
