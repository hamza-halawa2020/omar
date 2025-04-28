<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // CRM Permissions
        Permission::create(['name' => 'view_dashboard', 'guard_name' => 'web']);
        Permission::create(['name' => 'view_customers', 'guard_name' => 'web']);
        Permission::create(['name' => 'create_customers', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit_customers', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete_customers', 'guard_name' => 'web']);
        Permission::create(['name' => 'view_sales', 'guard_name' => 'web']);
        Permission::create(['name' => 'create_sales', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit_sales', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete_sales', 'guard_name' => 'web']);

        // ERP Permissions
        Permission::create(['name' => 'view_inventory', 'guard_name' => 'web']);
        Permission::create(['name' => 'create_inventory', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit_inventory', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete_inventory', 'guard_name' => 'web']);
        Permission::create(['name' => 'view_purchases', 'guard_name' => 'web']);
        Permission::create(['name' => 'create_purchases', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit_purchases', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete_purchases', 'guard_name' => 'web']);
        Permission::create(['name' => 'manage_finances', 'guard_name' => 'web']);

        // LMS Permissions
        Permission::create(['name' => 'view_courses', 'guard_name' => 'web']);
        Permission::create(['name' => 'create_courses', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit_courses', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete_courses', 'guard_name' => 'web']);
        Permission::create(['name' => 'view_students', 'guard_name' => 'web']);
        Permission::create(['name' => 'create_students', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit_students', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete_students', 'guard_name' => 'web']);
        Permission::create(['name' => 'manage_enrollments', 'guard_name' => 'web']);

        // Common Permissions for Admin
        Permission::create(['name' => 'manage_users', 'guard_name' => 'web']);
        Permission::create(['name' => 'manage_roles', 'guard_name' => 'web']);
        Permission::create(['name' => 'manage_settings', 'guard_name' => 'web']);
    }
}
