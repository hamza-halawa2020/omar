<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Installment;
use App\Models\InstallmentContract;
use App\Models\PaymentWay;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\Concerns\HandlesTransactionConcurrency;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\WhatsAppService;

class InstallmentContractService
{
    use HandlesTransactionConcurrency;

    public function indexData(): array
    {
        $clients = Client::where('type', 'client')->get();
        $products = Product::all();

        return compact('clients', 'products');
    }

    public function list(): Collection
    {
        return InstallmentContract::with([
            'client' => function ($query) {
                $query->where('type', 'client');
            },
            'product',
            'creator',
            'installments'
        ])->latest()->get();
    }

    public function store(array $data): InstallmentContract
    {
        return DB::transaction(function () use ($data) {
            [$productPrice, $downPayment, $remainingAmount, $interestRate, $interestAmount, $totalAmount, $installmentCount, $installmentAmount] = $this->calculateContractFinancials($data);

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
            if ($client) {
                $client->source_model = $contract;
                $client->log_description = __('messages.Installment_contract_created_successfully');
                $client->increment('debt', $totalAmount);
            }

            $this->createInstallments($contract, $data['start_date'], $installmentCount, $installmentAmount);

            $product = Product::find($data['product_id']);
            if ($product) {
                $product->decrement('stock', 1);
            }

            return $contract->load('installments');
        });
    }

    public function show(int $id): InstallmentContract
    {
        return InstallmentContract::with([
            'client' => function ($query) {
                $query->where('type', 'client');
            },
            'product',
            'creator',
            'installments.payments.paid_by'
        ])->findOrFail($id);
    }

    public function showPage(int $id): array
    {
        $contract = InstallmentContract::with([
            'client' => function ($query) {
                $query->where('type', 'client');
            },
            'product',
            'creator',
            'installments.payments'
        ])->findOrFail($id);

        $paymentWays = PaymentWay::all();

        return compact('contract', 'paymentWays');
    }

    public function update(int $id, array $data): InstallmentContract
    {
        return DB::transaction(function () use ($id, $data) {
            $contract = InstallmentContract::with('installments')->findOrFail($id);
            $hasPaidInstallments = $contract->installments()->where('status', 'paid')->exists();

            $recalculate = isset($data['product_price']) || isset($data['down_payment']) || isset($data['interest_rate']) || isset($data['installment_count']) || isset($data['start_date']);

            if (!$recalculate) {
                $contract->update($data);

                return $contract->load('installments');
            }

            if ($hasPaidInstallments) {
                throw new HttpResponseException(
                    response()->json(['status' => false, 'message' => __('messages.cannot_update_contract_with_paid_installments')], 400)
                );
            }

            [$productPrice, $downPayment, $remainingAmount, $interestRate, $interestAmount, $totalAmount, $installmentCount, $installmentAmount] = $this->calculateContractFinancials($data);
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
            $this->createInstallments($contract, $startDate, $installmentCount, $installmentAmount);

            $client = Client::find($data['client_id']);
            if ($client) {
                $client->source_model = $contract;
                $client->log_description = __('messages.installment_contract_updated_successfully');
                $client->decrement('debt', $oldTotal);
                $client->increment('debt', $totalAmount);
            }

            return $contract->load('installments');
        });
    }

    public function pay(array $data): array
    {
        $installment = Installment::with('contract.client', 'contract.product')->findOrFail($data['installment_id']);
        $client = $installment->contract->client;
        $product = $installment->contract->product;
        $total = $data['amount'] + ($data['commission'] ?? 0);

        return $this->withTransactionSubmissionLock('installment-payment', $data, function () use ($data, $installment, $client, $product, $total) {
            return DB::transaction(function () use ($data, $installment, $client, $product, $total) {
                $paymentWay = $this->lockedPaymentWay($data['payment_way_id']);
                $monthlyLimit = $this->lockedCurrentMonthlyLimit($paymentWay);
                $this->assertPaymentWayCanHandleTransaction($paymentWay, $monthlyLimit, 'receive', $data['amount'], $total);

                $transaction = Transaction::create([
                    'payment_way_id' => $paymentWay->id,
                    'created_by' => Auth::id(),
                    'type' => 'receive',
                    'amount' => $data['amount'],
                    'commission' => $data['commission'] ?? 0,
                    'notes' => __('messages.payment_for_installment') . ' ' . ($client->name ?? '') . ' - ' . ($product->name ?? ''),
                    'client_id' => $client->id ?? null,
                    'balance_before_transaction' => $paymentWay->balance,
                    'balance_after_transaction' => $paymentWay->balance + $total,
                ]);

                $paymentWay->increment('balance', $total);
                if ($monthlyLimit) {
                    $monthlyLimit->increment('receive_used', $total);
                }

                $installment->payments()->create([
                    'transaction_id' => $transaction->id,
                    'amount' => $data['amount'],
                    'payment_date' => $data['payment_date'],
                    'paid_by' => Auth::id(),
                ]);

                $installment->increment('paid_amount', $data['amount']);
                $installment->status = $installment->paid_amount >= $installment->required_amount ? 'paid' : 'pending';
                $installment->save();

                if ($client) {
                    $client->source_model = $transaction;
                    $client->log_description = __('messages.installment_paid_successfully');
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

                if ($client) {
                    app(WhatsAppService::class)->sendTransactionMessage($client, $data['amount'], 'receive', ['installment' => true]);
                }

                return [
                    'installment' => $installment->load('payments'),
                    'transaction' => $transaction,
                ];
            });
        });
    }

    public function destroy(int $id): void
    {
        DB::transaction(function () use ($id) {
            $contract = InstallmentContract::with('installments')->findOrFail($id);

            if ($contract->installments()->where('status', 'paid')->exists()) {
                throw new HttpResponseException(
                    response()->json([
                        'status' => false,
                        'message' => __('messages.cannot_delete_contract_with_paid_installments'),
                    ], 400)
                );
            }

            $contract->installments()->delete();
            $contract->delete();
        });
    }

    private function calculateContractFinancials(array $data): array
    {
        $productPrice = $data['product_price'];
        $downPayment = $data['down_payment'] ?? 0;
        $remainingAmount = $productPrice - $downPayment;
        $interestRate = $data['interest_rate'] ?? 0;
        $interestAmount = ($remainingAmount * $interestRate) / 100;
        $totalAmount = $remainingAmount + $interestAmount;
        $installmentCount = $data['installment_count'];
        $installmentAmount = $totalAmount / $installmentCount;

        return [$productPrice, $downPayment, $remainingAmount, $interestRate, $interestAmount, $totalAmount, $installmentCount, $installmentAmount];
    }

    private function createInstallments(InstallmentContract $contract, Carbon|string $startDate, int $installmentCount, float|int $installmentAmount): void
    {
        $parsedStartDate = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);

        for ($i = 1; $i <= $installmentCount; $i++) {
            Installment::create([
                'due_date' => $parsedStartDate->copy()->addMonths($i),
                'required_amount' => $installmentAmount,
                'paid_amount' => 0,
                'status' => 'pending',
                'installment_contract_id' => $contract->id,
            ]);
        }
    }

}
