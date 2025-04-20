<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crm_leads_statuses', function (Blueprint $table) {
            $table->id();
            $table->text('name');

            $table->foreignIdFor(App\Models\LeadsStatus::class, 'parent_id')
                ->nullable()
                ->constrained();

            $table->boolean('is_default')->default(false);

            $table->timestamps();
        });

        Artisan::call('db:seed', [
            '--class' => \Database\Seeders\LeadsStatusSeeder::class,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_leads_statuses');
    }
};
