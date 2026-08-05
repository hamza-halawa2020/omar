<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminTenantService
{
    /**
     * Create a new tenant and run migrations on its database.
     * Requirements: 3.1, 3.2
     */
    public function createTenant(string $name, string $domain): Tenant
    {
        $id = str_replace('-', '', Str::uuid()->toString());

        $tenant = Tenant::create([
            'id'     => $id,
            'name'   => $name,
            'domain' => $domain,
        ]);

        try {
            Artisan::call('tenants:migrate', [
                '--tenants' => [$tenant->id],
            ]);
        } catch (\Throwable $e) {
            Log::error('tenants:migrate failed for tenant ' . $tenant->id . ': ' . $e->getMessage());
            // Rollback: delete the tenant record that was just created
            $tenant->delete();
            throw $e;
        }

        return $tenant;
    }

    /**
     * Delete a tenant along with its users and database.
     * Requirements: 7.1, 7.2, 7.3
     */
    public function deleteTenant(Tenant $tenant): void
    {
        // Invalidate active sessions for users of this tenant (Requirement 7.3)
        $userIds = User::on('central')->where('tenant_id', $tenant->id)->pluck('id');

        if ($userIds->isNotEmpty()) {
            DB::connection('central')
                ->table('sessions')
                ->whereIn('user_id', $userIds)
                ->delete();
        }

        // Delete all users belonging to this tenant from central DB (Requirement 7.2)
        User::on('central')->where('tenant_id', $tenant->id)->delete();

        // Delete tenant record — HasDatabase concern drops the DB via TenantDeleted event
        $tenant->delete();
    }
}
