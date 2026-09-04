<?php

namespace App\Services;

use App\Models\PaymentWay;
use App\Services\Concerns\BuildsPaymentWayLogData;
use App\Services\Concerns\HandlesWalletMonthlyLimits;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentWayService
{
    use BuildsPaymentWayLogData;
    use HandlesWalletMonthlyLimits;

    public function indexData(): array
    {
        return [];
    }

    public function list(): Collection
    {
        return PaymentWay::with(['creator', 'monthlyLimits'])
            ->orderBy('position', 'asc')
            ->get();
    }

    public function store(array $data): PaymentWay
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = Auth::id();
            $data['position'] = PaymentWay::max('position') + 1;

            $paymentWay = PaymentWay::create($data);
            $this->getOrCreateCurrentMonthlyLimit($paymentWay);
            $this->logAction($paymentWay, 'create');

            return $paymentWay->load(['creator']);
        });
    }

    public function showList(int $id, string $timeFilter = 'today', ?string $startDate = null, ?string $endDate = null): array
    {
        $paymentWay = PaymentWay::with([
            'creator', 'transactions.client',
            'transactions.product', 'transactions.installmentPayment', 'logs', 'monthlyLimits',
        ])->findOrFail($id);

        $transactions = $paymentWay->transactions()->with(['client', 'product', 'installmentPayment']);
        try {
            if ($timeFilter === 'custom' && $startDate && $endDate) {
                $transactions->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
            } elseif ($timeFilter === 'today') {
                $transactions->whereBetween('created_at', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()]);
            }
        } catch (Exception) {
            throw new HttpResponseException(response()->json(['status' => false, 'message' => __('messages.invalid_date_format')], 400));
        }

        $paymentWay->transactions = $transactions->get();

        $receiveTransactions = $paymentWay->transactions->where('type', 'receive');
        $sendTransactions = $paymentWay->transactions->where('type', 'send');

        $receiveAmount = $receiveTransactions->sum('amount');
        $receiveCommission = $receiveTransactions->sum('commission');
        $receiveTotal = $receiveAmount + $receiveCommission;

        $sendAmount = $sendTransactions->sum('amount');
        $sendCommission = $sendTransactions->sum('commission');
        $sendTotal = $sendAmount + $sendCommission;

        $grandNet = $receiveTotal - $sendTotal;

        $currentLimit = $paymentWay->monthlyLimits()
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->first();

        return [
            'paymentWay' => $paymentWay,
            'statistics' => [
                'receive' => [
                    'receive_amount' => number_format($receiveAmount, 2, '.', ''),
                    'receive_commission' => number_format($receiveCommission, 2, '.', ''),
                    'receive_total' => number_format($receiveTotal, 2, '.', ''),
                ],
                'send' => [
                    'send_amount' => number_format($sendAmount, 2, '.', ''),
                    'send_commission' => number_format($sendCommission, 2, '.', ''),
                    'send_total' => number_format($sendTotal, 2, '.', ''),
                ],
                'grand_net' => number_format($grandNet, 2, '.', ''),
                'limits' => [
                    'send_limit' => $currentLimit ? number_format($currentLimit->send_limit, 2, '.', '') : 0,
                    'send_used' => $currentLimit ? number_format($currentLimit->send_used, 2, '.', '') : 0,
                    'send_remaining' => $currentLimit ? number_format($currentLimit->send_limit - $currentLimit->send_used, 2, '.', '') : 0,
                    'receive_limit' => $currentLimit ? number_format($currentLimit->receive_limit, 2, '.', '') : 0,
                    'receive_used' => $currentLimit ? number_format($currentLimit->receive_used, 2, '.', '') : 0,
                    'receive_remaining' => $currentLimit ? number_format($currentLimit->receive_limit - $currentLimit->receive_used, 2, '.', '') : 0,
                ],
            ],
        ];
    }

    public function update(int $id, array $data): PaymentWay
    {
        return DB::transaction(function () use ($id, $data) {
            $paymentWay = PaymentWay::findOrFail($id);

            $oldSendLimit = $paymentWay->send_limit;
            $oldReceiveLimit = $paymentWay->receive_limit;
            $paymentWay->update($data);

            if ($oldSendLimit != $paymentWay->send_limit || $oldReceiveLimit != $paymentWay->receive_limit) {
                $currentLimit = $this->getOrCreateCurrentMonthlyLimit($paymentWay);
                if ($currentLimit) {
                    $currentLimit->update([
                        'send_limit' => $paymentWay->send_limit,
                        'receive_limit' => $paymentWay->receive_limit,
                    ]);
                }
            }

            $this->logAction($paymentWay, 'update');

            return $paymentWay->load(['creator']);
        });
    }

    public function destroy(int $id): void
    {
        DB::transaction(function () use ($id) {
            $paymentWay = PaymentWay::findOrFail($id);
            if ($paymentWay->transactions()->exists()) {
                throw new HttpResponseException(response()->json(['status' => false, 'message' => __('messages.cannot_delete_payment_way_has_transactions')], 400));
            }

            $this->logAction($paymentWay, 'delete');
            $paymentWay->delete();
        });
    }

    public function reorder(array $order): void
    {
        if (empty($order)) {
            throw new HttpResponseException(response()->json(['status' => false, 'message' => 'Invalid order'], 400));
        }

        DB::transaction(function () use ($order) {
            foreach ($order as $position => $id) {
                PaymentWay::where('id', $id)->update(['position' => $position + 1]);
            }
        });
    }

    private function logAction(PaymentWay $paymentWay, string $action): void
    {
        $paymentWay->logs()->create([
                'created_by' => Auth::id(),
                'action' => $action,
            'data' => $this->paymentWayLogData($paymentWay),
        ]);
    }
}
