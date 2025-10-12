<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('12345678'),
            ]);
        }

        Client::create([
            'name' => 'أحمد علي',
            'phone_number' => '01099999999',
            'debt' => 8000,
            'created_by' => $user->id,
        ]);

        Client::create([
            'name' => 'علي حسن',
            'phone_number' => '01188888888',
            'debt' => 14500,
            'created_by' => $user->id,
        ]);

        Client::create([
            'name' => 'محمد سعد',
            'phone_number' => '01277777777',
            'debt' => 5200,
            'created_by' => $user->id,
        ]);
    }
}
