<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\PaymentWay;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{

    //     public function index()
    // {
    //     return view('dashboard.transactions.index');
    // }

    public function list()
    {
        $transactions = Transaction::with(['paymentWay', 'creator', 'logs'])->latest()->get();

        return response()->json(['status'  => true, 'message' => __('messages.transactions_fetched_successfully'), 'data' => TransactionResource::collection($transactions)]);
    }


    public function store(StoreTransactionRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/transactions'), $filename);
            $data['attachment'] = 'uploads/transactions/' . $filename;
        }


        $total = $data['amount'] +  $data['commission'];


        $paymentWay = PaymentWay::findOrFail($data['payment_way_id']);
        $total = $data['amount'] +  $data['commission'];

        if ($data['type'] === 'send' && $total > $paymentWay->balance) {
            return response()->json(['status' => false, 'message' => __('messages.not_enough_balance')], 400);
        }


        $transaction = Transaction::create($data);

        $paymentWay = $transaction->paymentWay;


        if ($data['type'] === 'receive') {
            $paymentWay->update([
                'balance' => $paymentWay->balance + $total,
                'receive_limit_alert' => $paymentWay->receive_limit_alert + $total,
            ]);
        } elseif ($data['type'] === 'send') {
            $paymentWay->update([
                'balance' => $paymentWay->balance - $total,
                'send_limit_alert' => $paymentWay->send_limit_alert +  $data['amount'],

            ]);
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
                'payment_way' => [
                    'id' => $paymentWay->id,
                    'name' => $paymentWay->name,
                    'category' => optional($paymentWay->category)->name,
                    'sub_category' => optional($paymentWay->subCategory)->name,
                    'creator' => optional($paymentWay->creator)->name,
                ],
            ],
        ]);


        return response()->json([
            'status' => true,
            'message' => __('messages.transaction_created_successfully'),
            'data' => new TransactionResource($transaction->load(['paymentWay', 'creator']))
        ], 201);
    }

    public function show($id)
    {
        $transaction = Transaction::with(['paymentWay', 'creator', 'logs'])->findOrFail($id);

        return response()->json(['status'  => true, 'message' => __('messages.transaction_fetched_successfully'), 'data' => new TransactionResource($transaction)]);
    }
}
