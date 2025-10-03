<?php

namespace App\Providers;

use App\Http\Controllers\Admin\UserCrudController;
use App\Models\Permission;
use Backpack\PermissionManager\app\Http\Controllers\UserCrudController as PermissionManagerUserCrudController;
use Backpack\PermissionManager\app\Models\Permission as PermissionManagerPermission;
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
    }
}
