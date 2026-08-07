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
            if (! Auth::guard('web')->user()->is_active) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->wantsJson()) {
                    return response()->json(['status' => false, 'message' => __('messages.admin.account_inactive')], 403);
                }

                return redirect()->route('login')->withErrors(['login' => __('messages.admin.account_inactive')]);
            }

            return $next($request);
        }

        if ($request->wantsJson()) {
            return response()->json(['status' => false, 'message' => 'Unauthorized.'], 401);
        }

        return redirect()->route('login');
    }
}
