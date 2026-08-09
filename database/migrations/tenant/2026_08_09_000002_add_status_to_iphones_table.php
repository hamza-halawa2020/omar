<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('iphones', function (Blueprint $table) {
            $table->string('status')->default('available')->after('net_profit_after_sale');
        });
    }

    public function down(): void
    {
        Schema::table('iphones', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
