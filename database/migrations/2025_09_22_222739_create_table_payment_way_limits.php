<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_way_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_way_id')->constrained('payment_ways')->cascadeOnDelete();
            $table->integer('month');
            $table->integer('year');
            $table->decimal('send_limit', 15, 2)->default(0); 
            $table->decimal('send_used', 15, 2)->default(0); 
            $table->decimal('receive_limit', 15, 2)->default(0); 
            $table->decimal('receive_used', 15, 2)->default(0); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_payment_way_limits');
    }
};
