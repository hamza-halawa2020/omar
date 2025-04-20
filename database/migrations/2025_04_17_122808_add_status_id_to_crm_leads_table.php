<?php

use App\Models\LeadsStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('crm_leads', function (Blueprint $table) {
            $table->foreignIdFor(App\Models\LeadsStatus::class, 'status_id')
                ->after('is_converted')
                ->default(LeadsStatus::where('is_default', true)->first()->id)
                ->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crm_leads', function (Blueprint $table) {
            $table->dropForeign('crm_leads_status_id_foreign');
        });
    }
};
