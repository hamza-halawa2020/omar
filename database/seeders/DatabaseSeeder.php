<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin in central DB
        $this->call(AdminSeeder::class);

        // -------------------------------------------------------------
        // Tenant 1: Demo Company (example.com)
        // -------------------------------------------------------------
        $tenant1 = Tenant::firstOrCreate(
            ['domain' => 'example.com'],
            [
                'id'   => str_replace('-', '', Str::uuid()->toString()),
                'name' => 'Demo Company',
            ]
        );

        // Seed Tenant 1 DB (Permissions, Roles, Finance Data)
        $tenant1->run(function () {
            $this->call([
                PermissionSeeder::class,
                RolesSeeder::class,
                FinanceSeeder::class,
            ]);

            // Create custom roles inside Tenant 1
            $managerRole = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
            $managerRole->syncPermissions([
                'clients_index', 'clients_show', 'clients_store', 'clients_update',
                'products_index', 'products_show', 'products_store',
                'transactions_index', 'transactions_store',
            ]);

            $employeeRole = Role::firstOrCreate(['name' => 'Employee', 'guard_name' => 'web']);
            $employeeRole->syncPermissions([
                'clients_index', 'clients_show',
                'products_index', 'products_show',
            ]);
        });

        // Create Users for Tenant 1 in Central DB
        $tenant1Users = [
            [
                'email'    => 'admin@example.com',
                'name'     => 'Super Admin (Demo Co)',
                'password' => bcrypt('12345678'),
                'role'     => 'Super admin',
            ],
            [
                'email'    => 'manager@example.com',
                'name'     => 'Manager (Demo Co)',
                'password' => bcrypt('12345678'),
                'role'     => 'Manager',
            ],
            [
                'email'    => 'employee@example.com',
                'name'     => 'Employee (Demo Co)',
                'password' => bcrypt('12345678'),
                'role'     => 'Employee',
            ],
        ];

        foreach ($tenant1Users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name'      => $userData['name'],
                    'password'  => $userData['password'],
                    'tenant_id' => $tenant1->id,
                ]
            );

            // Assign role inside Tenant 1 DB
            $tenant1->run(function () use ($user, $userData) {
                $user->setConnection('tenant');
                $user->syncRoles($userData['role']);
            });
        }

        // -------------------------------------------------------------
        // Tenant 2: Store Company (store.com)
        // -------------------------------------------------------------
        $tenant2 = Tenant::firstOrCreate(
            ['domain' => 'store.com'],
            [
                'id'   => str_replace('-', '', Str::uuid()->toString()),
                'name' => 'Store Company',
            ]
        );

        // Seed Tenant 2 DB
        $tenant2->run(function () {
            $this->call([
                PermissionSeeder::class,
                RolesSeeder::class,
                FinanceSeeder::class,
            ]);
        });

        // Create Users for Tenant 2 in Central DB
        $tenant2Users = [
            [
                'email'    => 'admin@store.com',
                'name'     => 'Super Admin (Store Co)',
                'password' => bcrypt('12345678'),
                'role'     => 'Super admin',
            ],
            [
                'email'    => 'user@store.com',
                'name'     => 'Standard User (Store Co)',
                'password' => bcrypt('12345678'),
                'role'     => 'Super admin',
            ],
        ];

        foreach ($tenant2Users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name'      => $userData['name'],
                    'password'  => $userData['password'],
                    'tenant_id' => $tenant2->id,
                ]
            );

            // Assign role inside Tenant 2 DB
            $tenant2->run(function () use ($user, $userData) {
                $user->setConnection('tenant');
                $user->syncRoles($userData['role']);
            });
        }
    }
}
