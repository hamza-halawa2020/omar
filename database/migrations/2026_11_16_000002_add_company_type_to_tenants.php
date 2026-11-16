<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'central';

    public function up(): void
    {
        Schema::connection('central')->table('tenants', function (Blueprint $table) {
            $table->string('company_type', 20)->default('general')->index();
        });
        // Preserve companies explicitly enabled under the previous implementation.
        DB::connection('central')->table('tenants')->where('umrah_enabled', true)->update(['company_type' => 'umrah']);
    }

    public function down(): void
    {
        Schema::connection('central')->table('tenants', function (Blueprint $table) {
            $table->dropIndex(['company_type']);
            $table->dropColumn('company_type');
        });
    }
};
