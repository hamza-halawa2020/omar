<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('umrah_travelers', function (Blueprint $table) {
            $table->boolean('whatsapp_enabled')->default(true);
        });
        Schema::create('umrah_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('traveler_id')->constrained('umrah_travelers')->restrictOnDelete();
            $table->string('event', 60);
            $table->string('event_key')->unique();
            $table->string('phone', 40);
            $table->text('message');
            $table->string('status', 30)->default('pending')->index();
            $table->unsignedInteger('attempts')->default(0);
            $table->string('result_code')->nullable();
            $table->string('provider_message_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamp('attempted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umrah_notifications');
        Schema::table('umrah_travelers', fn (Blueprint $table) => $table->dropColumn('whatsapp_enabled'));
    }
};
