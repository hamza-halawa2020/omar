<?php

namespace App\Providers;

use App\Repositories\AccountRepositoryInterface;
use App\Repositories\ContactRepositoryInterface;
use App\Repositories\impl\AccountRepository;
use App\Repositories\impl\ContactRepository;
use App\Repositories\impl\LeadRepository;
use App\Repositories\LeadRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(LeadRepositoryInterface::class, LeadRepository::class);
        $this->app->singleton(ContactRepositoryInterface::class, ContactRepository::class);
        $this->app->singleton(AccountRepositoryInterface::class, AccountRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
