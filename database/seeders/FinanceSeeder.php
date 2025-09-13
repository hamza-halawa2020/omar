<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Category;
use App\Models\PaymentWay;
use App\Models\Transaction;
use App\Models\PaymentWayLog;
use App\Models\TransactionLog;

class FinanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('12345678'),
        ]);

        // $sales = Category::create([
        //     'name' => 'مبيعات',
        //     'parent_id' => null,
        //     'created_by' => $user->id,
        // ]);

        // $installments = Category::create([
        //     'name' => 'أقساط',
        //     'parent_id' => null,
        //     'created_by' => $user->id,
        // ]);

        // $subSale = Category::create([
        //     'name' => 'مبيعات نقدي',
        //     'parent_id' => $sales->id,
        //     'created_by' => $user->id,
        // ]);

        // $cash = PaymentWay::create([
        //     'name' => 'كاش',
        //     'type' => 'cash',
        //     'phone_number' => null,
        //     'limit' => null,
        //     'balance' => 10000,
        //     'created_by' => $user->id,
        // ]);

        // $wallet = PaymentWay::create([
        //     'name' => 'محفظة فودافون كاش',
        //     'type' => 'wallet',
        //     'phone_number' => '01000000000',
        //     'limit' => 6000,
        //     'balance' => 2000,
        //     'created_by' => $user->id,
        // ]);

        // $t1 = Transaction::create([
        //     'category_id' => $sales->id,
        //     'sub_category_id' => $subSale->id,
        //     'payment_way_id' => $cash->id,
        //     'created_by' => $user->id,
        //     'type' => 'receive',
        //     'amount' => 1500,
        //     'commission' => 0,
        //     'notes' => 'مبيع منتج A',
        //     'attachment' => null,
        //     'status' => 'completed',
        // ]);

        // $t2 = Transaction::create([
        //     'category_id' => $installments->id,
        //     'sub_category_id' => null,
        //     'payment_way_id' => $wallet->id,
        //     'created_by' => $user->id,
        //     'type' => 'send',
        //     'amount' => 500,
        //     'commission' => 10,
        //     'notes' => 'دفع قسط',
        //     'attachment' => null,
        //     'status' => 'pending',
        // ]);

    }
}
