<?php

use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\Cors;
use App\Http\Middleware\EnsureAuthOrImpersonation;
use App\Http\Middleware\EnsureSuperAdminAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR |
                     Request::HEADER_X_FORWARDED_HOST |
                     Request::HEADER_X_FORWARDED_PORT |
                     Request::HEADER_X_FORWARDED_PROTO |
                     Request::HEADER_X_FORWARDED_AWS_ELB
        );

        $middleware->alias([
            'check.permission' => CheckPermission::class,
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
            'cors' => Cors::class,
            'tenancy' => \App\Http\Middleware\InitializeTenancyBySession::class,
            'auth.admin' => EnsureSuperAdminAuthenticated::class,
            'auth.or_impersonate' => EnsureAuthOrImpersonation::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);
    })->create();