<?php

namespace App\Providers;

use App\Http\Middleware\SystemRateLimit;
use App\Models\Client as ClientModel;
use App\Observers\ClientObserver;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Router $router): void
    {
        ClientModel::observe(ClientObserver::class);

        $router->pushMiddlewareToGroup('web', SystemRateLimit::class);
        $router->pushMiddlewareToGroup('api', SystemRateLimit::class);
    }
}
