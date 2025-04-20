<?php

use App\Enums\Leads\FlagType;
use App\Enums\Leads\SourceType;
use App\Enums\Leads\StatusType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crm_leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('industry');
            $table->string('address');
            $table->string('position');
            $table->enum('source', array_column(SourceType::cases(), 'value'));

            $table->unsignedBigInteger('assigned_to');
            $table->foreign('assigned_to')
                ->references('id')
                ->on('users');


            $table->text('notes')
                ->nullable();

            $table->enum('flag', array_column(FlagType::cases(), 'value'));

            $table->timestamp('last_follow_up')->nullable();    //  last time I had contact with the lead
            $table->timestamp('next_follow_up')->nullable();    //  next lead call time
            $table->boolean('is_follow_up')->default(false);    // when the next follow-up time arrives, did you call the user
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_leads');
    }
};
