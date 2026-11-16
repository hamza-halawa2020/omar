<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TenantService
{
    public function list()
    {
        return Tenant::latest()->get();
    }

    public function create(array $data): Tenant
    {
        return app(AdminTenantService::class)->createTenant($data['name'], $data['domain'], $data['company_type']);
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
