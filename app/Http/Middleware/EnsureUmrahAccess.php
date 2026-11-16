<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUmrahAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user('web');
        abort_unless($user && tenancy()->initialized
            && (string) $user->tenant_id === (string) tenant('id')
            && tenant()->isUmrah(), 403);

        return $next($request);
    }
}
