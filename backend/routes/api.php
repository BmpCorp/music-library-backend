<?php

use App\Http\Controllers\Api\ArtistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {
    // Guest access
    Route::get('/artist', [ArtistController::class, 'index']);
    Route::get('/artist/{id}', [ArtistController::class, 'show']);

    // Authorization needed
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', fn (Request $request) => $request->user);
    });
});
