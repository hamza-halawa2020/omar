<?php

namespace App\Providers;

use App\Services\AccountServiceInterface;
use App\Services\ContactServiceInterface;
use App\Services\impl\AccountService;
use App\Services\impl\ContactService;
use App\Services\impl\LeadService;
use App\Services\LeadServiceInterface;
use Illuminate\Support\ServiceProvider;

class ServiceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(LeadServiceInterface::class, LeadService::class);
        $this->app->singleton(ContactServiceInterface::class, ContactService::class);
        $this->app->singleton(AccountServiceInterface::class, AccountService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
