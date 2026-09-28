<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\ClientObserver;
use App\Models\Client as ClientModel;
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
    public function boot(): void
    {
        ClientModel::observe(ClientObserver::class);
    }
}
