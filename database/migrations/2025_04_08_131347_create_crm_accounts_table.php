<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crm_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('industry');
            $table->string('email');
            $table->string('phone');
            $table->text('address');

            $table->unsignedBigInteger('assigned_to');
            $table->foreign('assigned_to')
                ->references('id')
                ->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_accounts');
    }
};
