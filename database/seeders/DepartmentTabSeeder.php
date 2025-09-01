<?php

namespace Database\Seeders;

use App\Models\SmHumanDepartment;
use App\Models\Tab;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentTabSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $scheduleDepartment = SmHumanDepartment::where('slug', 'schedule')->first();

        $allTabs = Tab::all();

        $departmentTabs = [];

        foreach ($allTabs as $tab) {
            $departmentTabs[] = [
                'department_id' => $scheduleDepartment->id,
                'tab_id' => $tab->id,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        DB::table('department_tab')->insert($departmentTabs);
    }
}
