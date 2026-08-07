<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TenantService
{
    public function list()
    {
        return Tenant::latest()->get();
    }

    public function create(array $data): Tenant
    {
        $id = str_replace('-', '', Str::uuid()->toString());

        try {
            $tenant = Tenant::create([
                'id'     => $id,
                'name'   => $data['name'],
                'domain' => $data['domain'],
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
                $tenant->delete();
            }

            throw $e;
        }
    }

    public function delete(string $id): void
    {
        $tenant = Tenant::findOrFail($id);
        $userIds = User::on('central')->where('tenant_id', $tenant->id)->pluck('id');

        if ($userIds->isNotEmpty()) {
            DB::connection('central')->table('sessions')->whereIn('user_id', $userIds)->delete();
        }

        User::on('central')->where('tenant_id', $tenant->id)->delete();

        // TenancyServiceProvider listens to TenantDeleted event and automatically runs DeleteDatabase.
        $tenant->delete();
    }
}
