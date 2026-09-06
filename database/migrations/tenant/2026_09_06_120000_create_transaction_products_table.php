<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();
        });

        DB::table('transactions')
            ->whereNotNull('product_id')
            ->orderBy('id')
            ->select(['id', 'product_id', 'quantity', 'amount', 'created_at', 'updated_at'])
            ->chunk(100, function ($transactions) {
                foreach ($transactions as $transaction) {
                    $quantity = max((int) ($transaction->quantity ?: 1), 1);
                    $amount = (float) ($transaction->amount ?: 0);

                    DB::table('transaction_products')->insert([
                        'transaction_id' => $transaction->id,
                        'product_id' => $transaction->product_id,
                        'quantity' => $quantity,
                        'unit_price' => $quantity > 0 ? $amount / $quantity : 0,
                        'total' => $amount,
                        'created_at' => $transaction->created_at,
                        'updated_at' => $transaction->updated_at,
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_products');
    }
};
