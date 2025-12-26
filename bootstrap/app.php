<?php

use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\CheckProjectAccess;
use App\Http\Middleware\Cors;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureUserIsActive;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'user.active' => EnsureUserIsActive::class,
            'check.permission' => CheckPermission::class,
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
            'CheckProjectAccess' => CheckProjectAccess::class,
            'cors' => Cors::class,
            
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);
    })->create();
