<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\Umrah\Notification;
use App\Services\UmrahNotificationService;
use App\Support\UmrahTenantContext;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class SendUmrahNotifications extends Command
{
    protected $signature = 'umrah:send-notifications {--tenant= : Process one tenant only}';

    protected $description = 'Send pending Umrah WhatsApp outbox records in each tenant context';

    public function handle(): int
    {
        if (! Schema::connection('central')->hasColumn('tenants', 'company_type')) {
            $this->info('Umrah central migration has not been installed yet.');

            return self::SUCCESS;
        }
        $failed = false;
        Tenant::query()->where('company_type', 'umrah')
            ->when($this->option('tenant'), fn ($q, $id) => $q->whereKey($id))
            ->each(function (Tenant $tenant) use (&$failed) {
                try {
                    UmrahTenantContext::run($tenant, function () {
                        if (! Schema::hasTable('umrah_notifications')) {
                            return;
                        }
                        Notification::where('status', 'pending')->orderBy('id')->limit(100)->pluck('id')
                            ->each(fn ($id) => app(UmrahNotificationService::class)->deliver($id));
                    });
                } catch (\Throwable) {
                    $failed = true;
                    $this->error('Could not process tenant '.$tenant->id.'. Pending messages remain in the outbox.');
                }
            });

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
