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
        // Create Admin role and assign permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'Super admin', 'guard_name' => 'web']);
        $superAdminRolePermissions = Permission::all();
        $superAdminRole->syncPermissions($superAdminRolePermissions);

        // Create Admin role and assign permissions
        // $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        // $adminPermissions = Permission::all();
        // $adminRole->syncPermissions($adminPermissions);

        // // Create Manager role and assign permissions
        // $managerRole = Role::create(['name' => 'manager', 'guard_name' => 'web']);
        // $managerPermissions = Permission::whereIn('name', [
        //     'view_dashboard', 'view_customers', 'create_customers', 'edit_customers', 'delete_customers',
        //     'view_sales', 'create_sales', 'edit_sales', 'delete_sales',
        //     'view_inventory', 'create_inventory', 'edit_inventory', 'delete_inventory',
        //     'view_purchases', 'create_purchases', 'edit_purchases', 'delete_purchases'
        // ])->get();
        // $managerRole->syncPermissions($managerPermissions);

        // // Create User role and assign permissions
        // $userRole = Role::create(['name' => 'user', 'guard_name' => 'web']);
        // $userPermissions = Permission::whereIn('name', [
        //     'view_dashboard', 'view_customers', 'view_sales', 'view_inventory', 'view_purchases',
        //     'view_courses', 'view_students'
        // ])->get();
        // $userRole->syncPermissions($userPermissions);

    }
}
