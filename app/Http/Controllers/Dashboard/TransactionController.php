<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Requests\Transaction\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Client;
use App\Models\PaymentWay;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends BaseController
{
    public function __construct()
    {
        $this->middleware('check.permission:transactions_index')->only(['list', 'index']);
        $this->middleware('check.permission:transactions_store')->only('store');
        $this->middleware('check.permission:transactions_show')->only('show');
        $this->middleware('check.permission:transactions_update')->only('update');
    }

    public function index()
    {
        $fromDate = request('from_date', now()->isoFormat('YYYY-MM-DD'));
        $toDate = request('to_date', now()->isoFormat('YYYY-MM-DD'));

        $transactions = Transaction::with(['paymentWay', 'client', 'creator', 'logs'])
            ->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate)
            ->latest()
            ->paginate(50);

        return view('dashboard.transactions.index', compact('transactions', 'fromDate', 'toDate'));
    }


    public function list()
    {
        $transactions = Transaction::with(['paymentWay', 'client', 'creator', 'logs'])->latest()->get();

        return response()->json(['status' => true, 'message' => __('messages.transactions_fetched_successfully'), 'data' => TransactionResource::collection($transactions)]);
    }

    public function store(StoreTransactionRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();
        $quantity = $data['quantity'] ?? 1;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/transactions'), $filename);
            $data['attachment'] = 'uploads/transactions/'.$filename;
        }

        $client = null;
        if (! empty($data['client_id'])) {
            $client = Client::findOrFail($data['client_id']);
        }
        $product = null;
        if (! empty($data['product_id'])) {
            $product = Product::findOrFail($data['product_id']);
        }

        $paymentWay = PaymentWay::findOrFail($data['payment_way_id']);
        $total = $data['amount'] + ($data['commission'] ?? 0);

        if ($data['type'] === 'send' && $total > $paymentWay->balance) {
            return response()->json(['status' => false, 'message' => __('messages.not_enough_balance')], 400);
        }

        $monthlyLimit = null;

        if ($paymentWay->type === 'wallet') {
            $currentMonth = now()->month;
            $currentYear = now()->year;

            $monthlyLimit = $paymentWay->monthlyLimits()
                ->where('month', $currentMonth)
                ->where('year', $currentYear)
                ->first();

            if (! $monthlyLimit) {
                $monthlyLimit = $paymentWay->monthlyLimits()->create([
                    'month' => $currentMonth,
                    'year' => $currentYear,
                    'send_limit' => $paymentWay->send_limit,
                    'receive_limit' => $paymentWay->receive_limit,
                    'send_used' => 0,
                    'receive_used' => 0,
                ]);
            }

            if ($data['type'] === 'send' && ($monthlyLimit->send_used + $data['amount']) > $monthlyLimit->send_limit) {
                return response()->json(['status' => false, 'message' => __('messages.send_limit_exceeded')], 400);
            } elseif ($data['type'] === 'receive' && ($monthlyLimit->receive_used + $total) > $monthlyLimit->receive_limit) {
                return response()->json(['status' => false, 'message' => __('messages.receive_limit_exceeded')], 400);
            }
        }

        return DB::transaction(function () use ($data, $client, $product, $quantity, $paymentWay, $total, $monthlyLimit) {
            $transaction = Transaction::create($data);

            // Store balance before and after the transaction
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

            return response()->json(['status' => true, 'message' => __('messages.transaction_created_successfully'), 'data' => new TransactionResource($transaction->load(['paymentWay', 'client', 'creator']))], 201);
        });
    }

    public function show($id)
    {
        $transaction = Transaction::with(['paymentWay', 'client', 'product', 'creator', 'logs'])->findOrFail($id);

        return response()->json(['status' => true, 'message' => __('messages.transaction_fetched_successfully'), 'data' => new TransactionResource($transaction)]);
    }

    public function update(UpdateTransactionRequest $request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        $data = $request->validated();
        // Store old values for logging
        $oldData = [
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

        // Handle file upload
        if ($request->hasFile('attachment')) {
            // Delete old file if exists
            if ($transaction->attachment && file_exists(public_path($transaction->attachment))) {
                unlink(public_path($transaction->attachment));
            }
            
            $file = $request->file('attachment');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/transactions'), $filename);
            $data['attachment'] = 'uploads/transactions/'.$filename;
        }

        $client = null;
        if (!empty($data['client_id'])) {
            $client = Client::findOrFail($data['client_id']);
        }
        
        $product = null;
        if (!empty($data['product_id'])) {
            $product = Product::findOrFail($data['product_id']);
        }
        $paymentWay = null;
        if (isset($data['payment_way_id'])) {
            $paymentWay = PaymentWay::findOrFail($data['payment_way_id']);
        }else{
            $paymentWay = PaymentWay::findOrFail($transaction->payment_way_id);
        }
        $oldPaymentWay = PaymentWay::findOrFail($transaction->payment_way_id);
        
        $oldTotal = $transaction->amount + ($transaction->commission ?? 0);
        $newTotal = $data['amount'] + ($data['commission'] ?? 0);
        $quantity = $data['quantity'] ?? 1;
        $oldQuantity = $transaction->quantity ?? 1;

        return DB::transaction(function () use ($transaction, $data, $oldData, $client, $product, $paymentWay, $oldPaymentWay, $oldTotal, $newTotal, $quantity, $oldQuantity) {
            
            $this->reverseTransactionEffects($transaction, $oldPaymentWay, $oldTotal, $oldQuantity);
            
            $balanceBeforeTransaction = $paymentWay->fresh()->balance;
            $transaction->update($data);
            
            $this->applyTransactionEffects($transaction, $paymentWay, $client, $product, $newTotal, $quantity);
            
            $transaction->balance_before_transaction = $balanceBeforeTransaction;
            if ($data['type'] === 'send') {
                $transaction->balance_after_transaction = $balanceBeforeTransaction - $newTotal;
            } elseif ($data['type'] === 'receive') {
                $transaction->balance_after_transaction = $balanceBeforeTransaction + $newTotal;
            }
            $transaction->save();

            // Create log entry
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
                        'id' => optional($client)->id,
                        'name' => optional($client)->name,
                    ],
                    'product' => [
                        'id' => optional($product)->id,
                        'name' => optional($product)->name,
                    ],
                    'payment_way' => [
                        'id' => $paymentWay->id,
                        'name' => $paymentWay->name,
                        'category' => optional($paymentWay->category)->name,
                        'sub_category' => optional($paymentWay->subCategory)->name,
                    ],
                    'updated_by' => Auth::user()->name,
                ],
            ]);

            return response()->json([
                'status' => true, 
                'message' => __('messages.transaction_updated_successfully'), 
                'data' => new TransactionResource($transaction->load(['paymentWay', 'client', 'creator', 'logs']))
            ]);
        });
    }

    private function reverseTransactionEffects($transaction, $paymentWay, $total, $quantity)
    {
        $client = $transaction->client;
        $product = $transaction->product;

        if ($transaction->type === 'send') {
            // Reverse send effects
            if ($product) {
                $product->decrement('stock', $quantity);
            }
            if ($client && !$product) {
                $client->decrement('debt', $transaction->amount);
            }
            $paymentWay->increment('balance', $total);

            // Reverse monthly limits for wallets
            if ($paymentWay->type === 'wallet') {
                $monthlyLimit = $paymentWay->monthlyLimits()
                    ->where('month', now()->month)
                    ->where('year', now()->year)
                    ->first();
                if ($monthlyLimit) {
                    $monthlyLimit->decrement('send_used', $transaction->amount);
                }
            }
        } elseif ($transaction->type === 'receive') {
            // Reverse receive effects
            if ($product) {
                $product->increment('stock', $quantity);
            }
            if ($client && !$product) {
                $client->increment('debt', $transaction->amount);
            }
            $paymentWay->decrement('balance', $total);

            // Reverse monthly limits for wallets
            if ($paymentWay->type === 'wallet') {
                $monthlyLimit = $paymentWay->monthlyLimits()
                    ->where('month', now()->month)
                    ->where('year', now()->year)
                    ->first();
                if ($monthlyLimit) {
                    $monthlyLimit->decrement('receive_used', $total);
                }
            }
        }
    }

    private function applyTransactionEffects($transaction, $paymentWay, $client, $product, $total, $quantity)
    {
        if ($transaction->type === 'send') {
            if ($product) {
                $product->increment('stock', $quantity);
            }
            if ($client && !$product) {
                $client->increment('debt', $transaction->amount);
            }
            $paymentWay->decrement('balance', $total);

            // Update monthly limits for wallets
            if ($paymentWay->type === 'wallet') {
                $monthlyLimit = $paymentWay->monthlyLimits()
                    ->where('month', now()->month)
                    ->where('year', now()->year)
                    ->first();
                if (!$monthlyLimit) {
                    $monthlyLimit = $paymentWay->monthlyLimits()->create([
                        'month' => now()->month,
                        'year' => now()->year,
                        'send_limit' => $paymentWay->send_limit,
                        'receive_limit' => $paymentWay->receive_limit,
                        'send_used' => 0,
                        'receive_used' => 0,
                    ]);
                }
                $monthlyLimit->increment('send_used', $transaction->amount);
            }
        } elseif ($transaction->type === 'receive') {
            if ($product) {
                $product->decrement('stock', $quantity);
            }
            if ($client && !$product) {
                $client->decrement('debt', $transaction->amount);
            }
            $paymentWay->increment('balance', $total);

            // Update monthly limits for wallets
            if ($paymentWay->type === 'wallet') {
                $monthlyLimit = $paymentWay->monthlyLimits()
                    ->where('month', now()->month)
                    ->where('year', now()->year)
                    ->first();
                if (!$monthlyLimit) {
                    $monthlyLimit = $paymentWay->monthlyLimits()->create([
                        'month' => now()->month,
                        'year' => now()->year,
                        'send_limit' => $paymentWay->send_limit,
                        'receive_limit' => $paymentWay->receive_limit,
                        'send_used' => 0,
                        'receive_used' => 0,
                    ]);
                }
                $monthlyLimit->increment('receive_used', $total);
            }
        }
    }
}
