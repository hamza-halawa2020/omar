<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Add resource route names (used for permissions like roles_index, roles_create, etc.)
        $resources = [
            'roles',
            'user_role_permissions',
        ];

        $actions = ['index', 'show', 'create', 'update', 'delete'];

        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                $name = "{$resource}_{$action}";
                Permission::firstOrCreate(['name' => $name]);
            }
        }

        $customRoutes = [
            'assign_roles',
        ];

        foreach ($customRoutes as $route) {
            Permission::firstOrCreate(['name' => $route]);
        }
    }
}
