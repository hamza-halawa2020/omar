<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'central';

    public function up(): void
    {
        Schema::connection('central')->table('tenants', function (Blueprint $table) {
            $table->boolean('umrah_enabled')->default(false);
        });
        Schema::connection('central')->table('users', function (Blueprint $table) {
            $table->boolean('umrah_only')->default(false);
            $table->string('default_module', 20)->default('general');
        });
    }

    public function down(): void
    {
        Schema::connection('central')->table('users', fn (Blueprint $table) => $table->dropColumn(['umrah_only', 'default_module']));
        Schema::connection('central')->table('tenants', fn (Blueprint $table) => $table->dropColumn('umrah_enabled'));
    }
};
