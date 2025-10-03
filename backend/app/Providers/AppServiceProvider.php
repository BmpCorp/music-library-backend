<?php

namespace App\Providers;

use App\Http\Controllers\Admin\RoleCrudController;
use App\Http\Controllers\Admin\UserCrudController;
use Backpack\PermissionManager\app\Http\Controllers\RoleCrudController as PermissionManagerRoleCrudController;
use Backpack\PermissionManager\app\Http\Controllers\UserCrudController as PermissionManagerUserCrudController;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::addNamespace('backpack', resource_path('views/vendor/backpack'));

        // Permission manager customizations
        $this->app->bind(PermissionManagerUserCrudController::class, UserCrudController::class);
        $this->app->bind(PermissionManagerRoleCrudController::class, RoleCrudController::class);
    }
}
