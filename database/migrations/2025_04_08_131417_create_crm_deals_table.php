<?php

use App\Enums\Deals\StageType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crm_deals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('amount');
            $table->enum('stage', array_column(StageType::cases(), 'value'));

            $table->foreignIdFor(\App\Models\Contact::class)
                ->constrained('crm_contacts');

            $table->foreignIdFor(\App\Models\Account::class)
                ->constrained('crm_accounts');

            $table->date('expected_close_date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_deals');
    }
};
