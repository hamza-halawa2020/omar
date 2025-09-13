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
        Schema::create('payment_ways', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('sub_category_id')->nullable()->constrained('categories')->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['cash', 'wallet', 'balance_machine']);
            $table->string('phone_number')->nullable();
            $table->decimal('send_limit', 12, 2)->nullable();
            $table->decimal('send_limit_alert', 12, 2)->nullable();
            $table->decimal('receive_limit', 12, 2)->nullable();
            $table->decimal('receive_limit_alert', 12, 2)->nullable();
            $table->decimal('balance', 12, 2)->default(0);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_ways');
    }
};
