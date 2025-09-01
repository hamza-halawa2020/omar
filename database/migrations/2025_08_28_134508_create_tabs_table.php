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
     Schema::create('tabs', function (Blueprint $table) {
        $table->id();
        $table->string('label');
        $table->string('url')->nullable();
        $table->string('icon')->nullable();
        $table->unsignedBigInteger('parent_id')->nullable(); // لو منيو فرعية
        $table->string('permission_required')->nullable(); // اسم الـ permission المطلوب
        $table->integer('order')->default(0); // لترتيب المنيو
        $table->timestamps();

        $table->foreign('parent_id')->references('id')->on('tabs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabs');
    }
};
