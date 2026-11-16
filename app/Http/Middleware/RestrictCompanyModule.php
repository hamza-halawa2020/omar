<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictCompanyModule
{
    public function handle(Request $request, Closure $next)
    {
        if (tenant()?->isUmrah()
            && ! $request->routeIs('umrah.*', 'logout', 'dashboard.profile.*')) {
            if ($request->isMethod('GET') && ! $request->expectsJson()) {
                return redirect()->route('umrah.index');
            }
            abort(403);
        }

        return $next($request);
    }
}
