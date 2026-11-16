<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UmrahPermissionSeeder extends Seeder
{
    public const ROLE_NAMES = ['umrah_manager', 'umrah_bookings', 'umrah_accountant', 'umrah_procedures', 'umrah_viewer'];

    public function run(): void
    {
        foreach (config('umrah.permissions') as $name) {
            Permission::findOrCreate($name, 'web');
        }
        $roles = [
            'umrah_manager' => config('umrah.permissions'),
            'umrah_bookings' => ['umrah_access', 'umrah_bookings_manage', 'umrah_passports_view'],
            'umrah_accountant' => ['umrah_access', 'umrah_payments_collect', 'umrah_payments_confirm', 'umrah_reports_view'],
            'umrah_procedures' => ['umrah_access', 'umrah_procedures_manage', 'umrah_passports_view'],
            'umrah_viewer' => ['umrah_access'],
        ];
        foreach ($roles as $name => $permissions) {
            Role::findOrCreate($name, 'web')->givePermissionTo($permissions);
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
