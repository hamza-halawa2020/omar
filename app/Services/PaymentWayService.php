<?php

namespace App\Services;

use App\Models\PaymentWay;
use App\Models\Transaction;
use App\Services\Concerns\BuildsPaymentWayLogData;
use App\Services\Concerns\HandlesWalletMonthlyLimits;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
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

    public function list(Request $request): array
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $page = max((int) $request->input('page', 1), 1);
        $perPage = min(max((int) $request->input('per_page', 24), 1), 60);
        $query = PaymentWay::query()
            ->select([
                'id',
                'name',
                'type',
                'phone_number',
                'client_type',
                'receive_limit',
                'receive_limit_alert',
                'send_limit',
                'send_limit_alert',
                'balance',
                'created_by',
                'created_at',
                'updated_at',
                'position',
            ])
            ->with([
                'creator:id,name,email',
                'monthlyLimits' => function ($query) use ($currentMonth, $currentYear) {
                    $query
                        ->select([
                            'id',
                            'payment_way_id',
                            'month',
                            'year',
                            'send_limit',
                            'send_used',
                            'receive_limit',
                            'receive_used',
                        ])
                        ->where('month', $currentMonth)
                        ->where('year', $currentYear);
                },
            ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($nestedQuery) use ($request) {
                    $search = '%'.$request->search.'%';

                    $nestedQuery->where('name', 'like', $search)
                        ->orWhere('phone_number', 'like', $search)
                        ->orWhere('type', 'like', $search);
                });
            });

        $total = (clone $query)->count();

        $items = $query
            ->orderBy('position', 'asc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $lastPage = max((int) ceil($total / $perPage), 1);

        return [
            'items' => $items,
            'meta' => [
                'current_page' => $page,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'total' => $total,
                'has_more' => $page < $lastPage,
                'next_page' => $page < $lastPage ? $page + 1 : null,
            ],
        ];
    }

    public function stats(): array
    {
        $stats = PaymentWay::query()
            ->selectRaw('COALESCE(SUM(balance), 0) as total_balance')
            ->selectRaw("SUM(CASE WHEN type = 'wallet' THEN 1 ELSE 0 END) as total_wallets")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'wallet' THEN balance ELSE 0 END), 0) as wallet_balance")
            ->selectRaw("SUM(CASE WHEN type = 'cash' THEN 1 ELSE 0 END) as total_cash")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'cash' THEN balance ELSE 0 END), 0) as cash_balance")
            ->selectRaw("SUM(CASE WHEN type = 'balance_machine' THEN 1 ELSE 0 END) as total_machines")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'balance_machine' THEN balance ELSE 0 END), 0) as machine_balance")
            ->first();

        return [
            'total_balance' => (float) $stats->total_balance,
            'total_wallets' => (int) $stats->total_wallets,
            'wallet_balance' => (float) $stats->wallet_balance,
            'total_cash' => (int) $stats->total_cash,
            'cash_balance' => (float) $stats->cash_balance,
            'total_machines' => (int) $stats->total_machines,
            'machine_balance' => (float) $stats->machine_balance,
        ];
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
        $paymentWay = PaymentWay::with(['creator', 'logs', 'monthlyLimits'])->findOrFail($id);

        $transactions = Transaction::query()
            ->with(['client', 'product', 'products.product', 'installmentPayment', 'paymentWay', 'paymentSplits.paymentWay'])
            ->where(function ($query) use ($id) {
                $query->where('payment_way_id', $id)
                    ->orWhereHas('paymentSplits', fn ($query) => $query->where('payment_way_id', $id));
            })
            ->latest();
        try {
            if ($timeFilter === 'custom' && $startDate && $endDate) {
                $transactions->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
            } elseif ($timeFilter === 'today') {
                $transactions->whereBetween('created_at', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()]);
            }
        } catch (Exception) {
            throw new HttpResponseException(response()->json(['status' => false, 'message' => __('messages.invalid_date_format')], 400));
        }

        $transactions = $transactions->get()
            ->map(function (Transaction $transaction) use ($id) {
                $split = $transaction->paymentSplits->firstWhere('payment_way_id', $id);

                if ($split) {
                    $transaction->payment_way_split_amount = (float) $split->amount;
                    $transaction->payment_way_split_commission = $this->paymentWayTransactionCommission($transaction, $id);
                    $transaction->payment_way_split_balance_before = (float) $split->balance_before_transaction;
                    $transaction->payment_way_split_balance_after = (float) $split->balance_after_transaction;
                }

                return $transaction;
            });

        $paymentWay->setRelation('transactions', $transactions);

        $receiveTransactions = $paymentWay->transactions->where('type', 'receive');
        $sendTransactions = $paymentWay->transactions->where('type', 'send');

        $receiveTotal = $receiveTransactions->sum(fn ($transaction) => $this->paymentWayTransactionTotal($transaction, $id));
        $receiveCommission = $receiveTransactions->sum(fn ($transaction) => $this->paymentWayTransactionCommission($transaction, $id));
        $receiveAmount = max(0, $receiveTotal - $receiveCommission);

        $sendTotal = $sendTransactions->sum(fn ($transaction) => $this->paymentWayTransactionTotal($transaction, $id));
        $sendCommission = $sendTransactions->sum(fn ($transaction) => $this->paymentWayTransactionCommission($transaction, $id));
        $sendAmount = max(0, $sendTotal - $sendCommission);

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

    private function paymentWayTransactionTotal(Transaction $transaction, int $paymentWayId): float
    {
        $split = $transaction->paymentSplits->firstWhere('payment_way_id', $paymentWayId);

        if ($split) {
            return (float) $split->amount;
        }

        return (float) $transaction->amount + (float) $transaction->commission;
    }

    private function paymentWayTransactionCommission(Transaction $transaction, int $paymentWayId): float
    {
        $total = (float) $transaction->amount + (float) $transaction->commission;

        if ($total <= 0 || (float) $transaction->commission <= 0) {
            return 0.0;
        }

        return $transaction->commission * ($this->paymentWayTransactionTotal($transaction, $paymentWayId) / $total);
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
