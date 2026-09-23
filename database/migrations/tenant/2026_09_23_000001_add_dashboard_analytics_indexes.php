<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['type', 'created_at', 'payment_way_id'], 'transactions_type_created_payment_way_idx');
            $table->index(['type', 'created_at'], 'transactions_type_created_idx');
        });

        Schema::table('installments', function (Blueprint $table) {
            $table->index(['status', 'due_date'], 'installments_status_due_date_idx');
        });

        Schema::table('payment_way_limits', function (Blueprint $table) {
            $table->index(['year', 'month', 'payment_way_id'], 'payment_way_limits_year_month_payment_way_idx');
        });
    }

    public function down(): void
    {
        Schema::table('payment_way_limits', function (Blueprint $table) {
            $table->dropIndex('payment_way_limits_year_month_payment_way_idx');
        });

        Schema::table('installments', function (Blueprint $table) {
            $table->dropIndex('installments_status_due_date_idx');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_type_created_idx');
            $table->dropIndex('transactions_type_created_payment_way_idx');
        });
    }
};
