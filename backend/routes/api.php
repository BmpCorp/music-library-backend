<?php

use App\Http\Controllers\Api\AlbumController;
use App\Http\Controllers\Api\ArtistController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {
    // Guest access
    Route::get('/artist', [ArtistController::class, 'index']);
    Route::get('/artist/{id}', [ArtistController::class, 'show']);
    Route::get('/album', [AlbumController::class, 'index']);
    Route::get('/album/{id}', [AlbumController::class, 'show']);
    Route::post('/user/login', [UserController::class, 'login'])->name('login');

    // Authorization needed
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/artist/{id}/favorite', [ArtistController::class, 'toggleFavorite']);
        Route::post('/artist/{id}/listening-now', [ArtistController::class, 'toggleListeningNow']);
        Route::post('/artist/{artistId}/check-album/{albumId}', [ArtistController::class, 'setLastCheckedAlbum']);
        Route::get('/user', [UserController::class, 'index']);
        Route::post('/user/logout', [UserController::class, 'logout']);
    });
});
