<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('iphone_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('transaction_id')->nullable()->after('iphone_id');
            $table->unsignedBigInteger('payment_way_id')->nullable()->after('transaction_id');
            $table->unsignedBigInteger('client_id')->nullable()->after('payment_way_id');
            $table->index(['transaction_id', 'payment_way_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::table('iphone_logs', function (Blueprint $table) {
            $table->dropIndex(['transaction_id', 'payment_way_id', 'client_id']);
            $table->dropColumn(['transaction_id', 'payment_way_id', 'client_id']);
        });
    }
};
