<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureSuperAdminAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::guard('admin')->check()) {
            if ($request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Unauthorized.'], 401);
            }

            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
