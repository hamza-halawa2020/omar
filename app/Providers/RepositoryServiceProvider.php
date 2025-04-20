<?php

namespace App\Providers;

use App\Repositories\AccountRepositoryInterface;
use App\Repositories\CallRepositoryInterface;
use App\Repositories\ContactRepositoryInterface;
use App\Repositories\DealRepositoryInterface;
use App\Repositories\impl\AccountRepository;
use App\Repositories\impl\CallRepository;
use App\Repositories\impl\ContactRepository;
use App\Repositories\impl\DealRepository;
use App\Repositories\impl\LeadRepository;
use App\Repositories\impl\LeadStatusRepository;
use App\Repositories\impl\TaskRepository;
use App\Repositories\LeadRepositoryInterface;
use App\Repositories\LeadStatusRepositoryInterface;
use App\Repositories\TaskRepositoryInterface;
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
        $this->app->singleton(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->singleton(DealRepositoryInterface::class, DealRepository::class);
        $this->app->singleton(CallRepositoryInterface::class, CallRepository::class);
        $this->app->singleton(LeadStatusRepositoryInterface::class, LeadStatusRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
