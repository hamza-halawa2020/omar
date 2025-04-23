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
        Schema::table('crm_calls', function (Blueprint $table) {
            $table->unsignedBigInteger('call_status_id')->nullable();            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crm_calls', function (Blueprint $table) {
            $table->dropForeign(['call_status_id']);
            $table->dropColumn('call_status_id');
        });
    }
};
