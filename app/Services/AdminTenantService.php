<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolesSeeder;
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

        try {
            $tenant = Tenant::create([
                'id'     => $id,
                'name'   => $name,
                'domain' => $domain,
            ]);

            $tenant->run(function () {
                app(PermissionSeeder::class)->run();
                app(RolesSeeder::class)->run();
            });

            return $tenant;
        } catch (\Throwable $e) {
            Log::error('Tenant provisioning failed for tenant ' . $id . ': ' . $e->getMessage());

            $tenant = Tenant::find($id);
            if ($tenant) {
                // The TenantDeleted listener drops the tenant database if it was already created.
                $tenant->delete();
            }

            throw $e;
        }
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
