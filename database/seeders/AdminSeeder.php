<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Create the first Super Admin record in the central DB.
     * Requirements: 1.2
     */
    public function run(): void
    {
        Admin::firstOrCreate(
            ['email' => 'superadmin@system.com'],
            [
                'name'     => 'Super Admin',
                'password' => bcrypt('12345678'),
            ]
        );
    }
}
