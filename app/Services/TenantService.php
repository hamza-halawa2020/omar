<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Str;

class TenantService
{
    public function list()
    {
        return Tenant::latest()->get();
    }

    public function create(array $data): Tenant
    {
        $id = str_replace('-', '', \Illuminate\Support\Str::uuid()->toString());

        return Tenant::create([
            'id'     => $id,
            'name'   => $data['name'],
            'domain' => $data['domain'],
        ]);
    }

    public function delete(string $id): void
    {
        $tenant = Tenant::findOrFail($id);
        // TenancyServiceProvider listens to TenantDeleted event and automatically runs DeleteDatabase.
        $tenant->delete();
    }
}
