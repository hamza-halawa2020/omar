<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iphones', function (Blueprint $table) {
            $table->id();
            $table->string('device_type');
            $table->text('device_details')->nullable();
            $table->decimal('purchase_price_sar', 12, 2)->nullable();
            $table->string('currency', 20)->default('SAR');
            $table->decimal('purchase_price_egp', 12, 2);
            $table->decimal('extra_expenses', 12, 2)->default(0);
            $table->decimal('total_purchase_with_expenses', 12, 2)->default(0);
            $table->decimal('sale_price_egp', 12, 2)->nullable();
            $table->decimal('net_profit_after_sale', 12, 2)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iphones');
    }
};
