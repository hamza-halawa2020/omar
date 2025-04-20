<?php

namespace App\Providers;

use App\Services\AccountServiceInterface;
use App\Services\CallServiceInterface;
use App\Services\ContactServiceInterface;
use App\Services\DealServiceInterface;
use App\Services\impl\AccountService;
use App\Services\impl\CallService;
use App\Services\impl\ContactService;
use App\Services\impl\DealService;
use App\Services\impl\LeadService;
use App\Services\impl\LeadStatusService;
use App\Services\impl\TaskService;
use App\Services\LeadServiceInterface;
use App\Services\LeadStatusServiceInterface;
use App\Services\TaskServiceInterface;
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
        $this->app->singleton(TaskServiceInterface::class, TaskService::class);
        $this->app->singleton(DealServiceInterface::class, DealService::class);
        $this->app->singleton(CallServiceInterface::class, CallService::class);
        $this->app->singleton(LeadStatusServiceInterface::class, LeadStatusService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
