<?php

namespace Database\Seeders;

use App\Models\Tab;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TabsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $dashboard = Tab::create([
            'label' => 'Dashboard',
            'url' => '/dashboard',
            'icon' => 'fas fa-home',
            'permission_required' => 'view dashboard',
            'order' => 1
        ]);

        $users = Tab::create([
            'label' => 'Users',
            'url' => null,
            'icon' => 'fas fa-users',
            'permission_required' => null,
            'order' => 2
        ]);

        Tab::create([
            'label' => 'All Users',
            'url' => '/users',
            'icon' => 'fas fa-user',
            'parent_id' => $users->id,
            'permission_required' => 'view users',
            'order' => 1
        ]);

        Tab::create([
            'label' => 'Create User',
            'url' => '/users/create',
            'icon' => 'fas fa-user-plus',
            'parent_id' => $users->id,
            'permission_required' => 'create user',
            'order' => 2
        ]);
    }
}
