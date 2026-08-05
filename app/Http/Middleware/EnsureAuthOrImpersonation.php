<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAuthOrImpersonation
{
    /**
     * Allow access if the user is authenticated via web guard.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('web')->check()) {
            return $next($request);
        }

        if ($request->wantsJson()) {
            return response()->json(['status' => false, 'message' => 'Unauthorized.'], 401);
        }

        return redirect()->route('login');
    }
}
