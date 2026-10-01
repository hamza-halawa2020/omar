<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class SystemRateLimit
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('rate_limiting.system.enabled', true)) {
            return $next($request);
        }

        $maxAttempts = max(1, (int) config('rate_limiting.system.max_attempts', 120));
        $decaySeconds = max(1, (int) config('rate_limiting.system.decay_seconds', 60));
        $key = $this->resolveKey($request);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = RateLimiter::availableIn($key);

            return $this->tooManyRequestsResponse($request, $retryAfter);
        }

        RateLimiter::hit($key, $decaySeconds);

        return $next($request);
    }

    private function resolveKey(Request $request): string
    {
        $tenantId = $request->hasSession()
            ? $request->session()->get('tenant_id', 'central')
            : 'api';
        $actor = $request->user()
            ? 'user:'.$request->user()->getAuthIdentifier()
            : 'ip:'.$request->ip();

        return sha1("system-rate-limit|tenant:{$tenantId}|{$actor}");
    }

    private function tooManyRequestsResponse(Request $request, int $retryAfter): Response
    {
        $headers = ['Retry-After' => $retryAfter];

        if ($request->expectsJson()) {
            return response()->json([
                'status' => false,
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => $retryAfter,
            ], 429, $headers);
        }

        return response('Too many requests. Please try again later.', 429, $headers);
    }
}
