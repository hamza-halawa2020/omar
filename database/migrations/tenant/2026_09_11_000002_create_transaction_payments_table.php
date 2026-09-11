<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transaction_payments')) {
            return;
        }

        Schema::create('transaction_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->foreignId('payment_way_id')->constrained('payment_ways')->cascadeOnDelete();
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('balance_before_transaction', 12, 2)->nullable();
            $table->decimal('balance_after_transaction', 12, 2)->nullable();
            $table->timestamps();

            $table->index(['payment_way_id', 'transaction_id'], 'tp_way_tx_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_payments');
    }
};
