<?php

namespace App\Services;

use App\Models\Client;
use App\Models\PaymentWay;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\Concerns\HandlesTransactionConcurrency;
use App\Services\Concerns\HandlesWalletMonthlyLimits;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\WhatsAppService;

class TransactionService
{
    use HandlesTransactionConcurrency;
    use HandlesWalletMonthlyLimits;

    public function __construct(private readonly FileService $fileService)
    {
    }

    public function paginatedForIndex(Request $request): array
    {
        $fromDate = $request->get('from_date', now()->isoFormat('YYYY-MM-DD'));
        $toDate = $request->get('to_date', now()->isoFormat('YYYY-MM-DD'));

        $transactions = Transaction::with(['paymentWay', 'client', 'creator', 'logs', 'products.product'])
            ->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate)
            ->latest()
            ->paginate(50);

        return compact('transactions', 'fromDate', 'toDate');
    }

    public function list(): Collection
    {
        return Transaction::with(['paymentWay', 'client', 'creator', 'logs', 'products.product'])->latest()->get();
    }

    public function store(array $data, Request $request): Transaction
    {
        $data['created_by'] = Auth::id();

        return $this->withTransactionSubmissionLock('manual-transaction', $data, function () use ($data, $request) {
            $data['attachment'] = $request->hasFile('attachment')
                ? $this->fileService->storePublicFile($request->file('attachment'), 'uploads/transactions')
                : null;
            $productItems = $this->resolveProductItems($data);
            $firstProductItem = $productItems->first();

            $data['product_id'] = $firstProductItem['product']->id ?? null;
            $data['quantity'] = $firstProductItem['quantity'] ?? null;

            $client = !empty($data['client_id']) ? Client::findOrFail($data['client_id']) : null;
            $total = $data['amount'] + ($data['commission'] ?? 0);
            unset($data['products']);

            return DB::transaction(function () use ($data, $client, $productItems, $total) {
                $paymentWay = $this->lockedPaymentWay($data['payment_way_id']);
                $monthlyLimit = $this->lockedCurrentMonthlyLimit($paymentWay);
                $this->assertPaymentWayCanHandleTransaction($paymentWay, $monthlyLimit, $data['type'], $data['amount'], $total);

                $transaction = Transaction::create(array_filter($data, fn($value) => $value !== null));

                $transaction->balance_before_transaction = $paymentWay->balance;
                if ($data['type'] === 'send') {
                    $transaction->balance_after_transaction = $paymentWay->balance - $total;
                } elseif ($data['type'] === 'receive') {
                    $transaction->balance_after_transaction = $paymentWay->balance + $total;
                } else {
                    $transaction->balance_after_transaction = $paymentWay->balance;
                }
                $transaction->save();

                if ($data['type'] === 'send') {
                    $this->createProductLines($transaction, $productItems, $data['type']);

                    if ($client && $productItems->isEmpty()) {
                        $client->source_model = $transaction;
                        $client->log_description = __('messages.transaction_created_successfully');
                        $client->increment('debt', $data['amount']);
                    }

                    $paymentWay->decrement('balance', $total);

                    if ($monthlyLimit) {
                        $monthlyLimit->increment('send_used', $data['amount']);
                    }
                } elseif ($data['type'] === 'receive') {
                    $this->createProductLines($transaction, $productItems, $data['type']);

                    if ($client && $productItems->isEmpty()) {
                        $client->source_model = $transaction;
                        $client->log_description = __('messages.transaction_created_successfully');
                        $client->decrement('debt', $data['amount']);
                    }

                    $paymentWay->increment('balance', $total);

                    if ($monthlyLimit) {
                        $monthlyLimit->increment('receive_used', $total);
                    }
                }

                $transaction->logs()->create([
                    'created_by' => Auth::id(),
                    'action' => 'create',
                    'data' => [
                        'transaction' => [
                            'id' => $transaction->id,
                            'type' => $transaction->type,
                            'amount' => $transaction->amount,
                            'commission' => $transaction->commission,
                            'notes' => $transaction->notes,
                            'attachment' => $transaction->attachment,
                        ],
                        'client' => [
                            'id' => optional($client)->id,
                            'name' => optional($client)->name,
                        ],
                        'products' => $this->productItemsForLog($productItems),
                        'payment_way' => [
                            'id' => $paymentWay->id,
                            'name' => $paymentWay->name,
                            'category' => optional($paymentWay->category)->name,
                            'sub_category' => optional($paymentWay->subCategory)->name,
                            'creator' => optional($paymentWay->creator)->name,
                        ],
                    ],
                ]);

                $whatsapp = null;
                if ($client) {
                    $context = [];
                    if ($productItems->isNotEmpty()) {
                        $context['product'] = $productItems
                            ->map(fn (array $item) => $item['product']->name . ' x' . $item['quantity'])
                            ->implode(', ');
                    }
                    $whatsapp = app(WhatsAppService::class)->sendTransactionMessage($client, $data['amount'], $data['type'], $context);
                }

                $transaction->setAttribute('whatsapp', $whatsapp);

                return $transaction->load(['paymentWay', 'client', 'creator', 'products.product']);
            });
        });
    }

    public function show(int $id): Transaction
    {
        return Transaction::with(['paymentWay', 'client', 'product', 'products.product', 'creator', 'logs'])->findOrFail($id);
    }

    private function resolveProductItems(array $data)
    {
        $items = collect($data['products'] ?? [])
            ->filter(fn (array $item) => !empty($item['product_id']))
            ->groupBy(fn (array $item) => (int) $item['product_id'])
            ->map(fn ($items, int $productId) => [
                'product_id' => $productId,
                'quantity' => $items->sum(fn (array $item) => max((int) ($item['quantity'] ?? 1), 1)),
            ])
            ->map(function (array $item) use ($data) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];
                $unitPrice = (float) ($data['type'] === 'send' ? $product->purchase_price : $product->sale_price);

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total' => $unitPrice * $quantity,
                ];
            })
            ->values();

        if ($items->isEmpty() && !empty($data['product_id'])) {
            $product = Product::findOrFail($data['product_id']);
            $quantity = max((int) ($data['quantity'] ?? 1), 1);
            $unitPrice = (float) ($data['type'] === 'send' ? $product->purchase_price : $product->sale_price);

            $items->push([
                'product' => $product,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total' => $unitPrice * $quantity,
            ]);
        }

        return $items;
    }

    private function createProductLines(Transaction $transaction, $productItems, string $type): void
    {
        foreach ($productItems as $item) {
            $transaction->products()->create([
                'product_id' => $item['product']->id,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['total'],
            ]);

            if ($type === 'send') {
                $item['product']->increment('stock', $item['quantity']);
            } elseif ($type === 'receive') {
                $item['product']->decrement('stock', $item['quantity']);
            }
        }
    }

    private function productItemsForLog($productItems): array
    {
        return $productItems
            ->map(fn (array $item) => [
                'id' => $item['product']->id,
                'name' => $item['product']->name,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['total'],
            ])
            ->all();
    }

    private function reverseProductLineEffects($productItems, string $type): void
    {
        foreach ($productItems as $item) {
            if (! $item->product) {
                continue;
            }

            if ($type === 'send') {
                $item->product->decrement('stock', $item->quantity);
            } elseif ($type === 'receive') {
                $item->product->increment('stock', $item->quantity);
            }
        }
    }

    public function update(int $id, array $data, Request $request): Transaction
    {
        $transaction = Transaction::with('products.product')->findOrFail($id);
        $oldData = $this->buildOldData($transaction);

        $resolvedData = array_merge([
            'client_id' => $transaction->client_id,
            'product_id' => $transaction->product_id,
            'payment_way_id' => $transaction->payment_way_id,
            'type' => $transaction->type,
            'amount' => $transaction->amount,
            'commission' => $transaction->commission ?? 0,
            'notes' => $transaction->notes,
            'quantity' => $transaction->quantity ?? 1,
        ], $data);

        $newProductItems = $this->resolveProductItems($resolvedData);
        $firstProductItem = $newProductItems->first();
        $resolvedData['product_id'] = $firstProductItem['product']->id ?? null;
        $resolvedData['quantity'] = $firstProductItem['quantity'] ?? null;
        unset($resolvedData['products']);

        if ($request->hasFile('attachment')) {
            $this->fileService->deletePublicFile($transaction->attachment);
            $resolvedData['attachment'] = $this->fileService->storePublicFile($request->file('attachment'), 'uploads/transactions');
        }

        $oldClient = $transaction->client_id ? Client::findOrFail($transaction->client_id) : null;
        $oldProduct = $transaction->product_id ? Product::findOrFail($transaction->product_id) : null;
        $newClient = !empty($resolvedData['client_id']) ? Client::findOrFail($resolvedData['client_id']) : null;
        $newProduct = !empty($resolvedData['product_id']) ? Product::findOrFail($resolvedData['product_id']) : null;
        $oldProductItems = $transaction->products;

        $oldPaymentWay = PaymentWay::findOrFail($transaction->payment_way_id);
        $newPaymentWay = PaymentWay::findOrFail($resolvedData['payment_way_id']);
        $oldTotal = $transaction->amount + ($transaction->commission ?? 0);
        $newTotal = $resolvedData['amount'] + ($resolvedData['commission'] ?? 0);
        $newQuantity = (int) ($resolvedData['quantity'] ?? 1);
        $oldQuantity = $transaction->quantity ?? 1;

        return DB::transaction(function () use ($transaction, $resolvedData, $oldData, $oldClient, $oldProduct, $oldProductItems, $newClient, $newProduct, $newProductItems, $newPaymentWay, $oldPaymentWay, $oldTotal, $newTotal, $newQuantity, $oldQuantity) {
            if ($oldProductItems->isNotEmpty()) {
                $this->reverseProductLineEffects($oldProductItems, $transaction->type);
            }

            $this->reverseTransactionEffects(
                type: $transaction->type,
                amount: (float) $transaction->amount,
                total: $oldTotal,
                paymentWay: $oldPaymentWay,
                sourceTransaction: $transaction,
                client: $oldProductItems->isNotEmpty() ? null : $oldClient,
                product: $oldProductItems->isNotEmpty() ? null : $oldProduct,
                quantity: $oldQuantity
            );

            $balanceBeforeTransaction = $newPaymentWay->fresh()->balance;
            $transaction->update($resolvedData);
            $transaction->products()->delete();
            $this->createProductLines($transaction, $newProductItems, $resolvedData['type']);

            $this->applyTransactionEffects(
                type: $resolvedData['type'],
                amount: (float) $resolvedData['amount'],
                total: $newTotal,
                paymentWay: $newPaymentWay,
                sourceTransaction: $transaction,
                client: $newProductItems->isNotEmpty() ? null : $newClient,
                product: $newProductItems->isNotEmpty() ? null : $newProduct,
                quantity: $newQuantity
            );

            $transaction->balance_before_transaction = $balanceBeforeTransaction;
            if ($resolvedData['type'] === 'send') {
                $transaction->balance_after_transaction = $balanceBeforeTransaction - $newTotal;
            } elseif ($resolvedData['type'] === 'receive') {
                $transaction->balance_after_transaction = $balanceBeforeTransaction + $newTotal;
            }
            $transaction->save();

            $transaction->logs()->create([
                'created_by' => Auth::id(),
                'action' => 'update',
                'data' => [
                    'old_data' => $oldData,
                    'new_data' => [
                        'id' => $transaction->id,
                        'type' => $transaction->type,
                        'amount' => $transaction->amount,
                        'commission' => $transaction->commission,
                        'notes' => $transaction->notes,
                        'attachment' => $transaction->attachment,
                        'client_id' => $transaction->client_id,
                        'product_id' => $transaction->product_id,
                        'quantity' => $transaction->quantity,
                        'payment_way_id' => $transaction->payment_way_id,
                    ],
                    'client' => [
                        'id' => optional($newClient)->id,
                        'name' => optional($newClient)->name,
                    ],
                    'products' => $this->productItemsForLog($newProductItems),
                    'payment_way' => [
                        'id' => $newPaymentWay->id,
                        'name' => $newPaymentWay->name,
                        'category' => optional($newPaymentWay->category)->name,
                        'sub_category' => optional($newPaymentWay->subCategory)->name,
                    ],
                    'history' => [
                        'moved_between_payment_ways' => (int) $oldPaymentWay->id !== (int) $newPaymentWay->id,
                        'old_payment_way' => [
                            'id' => $oldPaymentWay->id,
                            'name' => $oldPaymentWay->name,
                        ],
                        'new_payment_way' => [
                            'id' => $newPaymentWay->id,
                            'name' => $newPaymentWay->name,
                        ],
                    ],
                    'updated_by' => Auth::user()->name,
                ],
            ]);

            return $transaction->load(['paymentWay', 'client', 'creator', 'logs', 'products.product']);
        });
    }

    private function reverseTransactionEffects(
        string $type,
        float $amount,
        float|int $total,
        PaymentWay $paymentWay,
        Transaction $sourceTransaction,
        ?Client $client,
        ?Product $product,
        int $quantity
    ): void {
        if ($type === 'send') {
            if ($product) {
                $product->decrement('stock', $quantity);
            }
            if ($client && !$product) {
                $client->source_model = $sourceTransaction;
                $client->log_description = __('messages.transaction_reversal');
                $client->decrement('debt', $amount);
            }
            $paymentWay->increment('balance', $total);

            if ($paymentWay->type === 'wallet') {
                $monthlyLimit = $this->findCurrentMonthlyLimit($paymentWay);
                if ($monthlyLimit) {
                    $monthlyLimit->decrement('send_used', $amount);
                }
            }
        } elseif ($type === 'receive') {
            if ($product) {
                $product->increment('stock', $quantity);
            }
            if ($client && !$product) {
                $client->source_model = $sourceTransaction;
                $client->log_description = __('messages.transaction_reversal');
                $client->increment('debt', $amount);
            }
            $paymentWay->decrement('balance', $total);

            if ($paymentWay->type === 'wallet') {
                $monthlyLimit = $this->findCurrentMonthlyLimit($paymentWay);
                if ($monthlyLimit) {
                    $monthlyLimit->decrement('receive_used', $total);
                }
            }
        }
    }

    private function applyTransactionEffects(
        string $type,
        float $amount,
        float|int $total,
        PaymentWay $paymentWay,
        Transaction $sourceTransaction,
        ?Client $client,
        ?Product $product,
        int $quantity
    ): void {
        if ($type === 'send') {
            if ($product) {
                $product->increment('stock', $quantity);
            }
            if ($client && !$product) {
                $client->source_model = $sourceTransaction;
                $client->log_description = __('messages.transaction_updated_successfully');
                $client->increment('debt', $amount);
            }
            $paymentWay->decrement('balance', $total);

            if ($paymentWay->type === 'wallet') {
                $monthlyLimit = $this->getOrCreateCurrentMonthlyLimit($paymentWay);
                if ($monthlyLimit) {
                    $monthlyLimit->increment('send_used', $amount);
                }
            }
        } elseif ($type === 'receive') {
            if ($product) {
                $product->decrement('stock', $quantity);
            }
            if ($client && !$product) {
                $client->source_model = $sourceTransaction;
                $client->log_description = __('messages.transaction_updated_successfully');
                $client->decrement('debt', $amount);
            }
            $paymentWay->increment('balance', $total);

            if ($paymentWay->type === 'wallet') {
                $monthlyLimit = $this->getOrCreateCurrentMonthlyLimit($paymentWay);
                if ($monthlyLimit) {
                    $monthlyLimit->increment('receive_used', $total);
                }
            }
        }
    }

    private function buildOldData(Transaction $transaction): array
    {
        return [
            'id' => $transaction->id,
            'type' => $transaction->type,
            'amount' => $transaction->amount,
            'commission' => $transaction->commission,
            'notes' => $transaction->notes,
            'attachment' => $transaction->attachment,
            'client_id' => $transaction->client_id,
            'product_id' => $transaction->product_id,
            'quantity' => $transaction->quantity,
            'payment_way_id' => $transaction->payment_way_id,
            'balance_before_transaction' => $transaction->balance_before_transaction,
            'balance_after_transaction' => $transaction->balance_after_transaction,
        ];
    }
}
