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

   
        $schedule = Tab::create([
            'label' => 'Schedule',
            'url' => null,
            'icon' => 'fas fa-users',
            'permission_required' => null,
            'order' => 2
        ]);

        Tab::create([
            'label' => 'Schedule Calendar',
            'url' => config('app.Retention_URL') . 'calendar',
            'icon' => 'fas fa-user',
            'parent_id' => $schedule->id,
             'permission_required' => 'academy_schedule_index',
            'order' => 1
        ]);

        Tab::create([
            'label' => 'Schedule',
            'url' => config('app.Retention_URL') . 'schedule',
            'icon' => 'fas fa-user-plus',
            'parent_id' => $schedule->id,
            'permission_required' => 'academy_schedule_index',
            'order' => 1
        ]);
        Tab::create([
            'label' => 'live-sessions',
            'url' => config('app.Retention_URL') . 'live-sessions',
            'icon' => 'fas fa-user-plus',
            'parent_id' => $schedule->id,
           'permission_required' => 'academy_schedule_index',
            'order' => 3
        ]);
        Tab::create([
            'label' => 'courses',
            'url' => config('app.Retention_URL') . 'courses',
            'icon' => 'fas fa-user-plus',
            'parent_id' => $schedule->id,
             'permission_required' => 'academy_schedule_index',
            'order' => 4
        ]);
    }
}
