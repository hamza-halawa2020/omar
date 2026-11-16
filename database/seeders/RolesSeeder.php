<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (tenant()?->isUmrah()) {
            $this->call(UmrahPermissionSeeder::class);

            return;
        }
        $superAdminRole = Role::firstOrCreate(['name' => 'Super admin', 'guard_name' => 'web']);
        $superAdminRolePermissions = Permission::all()->filter(fn ($permission) => ! str_starts_with($permission->name, 'umrah_'));
        $superAdminRole->syncPermissions($superAdminRolePermissions);
    }
}
