<?php

use App\Enums\Calls\OutcomeType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crm_calls', function (Blueprint $table) {
            $table->id();
            $table->morphs('related_to');

            $table->string('subject');
            $table->dateTime('call_time');
            $table->integer('duration_in_minutes');

            $table->text('notes')
                ->nullable();

            $table->enum('outcome', array_column(OutcomeType::cases(), 'value'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_calls');
    }
};
