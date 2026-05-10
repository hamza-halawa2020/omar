<?php

namespace App\Services;

use App\Models\Client;
use App\Models\PaymentWay;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\Concerns\HandlesWalletMonthlyLimits;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    use HandlesWalletMonthlyLimits;

    public function __construct(private readonly FileService $fileService)
    {
    }

    public function paginatedForIndex(Request $request): array
    {
        $fromDate = $request->get('from_date', now()->isoFormat('YYYY-MM-DD'));
        $toDate = $request->get('to_date', now()->isoFormat('YYYY-MM-DD'));

        $transactions = Transaction::with(['paymentWay', 'client', 'creator', 'logs'])
            ->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate)
            ->latest()
            ->paginate(50);

        return compact('transactions', 'fromDate', 'toDate');
    }

    public function list(): Collection
    {
        return Transaction::with(['paymentWay', 'client', 'creator', 'logs'])->latest()->get();
    }

    public function store(array $data, Request $request): Transaction
    {
        $data['created_by'] = Auth::id();
        $data['attachment'] = $request->hasFile('attachment')
            ? $this->fileService->storePublicFile($request->file('attachment'), 'uploads/transactions')
            : null;
        $quantity = $data['quantity'] ?? 1;

        $client = ! empty($data['client_id']) ? Client::findOrFail($data['client_id']) : null;
        $product = ! empty($data['product_id']) ? Product::findOrFail($data['product_id']) : null;

        $paymentWay = PaymentWay::findOrFail($data['payment_way_id']);
        $total = $data['amount'] + ($data['commission'] ?? 0);

        if ($data['type'] === 'send' && $total > $paymentWay->balance) {
            throw new HttpResponseException(response()->json(['status' => false, 'message' => __('messages.not_enough_balance')], 400));
        }

        $monthlyLimit = $this->getOrCreateCurrentMonthlyLimit($paymentWay);

        if ($monthlyLimit) {
            if ($data['type'] === 'send' && ($monthlyLimit->send_used + $data['amount']) > $monthlyLimit->send_limit) {
                throw new HttpResponseException(response()->json(['status' => false, 'message' => __('messages.send_limit_exceeded')], 400));
            }

            if ($data['type'] === 'receive' && ($monthlyLimit->receive_used + $total) > $monthlyLimit->receive_limit) {
                throw new HttpResponseException(response()->json(['status' => false, 'message' => __('messages.receive_limit_exceeded')], 400));
            }
        }

        return DB::transaction(function () use ($data, $client, $product, $quantity, $paymentWay, $total, $monthlyLimit) {
            $transaction = Transaction::create(array_filter($data, fn ($value) => $value !== null));

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
                if ($product) {
                    $product->increment('stock', $quantity);
                }

                if ($client && ! $product) {
                    $client->source_model = $transaction;
                    $client->log_description = __('messages.transaction_created_successfully');
                    $client->increment('debt', $data['amount']);
                }

                $paymentWay->decrement('balance', $total);

                if ($monthlyLimit) {
                    $monthlyLimit->increment('send_used', $data['amount']);
                }
            } elseif ($data['type'] === 'receive') {
                if ($product) {
                    $product->decrement('stock', $quantity);
                }

                if ($client && ! $product) {
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
                    'product' => [
                        'id' => optional($product)->id,
                        'name' => optional($product)->name,
                        'debt' => optional($product)->debt,
                    ],
                    'payment_way' => [
                        'id' => $paymentWay->id,
                        'name' => $paymentWay->name,
                        'category' => optional($paymentWay->category)->name,
                        'sub_category' => optional($paymentWay->subCategory)->name,
                        'creator' => optional($paymentWay->creator)->name,
                    ],
                ],
            ]);

            return $transaction->load(['paymentWay', 'client', 'creator']);
        });
    }

    public function show(int $id): Transaction
    {
        return Transaction::with(['paymentWay', 'client', 'product', 'creator', 'logs'])->findOrFail($id);
    }

    public function update(int $id, array $data, Request $request): Transaction
    {
        $transaction = Transaction::findOrFail($id);
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

        if ($request->hasFile('attachment')) {
            $this->fileService->deletePublicFile($transaction->attachment);
            $resolvedData['attachment'] = $this->fileService->storePublicFile($request->file('attachment'), 'uploads/transactions');
        }

        $oldClient = $transaction->client_id ? Client::findOrFail($transaction->client_id) : null;
        $oldProduct = $transaction->product_id ? Product::findOrFail($transaction->product_id) : null;
        $newClient = ! empty($resolvedData['client_id']) ? Client::findOrFail($resolvedData['client_id']) : null;
        $newProduct = ! empty($resolvedData['product_id']) ? Product::findOrFail($resolvedData['product_id']) : null;

        $oldPaymentWay = PaymentWay::findOrFail($transaction->payment_way_id);
        $newPaymentWay = PaymentWay::findOrFail($resolvedData['payment_way_id']);
        $oldTotal = $transaction->amount + ($transaction->commission ?? 0);
        $newTotal = $resolvedData['amount'] + ($resolvedData['commission'] ?? 0);
        $newQuantity = (int) ($resolvedData['quantity'] ?? 1);
        $oldQuantity = $transaction->quantity ?? 1;

        return DB::transaction(function () use (
            $transaction,
            $resolvedData,
            $oldData,
            $oldClient,
            $oldProduct,
            $newClient,
            $newProduct,
            $newPaymentWay,
            $oldPaymentWay,
            $oldTotal,
            $newTotal,
            $newQuantity,
            $oldQuantity
        ) {
            $this->reverseTransactionEffects(
                type: $transaction->type,
                amount: (float) $transaction->amount,
                total: $oldTotal,
                paymentWay: $oldPaymentWay,
                sourceTransaction: $transaction,
                client: $oldClient,
                product: $oldProduct,
                quantity: $oldQuantity
            );

            $balanceBeforeTransaction = $newPaymentWay->fresh()->balance;
            $transaction->update($resolvedData);

            $this->applyTransactionEffects(
                type: $resolvedData['type'],
                amount: (float) $resolvedData['amount'],
                total: $newTotal,
                paymentWay: $newPaymentWay,
                sourceTransaction: $transaction,
                client: $newClient,
                product: $newProduct,
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
                    'product' => [
                        'id' => optional($newProduct)->id,
                        'name' => optional($newProduct)->name,
                    ],
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

            return $transaction->load(['paymentWay', 'client', 'creator', 'logs']);
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
    ): void
    {
        if ($type === 'send') {
            if ($product) {
                $product->decrement('stock', $quantity);
            }
            if ($client && ! $product) {
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
            if ($client && ! $product) {
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
    ): void
    {
        if ($type === 'send') {
            if ($product) {
                $product->increment('stock', $quantity);
            }
            if ($client && ! $product) {
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
            if ($client && ! $product) {
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
