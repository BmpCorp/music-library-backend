<?php

namespace App\Providers;

use App\Enums\PermissionCode;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Telescope::night();

        $this->hideSensitiveRequestDetails();

        $isLocalOrDebug = $this->app->environment('local') || config('app.debug');

        Telescope::filter(function (IncomingEntry $entry) use ($isLocalOrDebug) {
            return $isLocalOrDebug ||
                   $entry->isReportableException() ||
                   $entry->isFailedRequest() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    protected function authorization(): void
    {
        Telescope::auth(function () {
            $user = backpack_user();
            $isAllowed = $user?->hasAnyPermission([PermissionCode::FULL_ACCESS, PermissionCode::MAINTENANCE]);

            if (!$isAllowed) {
                redirect(backpack_url('/login'))->send();
                return false;
            }

            return true;
        });
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user) {
            return $user->hasAnyPermission([PermissionCode::FULL_ACCESS, PermissionCode::MAINTENANCE]);
        });
    }
}
