<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('umrah_trips', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('departure_date');
            $table->date('return_date');
            $table->unsignedInteger('capacity');
            $table->string('transport')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
        Schema::create('umrah_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('umrah_trips')->restrictOnDelete();
            $table->string('name');
            $table->string('makkah_hotel')->nullable();
            $table->string('madinah_hotel')->nullable();
            $table->unsignedSmallInteger('makkah_nights')->default(0);
            $table->unsignedSmallInteger('madinah_nights')->default(0);
            $table->text('services')->nullable();
            $table->timestamps();
        });
        Schema::create('umrah_program_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('umrah_programs')->restrictOnDelete();
            $table->string('label');
            $table->decimal('amount', 12, 2);
            $table->char('currency', 3)->default('EGP');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('umrah_travelers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('english_name')->nullable();
            $table->string('phone', 40);
            $table->string('nationality', 100)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('passport_number', 60)->nullable()->unique();
            $table->date('passport_expires_at')->nullable();
            $table->string('passport_location')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
        Schema::create('umrah_bookings', function (Blueprint $table) {
            $table->id();
            $table->uuid('reference')->unique();
            $table->foreignId('traveler_id')->constrained('umrah_travelers')->restrictOnDelete();
            $table->foreignId('trip_id')->constrained('umrah_trips')->restrictOnDelete();
            $table->foreignId('program_price_id')->constrained('umrah_program_prices')->restrictOnDelete();
            $table->json('agreement');
            $table->decimal('total_amount', 12, 2);
            $table->char('currency', 3);
            $table->string('status', 20)->default('confirmed')->index();
            $table->string('visa_status', 20)->default('not_started');
            $table->string('ticket_status', 20)->default('not_issued');
            $table->string('travel_status', 20)->default('not_traveled');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->index(['trip_id', 'status']);
        });
        Schema::create('umrah_payment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('umrah_bookings')->restrictOnDelete();
            $table->date('due_date');
            $table->decimal('amount', 12, 2);
            $table->timestamps();
        });
        Schema::create('umrah_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('umrah_bookings')->restrictOnDelete();
            $table->uuid('receipt_number')->unique();
            $table->uuid('submission_token')->unique();
            $table->string('type', 20);
            $table->string('status', 20)->default('confirmed');
            $table->decimal('amount', 12, 2);
            $table->string('method', 60);
            $table->string('external_reference')->nullable();
            $table->date('paid_at');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('confirmed_by')->nullable();
            $table->timestamps();
        });
        Schema::create('umrah_booking_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('umrah_bookings')->restrictOnDelete();
            $table->string('action', 60);
            $table->json('data');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
        Schema::create('umrah_passport_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('traveler_id')->constrained('umrah_travelers')->restrictOnDelete();
            $table->string('location');
            $table->string('recipient');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
        Schema::create('umrah_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('traveler_id')->constrained('umrah_travelers')->restrictOnDelete();
            $table->string('label');
            $table->string('path');
            $table->string('original_name');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['umrah_documents', 'umrah_passport_movements', 'umrah_booking_logs', 'umrah_payments', 'umrah_payment_schedules', 'umrah_bookings', 'umrah_travelers', 'umrah_program_prices', 'umrah_programs', 'umrah_trips'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
