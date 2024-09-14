<?php

use App\Http\Controllers\GoogleController;
use App\Http\Controllers\SpotifyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Thank you for using Laravel template for building Vibe Sync.php :)']);
});

Route::get('/google', [GoogleController::class, 'auth']);
Route::get('/callback', [GoogleController::class, 'callback']);

Route::get('/spotify', [SpotifyController::class, 'auth']);
Route::get('/spotify/callback', [SpotifyController::class, 'callback']);
