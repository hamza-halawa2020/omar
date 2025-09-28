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
        Schema::create('installment_contracts', function (Blueprint $table) {
            $table->id();
            $table->decimal('product_price', 10, 2); // سعر المنتج
            $table->decimal('down_payment', 10, 2)->default(0); // الدفعة المقدمة
            $table->decimal('remaining_amount', 10, 2)->default(0); // باقى مبلغ التقسيط
            $table->integer('installment_count');  // عدد الأقساط  -- عدد الشهور اللي هيدفع فيها العميل
            $table->decimal('interest_rate', 5, 2)->default(0); // نسبة الفايدة %
            $table->decimal('interest_amount', 10, 2)->default(0); // مبلغ الفايدة
            $table->decimal('total_amount', 10, 2); // المبلغ الإجمالي الكلي (سعر المنتج + الفوائد)
            $table->decimal('installment_amount', 10, 2); // قيمة القسط الشهري
            $table->date('start_date'); // تاريخ بداية الأقساط
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installment_contracts');
    }
};
