<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureSuperAdminAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('web')->check()) {
            Auth::guard('admin')->logout();

            if ($request->wantsJson()) {
                return response()->json(['status' => false, 'message' => __('messages.admin.admin_access_only')], 403);
            }

            return redirect()->route('admin.login');
        }

        if (! Auth::guard('admin')->check()) {
            if ($request->wantsJson()) {
                return response()->json(['status' => false, 'message' => __('messages.admin.unauthorized')], 401);
            }

            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
