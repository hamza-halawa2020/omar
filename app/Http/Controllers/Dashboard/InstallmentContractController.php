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

        return response()->json([
            'status' => true,
            'message' => __('messages.Installment_contract_created_successfully'),
            'data' => $contract->load('installments'),
        ], 201);
    }

    public function show($id)
    {
        $contract = InstallmentContract::with(['client', 'product', 'creator', 'installments.payments.paid_by'])
            ->findOrFail($id);

        return response()->json([
            'status' => true,
            'message' => __('messages.installment_contract_fetched_successfully'),
            'data' => new InstallmentContractResource($contract),
        ]);
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

        $installment = Installment::findOrFail($data['installment_id']);

        $transactions = Transaction::create([
            'payment_way_id' => $data['payment_way_id'],
            'created_by' => Auth::id(),
            'type' => 'receive',
            'amount' => $data['amount'],
            'commission' => $data['commission'],
            'notes' => __('messages.payment_for_installment').$installment->contract->client->name.' - '.$installment->contract->product->name,
            'attachment' => null,
            'client_id' => $installment->contract->client_id,
        ]);

        $total = $data['amount'] + $data['commission'];
        $transactions->paymentWay->increment('balance', $total);

        $payment = $installment->payments()->create([
            'transaction_id' => $transactions->id,
            'amount' => $data['amount'],
            'payment_date' => $data['payment_date'],
            'paid_by' => Auth::id(),
        ]);

        $installment->paid_amount += $data['amount'];
        $installment->status = $installment->paid_amount >= $installment->required_amount ? 'paid' : 'pending';
        $installment->save();

        $client = $installment->contract->client;
        $client->decrement('debt', $data['amount']);

        return response()->json([
            'status' => true,
            'message' => __('messages.installment_paid_successfully'),
            'data' => $installment->load('payments'),
        ]);
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
