<?php

namespace App\Http\Middleware;

use App\Http\Livewire\UserPermissions;
use App\Models\UserPermission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class EnsureUserRoleIsAllowedToAccess
{
    // dashboard, pages, navigation-menus

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $userRole = auth()->user()->role;
            $currentRouteName = Route::currentRouteName();

            if ( UserPermission::isRoleHasRightToAccess($userRole, $currentRouteName)
                || in_array($currentRouteName, $this->defaultUserAccessRole()[$userRole])
            ) {
                return $next($request);
            } else {
                abort(403, 'Unauthorized action.');
            }
        } catch (\Throwable $th) {
            abort(403, 'You are not allowed to access this page.');
        }
    }

    /**
     * The default user access role.
     *
     * @return \string[][]
     */
    private function defaultUserAccessRole()
    {
        return [
            'user' => [
                'dashboard'
            ],
            'admin' => [
                'pages',
                'navigation-menus',
                'dashboard',
                'users',
                'user-permissions',
            ]
        ];
    }
}
