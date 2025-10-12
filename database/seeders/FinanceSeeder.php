<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Client;
use App\Models\Installment;
use App\Models\InstallmentContract;
use App\Models\InstallmentPayment;
use App\Models\PaymentWay;
use App\Models\PaymentWayLimit;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('12345678'),
        ]);

        // // Categories
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

        // $banks = Category::create([
        //     'name' => 'حسابات بنكية',
        //     'parent_id' => null,
        //     'created_by' => $user->id,
        // ]);

        // $subBank = Category::create([
        //     'name' => 'CIB',
        //     'parent_id' => $banks->id,
        //     'created_by' => $user->id,
        // ]);

        // $wallets = Category::create([
        //     'name' => 'محافظ',
        //     'parent_id' => null,
        //     'created_by' => $user->id,
        // ]);

        // $vodafone = Category::create([
        //     'name' => 'Vodafone Cash',
        //     'parent_id' => $wallets->id,
        //     'created_by' => $user->id,
        // ]);

        // // Payment Ways
        // $cibWallet = PaymentWay::create([
        //     'category_id' => $banks->id,
        //     'sub_category_id' => $subBank->id,
        //     'name' => 'CIB Wallet',
        //     'type' => 'wallet',
        //     'phone_number' => '01012345678',
        //     'send_limit' => 10000,
        //     'send_limit_alert' => 8000,
        //     'receive_limit' => 20000,
        //     'receive_limit_alert' => 15000,
        //     'balance' => 5000,
        //     'created_by' => $user->id,
        // ]);

        // $cash = PaymentWay::create([
        //     'category_id' => $sales->id,
        //     'sub_category_id' => null,
        //     'name' => 'Cash',
        //     'type' => 'cash',
        //     'phone_number' => null,
        //     'send_limit' => 0,
        //     'send_limit_alert' => 0,
        //     'receive_limit' => 0,
        //     'receive_limit_alert' => 0,
        //     'balance' => 10000,
        //     'created_by' => $user->id,
        // ]);

        // $instaPay = PaymentWay::create([
        //     'category_id' => $wallets->id,
        //     'sub_category_id' => $vodafone->id,
        //     'name' => 'Instapay',
        //     'type' => 'wallet',
        //     'phone_number' => '01055555555',
        //     'send_limit' => 15000,
        //     'send_limit_alert' => 12000,
        //     'receive_limit' => 30000,
        //     'receive_limit_alert' => 25000,
        //     'balance' => 2000,
        //     'created_by' => $user->id,
        // ]);

        // PaymentWayLimit::create([
        //     'payment_way_id' => $cibWallet->id,
        //     'month' => date('m'),
        //     'year' => date('Y'),
        //     'send_limit' => 10000,
        //     'send_used' => 2000,
        //     'receive_limit' => 20000,
        //     'receive_used' => 4000,
        // ]);

        // Clients
        $client1 = Client::create([
            'name' => 'Ahmed Ali',
            'phone_number' => '01099999999',
            'debt' => 8000,
            'created_by' => $user->id,
        ]);

        $client2 = Client::create([
            'name' => 'ali Hassan',
            'phone_number' => '01188888888',
            'debt' => 14500,
            'created_by' => $user->id,
        ]);

        // Products
        // $product1 = Product::create([
        //     'name' => 'تلاجة شارب 16 قدم',
        //     'description' => 'تلاجة موفرة للطاقة',
        //     'purchase_price' => 8000,
        //     'sale_price' => 10000,
        //     'stock' => 10,
        //     'created_by' => $user->id,
        // ]);

        // $product2 = Product::create([
        //     'name' => 'موبايل سامسونج A55',
        //     'description' => 'ذاكرة 256GB',
        //     'purchase_price' => 12000,
        //     'sale_price' => 14500,
        //     'stock' => 20,
        //     'created_by' => $user->id,
        // ]);

        // // Transactions
        // $transaction1 = Transaction::create([
        //     'payment_way_id' => $cash->id,
        //     'created_by' => $user->id,
        //     'type' => 'receive',
        //     'amount' => 400,
        //     'commission' => 0,
        //     'notes' => 'Installment Payment - Ahmed',
        //     'client_id' => $client1->id,
        // ]);

        // $transaction2 = Transaction::create([
        //     'payment_way_id' => $cibWallet->id,
        //     'created_by' => $user->id,
        //     'type' => 'receive',
        //     'amount' => 600,
        //     'commission' => 5,
        //     'notes' => 'Installment Payment - Ahmed',
        //     'client_id' => $client1->id,
        // ]);

        // $transaction3 = Transaction::create([
        //     'payment_way_id' => $instaPay->id,
        //     'created_by' => $user->id,
        //     'type' => 'receive',
        //     'amount' => 1200,
        //     'commission' => 10,
        //     'notes' => 'Installment Payment - ali',
        //     'client_id' => $client2->id,
        // ]);

        // TransactionLog::create([
        //     'transaction_id' => $transaction1->id,
        //     'created_by' => $user->id,
        //     'action' => 'create',
        //     'data' => $transaction1->toArray(),
        // ]);

        // TransactionLog::create([
        //     'transaction_id' => $transaction2->id,
        //     'created_by' => $user->id,
        //     'action' => 'create',
        //     'data' => $transaction2->toArray(),
        // ]);

        // TransactionLog::create([
        //     'transaction_id' => $transaction3->id,
        //     'created_by' => $user->id,
        //     'action' => 'create',
        //     'data' => $transaction3->toArray(),
        // ]);

        // Installment Contracts
        // $contract1 = InstallmentContract::create([
        //     'total_amount' => 10000,
        //     'product_price' => 10000,
        //     'installment_count' => 10,
        //     'installment_amount' => 1000,
        //     'down_payment' => 2000,
        //     'start_date' => '2025-01-01',
        //     'client_id' => $client1->id,
        //     'product_id' => $product1->id,
        //     'created_by' => $user->id,
        // ]);

        // $contract2 = InstallmentContract::create([
        //     'total_amount' => 14500,
        //     'product_price' => 14500,
        //     'installment_count' => 5,
        //     'installment_amount' => 2900,
        //     'down_payment' => 0,
        //     'start_date' => '2025-03-01',
        //     'client_id' => $client2->id,
        //     'product_id' => $product2->id,
        //     'created_by' => $user->id,
        // ]);

        // // Installments - Ahmed
        // $installment1 = Installment::create([
        //     'due_date' => '2025-02-01',
        //     'required_amount' => 1000,
        //     'paid_amount' => 1000,
        //     'status' => 'paid',
        //     'installment_contract_id' => $contract1->id,
        // ]);

        // InstallmentPayment::create([
        //     'installment_id' => $installment1->id,
        //     'transaction_id' => $transaction1->id,
        //     'amount' => 400,
        //     'payment_date' => '2025-02-01',
        //     'paid_by' => $user->id,
        // ]);

        // InstallmentPayment::create([
        //     'installment_id' => $installment1->id,
        //     'transaction_id' => $transaction2->id,
        //     'amount' => 600,
        //     'payment_date' => '2025-02-10',
        //     'paid_by' => $user->id,
        // ]);

        // // Installments - ali
        // $installment2 = Installment::create([
        //     'due_date' => '2025-04-01',
        //     'required_amount' => 2900,
        //     'paid_amount' => 1200,
        //     'status' => 'pending',
        //     'installment_contract_id' => $contract2->id,
        // ]);

        // InstallmentPayment::create([
        //     'installment_id' => $installment2->id,
        //     'transaction_id' => $transaction3->id,
        //     'amount' => 1200,
        //     'payment_date' => '2025-04-05',
        //     'paid_by' => $user->id,
        // ]);
    }
}
