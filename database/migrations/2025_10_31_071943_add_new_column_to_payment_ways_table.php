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
        Schema::table('payment_ways', function (Blueprint $table) {
            $table->enum('client_type',['client','merchant'])->default('client')->after('balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_ways', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
