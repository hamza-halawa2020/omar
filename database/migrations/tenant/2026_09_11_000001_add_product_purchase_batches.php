<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('transaction_products', 'cost_total')) {
            Schema::table('transaction_products', function (Blueprint $table) {
                $table->decimal('cost_total', 12, 2)->default(0)->after('total');
            });
        }

        if (! Schema::hasTable('product_purchase_batches')) {
            Schema::create('product_purchase_batches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id');
                $table->foreignId('transaction_product_id')->nullable();
                $table->unsignedInteger('purchased_quantity')->default(0);
                $table->unsignedInteger('remaining_quantity')->default(0);
                $table->decimal('unit_cost', 12, 2)->default(0);
                $table->timestamps();

                $table->foreign('product_id', 'ppb_product_fk')->references('id')->on('products')->cascadeOnDelete();
                $table->foreign('transaction_product_id', 'ppb_tx_product_fk')->references('id')->on('transaction_products')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('transaction_product_batch_allocations')) {
            Schema::create('transaction_product_batch_allocations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('transaction_product_id');
                $table->foreignId('product_purchase_batch_id')->nullable();
                $table->unsignedInteger('quantity')->default(0);
                $table->decimal('unit_cost', 12, 2)->default(0);
                $table->decimal('total', 12, 2)->default(0);
                $table->timestamps();

                $table->foreign('transaction_product_id', 'tpba_tx_product_fk')->references('id')->on('transaction_products')->cascadeOnDelete();
                $table->foreign('product_purchase_batch_id', 'tpba_batch_fk')->references('id')->on('product_purchase_batches')->nullOnDelete();
            });
        }

        if (
            DB::table('product_purchase_batches')->count() === 0
            && DB::table('transaction_product_batch_allocations')->count() === 0
        ) {
            $this->backfillBatchCosts();
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_product_batch_allocations');
        Schema::dropIfExists('product_purchase_batches');

        if (Schema::hasColumn('transaction_products', 'cost_total')) {
            Schema::table('transaction_products', function (Blueprint $table) {
                $table->dropColumn('cost_total');
            });
        }
    }

    private function backfillBatchCosts(): void
    {
        $lines = DB::table('transaction_products')
            ->join('transactions', 'transactions.id', '=', 'transaction_products.transaction_id')
            ->leftJoin('products', 'products.id', '=', 'transaction_products.product_id')
            ->orderBy('transactions.created_at')
            ->orderBy('transaction_products.id')
            ->select([
                'transaction_products.id',
                'transaction_products.product_id',
                'transaction_products.quantity',
                'transaction_products.unit_price',
                'transaction_products.total',
                'transactions.type',
                'transaction_products.created_at',
                'transaction_products.updated_at',
                'products.purchase_price',
            ])
            ->get();

        foreach ($lines as $line) {
            if (! $line->product_id) {
                continue;
            }

            $quantity = max((int) $line->quantity, 1);

            if ($line->type === 'send') {
                DB::table('transaction_products')
                    ->where('id', $line->id)
                    ->update(['cost_total' => (float) $line->total]);

                DB::table('product_purchase_batches')->insert([
                    'product_id' => $line->product_id,
                    'transaction_product_id' => $line->id,
                    'purchased_quantity' => $quantity,
                    'remaining_quantity' => $quantity,
                    'unit_cost' => (float) $line->unit_price,
                    'created_at' => $line->created_at,
                    'updated_at' => $line->updated_at,
                ]);

                continue;
            }

            if ($line->type !== 'receive') {
                continue;
            }

            $remaining = $quantity;
            $costTotal = 0;
            $batches = DB::table('product_purchase_batches')
                ->where('product_id', $line->product_id)
                ->where('remaining_quantity', '>', 0)
                ->orderBy('created_at')
                ->orderBy('id')
                ->get();

            foreach ($batches as $batch) {
                if ($remaining <= 0) {
                    break;
                }

                $allocatedQuantity = min($remaining, (int) $batch->remaining_quantity);
                $allocatedTotal = $allocatedQuantity * (float) $batch->unit_cost;
                $costTotal += $allocatedTotal;
                $remaining -= $allocatedQuantity;

                DB::table('product_purchase_batches')
                    ->where('id', $batch->id)
                    ->decrement('remaining_quantity', $allocatedQuantity);

                DB::table('transaction_product_batch_allocations')->insert([
                    'transaction_product_id' => $line->id,
                    'product_purchase_batch_id' => $batch->id,
                    'quantity' => $allocatedQuantity,
                    'unit_cost' => (float) $batch->unit_cost,
                    'total' => $allocatedTotal,
                    'created_at' => $line->created_at,
                    'updated_at' => $line->updated_at,
                ]);
            }

            if ($remaining > 0) {
                $fallbackUnitCost = (float) ($line->purchase_price ?? $line->unit_price ?? 0);
                $fallbackTotal = $remaining * $fallbackUnitCost;
                $costTotal += $fallbackTotal;

                DB::table('transaction_product_batch_allocations')->insert([
                    'transaction_product_id' => $line->id,
                    'product_purchase_batch_id' => null,
                    'quantity' => $remaining,
                    'unit_cost' => $fallbackUnitCost,
                    'total' => $fallbackTotal,
                    'created_at' => $line->created_at,
                    'updated_at' => $line->updated_at,
                ]);
            }

            DB::table('transaction_products')
                ->where('id', $line->id)
                ->update(['cost_total' => $costTotal]);
        }
    }
};
