<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $resources = ['roles', 'user_role_permissions'];
        $actions = ['index', 'show', 'create', 'store', 'edit', 'update', 'destroy'];


        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                $name = "{$resource}_{$action}";
                Permission::firstOrCreate(['name' => $name]);
            }
        }

        $customRoutes = [
            'user_role_permissions',
            'departments_index',
            'departments_update',
            'tabs_index',
            'tabs_update'
        ];

        foreach ($customRoutes as $route) {
            $name = "{$route}";
            Permission::firstOrCreate(['name' => $name]);
        }
    }
}
