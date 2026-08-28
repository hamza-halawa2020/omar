<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductAnalyticsService
{
    public function analytics(Request $request): array
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $productId = $request->filled('product_id') ? (int) $request->get('product_id') : null;

        $products = Product::query()
            ->select('id', 'name', 'code')
            ->orderBy('name')
            ->get();

        $productRows = $this->productRows($fromDate, $toDate, $productId);
        $totals = $this->totals($productRows);
        $salesTransactions = $this->salesTransactions($fromDate, $toDate, $productId);

        return compact('fromDate', 'toDate', 'productId', 'products', 'productRows', 'totals', 'salesTransactions');
    }

    private function productRows(string $fromDate, string $toDate, ?int $productId): Collection
    {
        $query = Product::query()
            ->leftJoin('transactions', function ($join) use ($fromDate, $toDate) {
                $join->on('products.id', '=', 'transactions.product_id')
                    ->whereDate('transactions.created_at', '>=', $fromDate)
                    ->whereDate('transactions.created_at', '<=', $toDate);
            })
            ->when($productId, fn ($query) => $query->where('products.id', $productId))
            ->select([
                'products.id',
                'products.name',
                'products.code',
                'products.purchase_price',
                'products.sale_price',
                'products.stock',
            ])
            ->selectRaw('COUNT(transactions.id) as total_transactions')
            ->selectRaw("SUM(CASE WHEN transactions.type = 'receive' THEN 1 ELSE 0 END) as sales_count")
            ->selectRaw("SUM(CASE WHEN transactions.type = 'receive' THEN COALESCE(transactions.quantity, 1) ELSE 0 END) as sold_quantity")
            ->selectRaw("SUM(CASE WHEN transactions.type = 'receive' THEN COALESCE(transactions.amount, 0) ELSE 0 END) as sales_amount")
            ->selectRaw("SUM(CASE WHEN transactions.type = 'receive' THEN COALESCE(transactions.commission, 0) ELSE 0 END) as sales_commission")
            ->selectRaw("SUM(CASE WHEN transactions.type = 'receive' THEN COALESCE(transactions.quantity, 1) * COALESCE(products.purchase_price, 0) ELSE 0 END) as sales_cost")
            ->selectRaw("SUM(CASE WHEN transactions.type = 'send' THEN 1 ELSE 0 END) as purchase_count")
            ->selectRaw("SUM(CASE WHEN transactions.type = 'send' THEN COALESCE(transactions.quantity, 1) ELSE 0 END) as purchased_quantity")
            ->selectRaw("SUM(CASE WHEN transactions.type = 'send' THEN COALESCE(transactions.amount, 0) ELSE 0 END) as purchase_amount")
            ->groupBy('products.id', 'products.name', 'products.code', 'products.purchase_price', 'products.sale_price', 'products.stock')
            ->orderByDesc(DB::raw("(SUM(CASE WHEN transactions.type = 'receive' THEN COALESCE(transactions.amount, 0) + COALESCE(transactions.commission, 0) - (COALESCE(transactions.quantity, 1) * COALESCE(products.purchase_price, 0)) ELSE 0 END))"));

        if (! $productId) {
            $query->havingRaw('COUNT(transactions.id) > 0');
        }

        return $query->get()->map(function ($row) {
            $row->total_transactions = (int) $row->total_transactions;
            $row->sales_count = (int) $row->sales_count;
            $row->sold_quantity = (int) $row->sold_quantity;
            $row->sales_amount = (float) $row->sales_amount;
            $row->sales_commission = (float) $row->sales_commission;
            $row->sales_cost = (float) $row->sales_cost;
            $row->purchase_count = (int) $row->purchase_count;
            $row->purchased_quantity = (int) $row->purchased_quantity;
            $row->purchase_amount = (float) $row->purchase_amount;
            $row->gross_profit = $row->sales_amount - $row->sales_cost;
            $row->net_profit = $row->sales_amount + $row->sales_commission - $row->sales_cost;
            $row->profit_margin = $row->sales_amount > 0 ? ($row->net_profit / $row->sales_amount) * 100 : 0;

            return $row;
        });
    }

    private function totals(Collection $productRows): array
    {
        $salesAmount = (float) $productRows->sum('sales_amount');
        $netProfit = (float) $productRows->sum('net_profit');

        return [
            'products_count' => $productRows->count(),
            'sales_count' => (int) $productRows->sum('sales_count'),
            'sold_quantity' => (int) $productRows->sum('sold_quantity'),
            'sales_amount' => $salesAmount,
            'sales_commission' => (float) $productRows->sum('sales_commission'),
            'sales_cost' => (float) $productRows->sum('sales_cost'),
            'gross_profit' => (float) $productRows->sum('gross_profit'),
            'net_profit' => $netProfit,
            'profit_margin' => $salesAmount > 0 ? ($netProfit / $salesAmount) * 100 : 0,
            'purchase_amount' => (float) $productRows->sum('purchase_amount'),
            'purchased_quantity' => (int) $productRows->sum('purchased_quantity'),
        ];
    }

    private function salesTransactions(string $fromDate, string $toDate, ?int $productId): LengthAwarePaginator
    {
        return Transaction::query()
            ->with(['product', 'paymentWay', 'client'])
            ->where('type', 'receive')
            ->whereNotNull('product_id')
            ->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate)
            ->when($productId, fn ($query) => $query->where('product_id', $productId))
            ->latest()
            ->paginate(25)
            ->through(function (Transaction $transaction) {
                $quantity = (int) ($transaction->quantity ?? 1);
                $purchasePrice = (float) optional($transaction->product)->purchase_price;
                $cost = $quantity * $purchasePrice;

                $transaction->analytics_quantity = $quantity;
                $transaction->analytics_cost = $cost;
                $transaction->analytics_profit = (float) $transaction->amount + (float) $transaction->commission - $cost;

                return $transaction;
            });
    }
}
