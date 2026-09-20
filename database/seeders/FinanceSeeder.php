<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        // Users are stored in central DB — get the first admin user for this tenant
        $user = \DB::connection('central')->table('users')
            ->where('tenant_id', tenant('id'))
            ->first();

        $userId = $user?->id ?? 1;

        // Clients
        $client1 = Client::create([
            'name' => 'Ahmed Ali',
            'phone_number' => '01099999999',
            'debt' => 8000,
            'created_by' => $userId,
        ]);

        $client2 = Client::create([
            'name' => 'ali Hassan',
            'phone_number' => '01188888888',
            'debt' => 14500,
            'created_by' => $userId,
        ]);

    }
}
