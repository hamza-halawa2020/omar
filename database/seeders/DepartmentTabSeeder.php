<?php

namespace Database\Seeders;

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
        $departmentTabs = [

            ['department_id' => 35, 'tab_id' => 1],
            ['department_id' => 35, 'tab_id' => 2],
            ['department_id' => 35, 'tab_id' => 3],

   
            ['department_id' => 35, 'tab_id' => 2],
            ['department_id' => 35, 'tab_id' => 4],

    
            ['department_id' => 35, 'tab_id' => 1],
            ['department_id' => 35, 'tab_id' => 5],
        ];

        DB::table('department_tab')->insert($departmentTabs);
    }
}
