<?php

use App\Enums\WorkFlow\WorkFlowType;
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
        Schema::create('crm_work_flows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', array_column(WorkFlowType::cases(), 'value'));

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_work_flows');
    }
};
