<?php

use App\Http\Controllers\AccountController;
use App\Http\Middleware\AuthOnly;
use Illuminate\Support\Facades\Route;

Route::post('login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('register', [App\Http\Controllers\AuthController::class, 'register']);

Route::middleware([AuthOnly::class])->group(function () {
    Route::get('me', [App\Http\Controllers\AuthController::class, 'me']);
    Route::post('logout', [App\Http\Controllers\AuthController::class, 'logout']);
    Route::post('refresh', [App\Http\Controllers\AuthController::class, 'refresh']);

    Route::apiResource('listings', App\Http\Controllers\ListingController::class)->only(['show', 'store', 'update', 'destroy']);
    Route::get('my/listings', [App\Http\Controllers\ListingController::class, 'myIndex']);
    Route::post('listings/{listing}/publish', [App\Http\Controllers\ListingController::class, 'publish']);
    Route::post('listings/{listing}/unpublish', [App\Http\Controllers\ListingController::class, 'unpublish']);

    Route::post('listings/{listing}/links', [App\Http\Controllers\LinkController::class, 'store']);
    Route::delete('links/{link}', [App\Http\Controllers\LinkController::class, 'delete']);

    Route::post('listings/{listing}/like', [App\Http\Controllers\LikeController::class, 'likeListing']);
    Route::post('listings/{listing}/dislike', [App\Http\Controllers\LikeController::class, 'dislikeListing']);

    Route::post('avatars', [App\Http\Controllers\AvatarController::class, 'store']);

    Route::get('currencies', [App\Http\Controllers\CurrencyController::class, 'index']);

    Route::patch('account', [AccountController::class, 'update']);

});
Route::get('listings', [App\Http\Controllers\ListingController::class, 'index']);

Route::get('tags', [App\Http\Controllers\TagController::class, 'index']);
Route::get('genres', [App\Http\Controllers\GenreController::class, 'index']);
