<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRoleOrPermission
{
    public function handle(Request $request, Closure $next, $role = null, $permission = null)
    {
        if ($role && !Auth::user()->hasRole($role)) {
            abort(403, 'You do not have the required role to access this page.');
        }

        if ($permission && !Auth::user()->can($permission)) {
            abort(403, 'You do not have the required permission to access this page.');
        }

        return $next($request);
    }
}
