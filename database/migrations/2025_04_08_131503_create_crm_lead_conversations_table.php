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
        Schema::create('crm_lead_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Lead::class)
                ->constrained('crm_leads');

            $table->foreignIdFor(\App\Models\Contact::class)
                ->constrained('crm_contacts');

            $table->foreignIdFor(\App\Models\Account::class)
                ->constrained('crm_accounts');

           $table->foreignIdFor(\App\Models\Deal::class)
               ->constrained('crm_deals');

            $table->unsignedInteger('converted_by');
            $table->foreign('converted_by')
                ->references('id')
                ->on('users');

            $table->timestamp('converted_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_lead_conversations');
    }
};
