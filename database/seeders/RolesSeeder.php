<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdminRole = Role::firstOrCreate(['name' => 'Super admin', 'guard_name' => 'web']);
        $superAdminRolePermissions = Permission::all();
        $superAdminRole->syncPermissions($superAdminRolePermissions);
    }
}
