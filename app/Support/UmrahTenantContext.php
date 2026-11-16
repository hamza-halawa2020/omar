<?php

namespace App\Support;

use App\Models\Tenant;
use Spatie\Permission\PermissionRegistrar;

final class UmrahTenantContext
{
    public static function run(Tenant $tenant, callable $callback): mixed
    {
        $previous = tenant();
        $previousKey = config('permission.cache.key');
        try {
            tenancy()->initialize($tenant);
            config(['permission.cache.key' => 'spatie.permission.cache.tenant.'.$tenant->id]);
            app(PermissionRegistrar::class)->initializeCache();
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            return $callback();
        } finally {
            if ($previous) {
                tenancy()->initialize($previous);
            } else {
                tenancy()->end();
            }
            config(['permission.cache.key' => $previousKey]);
            app(PermissionRegistrar::class)->initializeCache();
            app(PermissionRegistrar::class)->clearPermissionsCollection();
        }
    }
}
