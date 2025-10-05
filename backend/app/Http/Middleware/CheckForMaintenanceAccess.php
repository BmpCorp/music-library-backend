<?php

namespace App\Http\Middleware;

use App\Enums\PermissionCode;
use Closure;

class CheckForMaintenanceAccess
{
    /**
     * Answer to unauthorized access request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    private function respondToUnauthorizedRequest($request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response(trans('backpack::base.unauthorized'), 401);
        } else {
            return redirect()->to(backpack_url());
        }
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (backpack_auth()->guest()) {
            return $this->respondToUnauthorizedRequest($request);
        }

        if (!backpack_user()->canAny([PermissionCode::FULL_ACCESS, PermissionCode::MAINTENANCE])) {
            return $this->respondToUnauthorizedRequest($request);
        }

        return $next($request);
    }
}
