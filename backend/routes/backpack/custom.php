<?php

use App\Http\Controllers\Admin\AlbumCrudController;
use App\Http\Controllers\Admin\ArtistCrudController;
use App\Http\Controllers\Admin\CountryCrudController;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Admin\UserCrudController;
use App\Http\Controllers\Admin\UserFavoriteArtistCrudController;
use App\Http\Middleware\CheckForMaintenanceAccess;
use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\CRUD.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace' => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::group([
        'prefix' => 'metrics',
        'middleware' => [CheckForMaintenanceAccess::class],
    ], function () {
        Route::get('php', [MaintenanceController::class, 'getPhpInfo']);
        Route::get('php-fpm', [MaintenanceController::class, 'getPhpFpmStatus']);
        Route::get('mysql', [MaintenanceController::class, 'getMysqlVars']);
    });

    Route::crud('user', UserCrudController::class);
    Route::crud('country', CountryCrudController::class);
    Route::crud('artist', ArtistCrudController::class);
    Route::crud('album', AlbumCrudController::class);
    Route::crud('user-favorite-artist', UserFavoriteArtistCrudController::class);
}); // this should be the absolute last line of this file

/**
 * DO NOT ADD ANYTHING HERE.
 */
