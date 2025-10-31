<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\InstallmentContract\PayRequest;
use App\Http\Requests\InstallmentContract\StoreInstallmentContractRequest;
use App\Http\Requests\InstallmentContract\UpdateInstallmentContractRequest;
use App\Http\Resources\InstallmentContractResource;
use App\Models\Client;
use App\Models\Installment;
use App\Models\InstallmentContract;
use App\Models\PaymentWay;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InstallmentContractController extends BaseController
{
    public function __construct()
    {
        $this->middleware('check.permission:installments_index')->only('index', 'list');
        $this->middleware('check.permission:installments_store')->only('store');
        $this->middleware('check.permission:installments_show')->only('show', 'showPage');
        $this->middleware('check.permission:installments_update')->only('update');
        $this->middleware('check.permission:installments_pay')->only('pay');
        $this->middleware('check.permission:installments_destroy')->only('destroy');
    }

    public function index()
    {
        $clients = Client::all();
        $products = Product::all();

        return view('dashboard.installment_contracts.index', compact('clients', 'products'));
    }

    public function list()
    {
        $installments = InstallmentContract::with(['client', 'product', 'creator', 'installments'])->latest()->get();

        return response()->json(['status' => true, 'message' => __('messages.installments_fetched_successfully'), 'data' => InstallmentContractResource::collection($installments)]);
    }

    public function store(StoreInstallmentContractRequest $request)
    {
        $data = $request->validated();

        $downPayment = $data['down_payment'] ?? 0;
        $productPrice = $data['product_price'];
        $remainingAmount = $productPrice - $downPayment;

        $interestRate = $data['interest_rate'] ?? 0;
        $interestAmount = ($remainingAmount * $interestRate) / 100;

        $totalAmount = $remainingAmount + $interestAmount;

        $installmentCount = $data['installment_count'];
        $installmentAmount = $totalAmount / $installmentCount;

        $contract = InstallmentContract::create([
            'product_price' => $productPrice,
            'down_payment' => $downPayment,
            'remaining_amount' => $remainingAmount,
            'installment_count' => $installmentCount,
            'interest_rate' => $interestRate,
            'interest_amount' => $interestAmount,
            'total_amount' => $totalAmount,
            'installment_amount' => $installmentAmount,
            'start_date' => $data['start_date'],
            'client_id' => $data['client_id'],
            'product_id' => $data['product_id'],
            'created_by' => Auth::id(),
        ]);

        $client = Client::find($data['client_id']);
        $client->increment('debt', $totalAmount);

        $startDate = Carbon::parse($data['start_date']);
        for ($i = 1; $i <= $installmentCount; $i++) {
            Installment::create([
                'due_date' => $startDate->copy()->addMonths($i),
                'required_amount' => $installmentAmount,
                'paid_amount' => 0,
                'status' => 'pending',
                'installment_contract_id' => $contract->id,
            ]);
        }

        $product = Product::find($data['product_id']);

        $product->decrement('stock', 1);

        return response()->json(['status' => true,'message' => __('messages.Installment_contract_created_successfully'),'data' => $contract->load('installments'),], 201);
    }

    public function show($id)
    {
        $contract = InstallmentContract::with(['client', 'product', 'creator', 'installments.payments.paid_by'])->findOrFail($id);

        return response()->json(['status' => true,'message' => __('messages.installment_contract_fetched_successfully'),'data' => new InstallmentContractResource($contract),]);
    }

    public function showPage($id)
    {
        $contract = InstallmentContract::with(['client', 'product', 'creator', 'installments.payments'])->findOrFail($id);

        $paymentWays = PaymentWay::all();

        return view('dashboard.installment_contracts.show', compact('contract', 'paymentWays'));
    }

    public function update(UpdateInstallmentContractRequest $request, $id)
    {
        $contract = InstallmentContract::with('installments')->findOrFail($id);
        $data = $request->validated();

        $hasPaidInstallments = $contract->installments()->where('status', 'paid')->exists();

        $recalculate = isset($data['product_price']) || isset($data['down_payment']) || isset($data['interest_rate']) || isset($data['installment_count']) || isset($data['start_date']);

        if ($recalculate) {
            if ($hasPaidInstallments) {
                return response()->json(['status' => false, 'message' => __('messages.cannot_update_contract_with_paid_installments')], 400);
            }

            $productPrice = $data['product_price'];
            $downPayment = $data['down_payment'] ?? 0;
            $remainingAmount = $productPrice - $downPayment;

            $interestRate = $data['interest_rate'] ?? 0;
            $interestAmount = ($remainingAmount * $interestRate) / 100;

            $installmentCount = $data['installment_count'];
            $totalAmount = $remainingAmount + $interestAmount;
            $installmentAmount = $totalAmount / $installmentCount;

            $startDate = Carbon::parse($data['start_date']);

            $oldTotal = $contract->total_amount;

            $contract->update([
                'product_price' => $productPrice,
                'down_payment' => $downPayment,
                'remaining_amount' => $remainingAmount,
                'installment_count' => $installmentCount,
                'interest_rate' => $interestRate,
                'interest_amount' => $interestAmount,
                'total_amount' => $totalAmount,
                'installment_amount' => $installmentAmount,
                'start_date' => $startDate,
                'client_id' => $data['client_id'],
                'product_id' => $data['product_id'],
            ]);

            $contract->installments()->delete();

            for ($i = 1; $i <= $installmentCount; $i++) {
                Installment::create([
                    'due_date' => $startDate->copy()->addMonths($i),
                    'required_amount' => $installmentAmount,
                    'paid_amount' => 0,
                    'status' => 'pending',
                    'installment_contract_id' => $contract->id,
                ]);
            }

            $client = Client::find($data['client_id']);
            $client->decrement('debt', $oldTotal);
            $client->increment('debt', $totalAmount);

            return response()->json(['status' => true, 'message' => __('messages.installment_contract_updated_successfully'), 'data' => new InstallmentContractResource($contract->load('installments'))]);
        } else {
            $contract->update($data);

            return response()->json(['status' => true, 'message' => __('messages.installment_contract_updated_successfully'), 'data' => new InstallmentContractResource($contract->load('installments')),
            ]);
        }
    }

    public function pay(PayRequest $request)
    {
        $data = $request->validated();

        $installment = Installment::with('contract.client', 'contract.product')->findOrFail($data['installment_id']);
        $client = $installment->contract->client;
        $product = $installment->contract->product;
        $paymentWay = PaymentWay::findOrFail($data['payment_way_id']);
        $total = $data['amount'] + ($data['commission'] ?? 0);
        $type = 'receive' ;


        $monthlyLimit = null;
        if ($paymentWay->type === 'wallet') {
            $currentMonth = now()->month;
            $currentYear = now()->year;

            $monthlyLimit = $paymentWay->monthlyLimits()->where('month', $currentMonth)->where('year', $currentYear)->first();

            if (($monthlyLimit->receive_used + $total) > $monthlyLimit->receive_limit) {
                return response()->json(['status' => false, 'message' => __('messages.receive_limit_exceeded')], 400);
            }
        }

        return DB::transaction(function () use ($data, $installment, $client, $product, $paymentWay, $total, $monthlyLimit) {

            $transaction = Transaction::create([
                'payment_way_id' => $paymentWay->id,
                'created_by' => Auth::id(),
                'type' => 'receive',
                'amount' => $data['amount'],
                'commission' => $data['commission'] ?? 0,
                'notes' => __('messages.payment_for_installment').' '.($client->name ?? '').' - '.($product->name ?? ''),
                'client_id' => $client->id ?? null,
                'balance_before_transaction' => $paymentWay->balance,
                'balance_after_transaction' => $paymentWay->balance + $total,
            ]);

            $paymentWay->increment('balance', $total);

            if ($monthlyLimit) {
                $monthlyLimit->increment('receive_used', $total);
            }

            $payment = $installment->payments()->create([
                'transaction_id' => $transaction->id,
                'amount' => $data['amount'],
                'payment_date' => $data['payment_date'],
                'paid_by' => Auth::id(),
            ]);

            $installment->increment('paid_amount', $data['amount']);
            $installment->status = $installment->paid_amount >= $installment->required_amount ? 'paid' : 'pending';
            $installment->save();

            if ($client) {
                $client->decrement('debt', $data['amount']);
            }

            $transaction->logs()->create([
                'created_by' => Auth::id(),
                'action' => 'create',
                'data' => [
                    'installment' => [
                        'id' => $installment->id,
                        'amount' => $installment->required_amount,
                        'paid' => $installment->paid_amount,
                        'status' => $installment->status,
                    ],
                    'client' => [
                        'id' => $client->id ?? null,
                        'name' => $client->name ?? null,
                    ],
                    'payment_way' => [
                        'id' => $paymentWay->id,
                        'name' => $paymentWay->name,
                        'balance_before' => $transaction->balance_before_transaction,
                        'balance_after' => $transaction->balance_after_transaction,
                    ],
                ],
            ]);

            return response()->json(['status' => true,'message' => __('messages.installment_paid_successfully'),'data' => ['installment' => $installment->load('payments'),'transaction' => $transaction]]);
        });
    }

    public function destroy($id)
    {
        $contract = InstallmentContract::with('installments')->findOrFail($id);

        if ($contract->installments()->where('status', 'paid')->exists()) {
            return response()->json([
                'status' => false,
                'message' => __('messages.cannot_delete_contract_with_paid_installments'),
            ], 400);
        }

        $contract->installments()->delete();
        $contract->delete();

        return response()->json([
            'status' => true,
            'message' => __('messages.installment_contract_deleted_successfully'),
        ]);
    }
}
