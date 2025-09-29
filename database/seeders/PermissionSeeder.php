<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {

        $permissions = [
            'categories_index',
            'categories_store',
            'categories_update',
            'categories_destroy',

            'clients_index',
            'clients_debts',
            'clients_store',
            'clients_show',
            'clients_update',
            'clients_destroy',

            'installments_index',
            'installments_store',
            'installments_show',
            'installments_update',
            'installments_pay',
            'installments_destroy',

            'payment_ways_index',
            'payment_ways_store',
            'payment_ways_show',
            'payment_ways_update',
            'payment_ways_destroy',

            'payment_way_logs_index',

            'products_index',
            'products_store',
            'products_show',
            'products_update',
            'products_destroy',

            'transactions_index',
            'transactions_store',
            'transactions_show',

            'transaction_logs_index',

            'users_index',
            'users_store',
            'users_show',
            'users_update',
            'users_destroy',

            'roles_index',
            'roles_store',
            'roles_update',
            'roles_destroy',
        ];

        foreach ($permissions as $permission) {
            $name = "{$permission}";
            Permission::firstOrCreate(['name' => $name]);
        }
    }
}
