<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Client;
use App\Models\PaymentWay;
use App\Models\Transaction;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends BaseController
{
    public function __construct()
    {
        $this->middleware('check.permission:transactions_index')->only('list');
        $this->middleware('check.permission:transactions_store')->only('store');
        $this->middleware('check.permission:transactions_show')->only('show');
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

        return DB::transaction(function () use ($data, $client, $paymentWay, $total, $monthlyLimit) {
            $transaction = Transaction::create($data);

            if ($data['type'] === 'send') {
                if ($client) {
                    $client->increment('debt', $data['amount']);
                }

                $paymentWay->decrement('balance', $total);

                if ($monthlyLimit) {
                    $monthlyLimit->increment('send_used', $data['amount']);
                }
            } elseif ($data['type'] === 'receive') {
                if ($client) {
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
                        'debt' => optional($client)->debt,
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
        $transaction = Transaction::with(['paymentWay', 'client', 'creator', 'logs'])->findOrFail($id);

        return response()->json(['status' => true, 'message' => __('messages.transaction_fetched_successfully'), 'data' => new TransactionResource($transaction)]);
    }
}
