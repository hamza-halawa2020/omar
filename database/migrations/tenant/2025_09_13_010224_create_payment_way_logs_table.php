<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_way_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_way_id')->constrained('payment_ways')->cascadeOnDelete();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->enum('action', ['create', 'update', 'delete']);
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_way_logs');
    }
};
