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
        Schema::create('crm_action_for_statuses', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(\App\Models\LeadsStatus::class, 'status_id')
                ->constrained();

            $table->unsignedTinyInteger('reminder_hour');
            $table->unsignedTinyInteger('reminder_today_count');
            $table->unsignedTinyInteger('reminder_days_count');
            //  action_after_days_count
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_for_statuses');
    }
};
