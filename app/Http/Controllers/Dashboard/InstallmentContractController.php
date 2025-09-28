<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\InstallmentContract\StoreInstallmentContractRequest;
use App\Http\Requests\InstallmentContract\UpdateInstallmentContractRequest;
use App\Http\Resources\InstallmentContractResource;
use App\Models\Client;
use App\Models\Installment;
use App\Models\InstallmentContract;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class InstallmentContractController extends BaseController
{
    public function __construct()
    {
        // $this->middleware('check.permission:installments_index')->only('index');
        // $this->middleware('check.permission:installments_update')->only(['edit', 'update']);
    }

    public function index()
    {
        $clients = Client::all();
        $products = Product::all();

        return view('dashboard.installment_contracts.index', compact('clients', 'products'));
    }

    public function list()
    {
        $installments = InstallmentContract::with(['client', 'product', 'creator', 'installments'])->get();

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

        // المبلغ الكلي
        $totalAmount = $remainingAmount + $interestAmount;

        // قيمة القسط الشهري
        $installmentCount = $data['installment_count'];
        $installmentAmount = $totalAmount / $installmentCount;

        // إنشاء العقد
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

        // إنشاء الأقساط
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

        return response()->json([
            'status' => true,
            'message' => __('messages.Installment_contract_created_successfully'),
            'data' => $contract->load('installments'),
        ], 201);
    }

    public function show($id)
    {
        $contract = InstallmentContract::with(['client', 'product', 'creator', 'installments.payments'])
            ->findOrFail($id);

        return response()->json([
            'status' => true,
            'message' => __('messages.installment_contract_fetched_successfully'),
            'data' => new InstallmentContractResource($contract),
        ]);
    }

    public function showPage($id)
    {
        $contract = InstallmentContract::with([
            'client',
            'product',
            'creator',
            'installments.payments',
        ])->findOrFail($id);

        return view('dashboard.installment_contracts.show', compact('contract'));
    }

    public function update(UpdateInstallmentContractRequest $request, $id)
    {
        $contract = InstallmentContract::with('installments')->findOrFail($id);
        $data = $request->validated();

        // check لو فيه أقساط مدفوعة
        $hasPaidInstallments = $contract->installments()->where('status', 'paid')->exists();

        // لو المستخدم عدل أي حاجة مؤثرة على الحسابات
        $recalculate = isset($data['product_price']) ||
                       isset($data['down_payment']) ||
                       isset($data['interest_rate']) ||
                       isset($data['installment_count']) ||
                       isset($data['start_date']);

        if ($recalculate) {
            if ($hasPaidInstallments) {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.cannot_update_contract_with_paid_installments'),
                ], 400);
            }

            $productPrice = $data['product_price'] ?? $contract->product_price;
            $downPayment = $data['down_payment'] ?? $contract->down_payment;
            $remainingAmount = $productPrice - $downPayment;

            $interestRate = $data['interest_rate'] ?? $contract->interest_rate;
            $interestAmount = ($remainingAmount * $interestRate) / 100;

            $installmentCount = $data['installment_count'] ?? $contract->installment_count;
            $totalAmount = $remainingAmount + $interestAmount;
            $installmentAmount = $totalAmount / $installmentCount;

            $startDate = Carbon::parse($data['start_date'] ?? $contract->start_date);

            // تحديث بيانات العقد
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
                'client_id' => $data['client_id'] ?? $contract->client_id,
                'product_id' => $data['product_id'] ?? $contract->product_id,
            ]);

            // مسح الأقساط القديمة (pending) فقط
            $contract->installments()->delete();

            // إنشاء الأقساط الجديدة
            for ($i = 1; $i <= $installmentCount; $i++) {
                Installment::create([
                    'due_date' => $startDate->copy()->addMonths($i),
                    'required_amount' => $installmentAmount,
                    'paid_amount' => 0,
                    'status' => 'pending',
                    'installment_contract_id' => $contract->id,
                ]);
            }
        } else {
            // تعديل بيانات بسيطة (مثلا client_id او product_id بس)
            $contract->update($data);
        }

        return response()->json([
            'status' => true,
            'message' => __('messages.installment_contract_updated_successfully'),
            'data' => new InstallmentContractResource($contract->load('installments')),
        ]);
    }

    public function pay(Request $request)
    {
        $request->validate([
            'installment_id' => 'required|exists:installments,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
        ]);

        $installment = Installment::findOrFail($request->installment_id);

        // سجل الدفع
        $payment = $installment->payments()->create([
            'transaction_id' => null, // ممكن تربطه بـ Transaction
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'paid_by' => Auth::id(),
        ]);

        // حدث المبلغ المدفوع وحالة القسط
        $installment->paid_amount += $request->amount;
        $installment->status = $installment->paid_amount >= $installment->required_amount ? 'paid' : 'pending';
        $installment->save();
        
        // إنقاص مديونية العميل
        $client = $installment->contract->client;
    $client->decrement('debt', $request->amount);

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

        // امسح الأقساط الأول
        $contract->installments()->delete();
        $contract->delete();

        return response()->json([
            'status' => true,
            'message' => __('messages.installment_contract_deleted_successfully'),
        ]);
    }
}
