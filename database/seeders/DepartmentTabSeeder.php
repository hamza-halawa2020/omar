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
            ['department_id' => 39, 'tab_id' => 7],
            ['department_id' => 39, 'tab_id' => 8],
            ['department_id' => 39, 'tab_id' => 9],
            ['department_id' => 39, 'tab_id' => 10],
            ['department_id' => 39, 'tab_id' => 11],
        ];

        DB::table('department_tab')->insert($departmentTabs);
    }
}
