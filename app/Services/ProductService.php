<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ProductService
{
    public function __construct(private readonly FileService $fileService) {}

    public function list(Request $request): LengthAwarePaginator
    {
        $relations = [];
        $perPage = min(max((int) $request->input('per_page', 25), 1), 100);

        if ($request->boolean('with_batches') && Schema::hasTable('product_purchase_batches')) {
            $relations['purchaseBatches'] = fn ($query) => $query
                ->select(['id', 'product_id', 'remaining_quantity', 'unit_cost', 'created_at'])
                ->where('remaining_quantity', '>', 0)
                ->orderBy('created_at')
                ->orderBy('id');
        }

        return Product::query()
            ->select([
                'id',
                'name',
                'code',
                'image',
                'description',
                'purchase_price',
                'sale_price',
                'stock',
                'created_by',
                'created_at',
                'updated_at',
            ])
            ->with([
                'creator:id,name,email',
                ...$relations,
            ])
            ->when($request->filled('code'), function ($q) use ($request) {
                $q->where('code', $request->code);
            })
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%'.$request->search.'%')
                        ->orWhere('code', 'like', '%'.$request->search.'%')
                        ->orWhere('stock', 'like', '%'.$request->search.'%');
                });
            })
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function codes(): Collection
    {
        return Product::query()
            ->whereNotNull('code')
            ->where('code', '!=', '')
            ->distinct()
            ->orderBy('code')
            ->pluck('code');
    }

    public function create(array $data, Request $request): Product
    {
        $data['created_by'] = Auth::id();
        $data['image'] = $request->hasFile('image')
            ? $this->fileService->storePublicFile($request->file('image'), 'uploads/products')
            : null;

        return Product::create(array_filter($data, fn ($value) => $value !== null));
    }

    public function show(int $id): Product
    {
        return Product::with(['creator'])->findOrFail($id);
    }

    public function details(int $id): array
    {
        $hasBatches = Schema::hasTable('product_purchase_batches');
        $hasCostTotal = Schema::hasTable('transaction_products') && Schema::hasColumn('transaction_products', 'cost_total');

        $productRelations = ['installmentContracts.client'];

        if ($hasBatches) {
            $productRelations['purchaseBatches'] = fn ($query) => $query
                ->with('transactionProduct.transaction')
                ->orderBy('created_at')
                ->orderBy('id');
        }

        $product = Product::with($productRelations)->findOrFail($id);

        $transactionRelations = ['products.product', 'paymentWay', 'client'];
        if ($hasBatches) {
            $transactionRelations[] = 'products.batchAllocations.purchaseBatch';
        }

        $transactions = Transaction::with($transactionRelations)
            ->whereHas('products', fn ($query) => $query->where('product_id', $product->id))
            ->latest()
            ->get()
            ->map(function ($transaction) use ($product, $hasCostTotal) {
                $line = $transaction->products->firstWhere('product_id', $product->id);
                $quantity = (int) ($line->quantity ?? 1);
                $cost = $transaction->type === 'receive'
                    ? (float) ($hasCostTotal ? ($line->cost_total ?? 0) : ($quantity * (float) ($product->purchase_price ?? 0)))
                    : 0;

                $transaction->product_line = $line;
                $transaction->quantity = $quantity;
                $transaction->sale_cost = $cost;
                $transaction->sale_profit = $transaction->type === 'receive'
                    ? (float) ($line->total ?? $transaction->amount) + (float) $transaction->commission - $cost
                    : null;

                return $transaction;
            });

        $purchaseBatches = $hasBatches ? $product->purchaseBatches : collect();
        $salesTransactions = $transactions->where('type', 'receive')->values();
        $purchaseTransactions = $transactions->where('type', 'send')->values();
        $totalCost = $hasBatches
            ? (float) $purchaseBatches->sum(fn ($batch) => (int) $batch->remaining_quantity * (float) $batch->unit_cost)
            : (float) ($product->stock * $product->purchase_price);

        $salesAmount = (float) $salesTransactions->sum(fn ($transaction) => (float) optional($transaction->product_line)->total);
        $salesCommission = (float) $salesTransactions->sum('commission');
        $salesCost = (float) $salesTransactions->sum('sale_cost');
        $salesProfit = $salesAmount + $salesCommission - $salesCost;
        $purchasedAmount = (float) $purchaseTransactions->sum(fn ($transaction) => (float) optional($transaction->product_line)->total);

        return [
            'product' => $product,
            'totalCost' => $totalCost,
            'purchaseBatches' => $purchaseBatches,
            'installmentContracts' => $product->installmentContracts,
            'transactions' => $transactions,
            'salesTransactions' => $salesTransactions,
            'purchaseTransactions' => $purchaseTransactions,
            'summary' => [
                'sales_amount' => $salesAmount,
                'sales_commission' => $salesCommission,
                'sales_cost' => $salesCost,
                'sales_profit' => $salesProfit,
                'sold_quantity' => (int) $salesTransactions->sum('quantity'),
                'purchased_amount' => $purchasedAmount,
                'purchased_quantity' => (int) $purchaseTransactions->sum('quantity'),
            ],
        ];
    }

    public function update(int $id, array $data, Request $request): Product
    {
        $product = Product::findOrFail($id);

        if ($request->hasFile('image')) {
            $this->fileService->deletePublicFile($product->image);
            $data['image'] = $this->fileService->storePublicFile($request->file('image'), 'uploads/products');
        }

        $product->update($data);

        return $product;
    }

    public function delete(int $id): Product
    {
        $product = Product::findOrFail($id);

        if ($product->installmentContracts()->exists()) {
            throw new HttpResponseException(
                response()->json(['status' => false, 'message' => __('messages.cannot_delete_Product_with_installments')], 400)
            );
        }

        $product->delete();

        return $product;
    }
}
