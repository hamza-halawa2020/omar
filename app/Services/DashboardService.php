<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Installment;
use App\Models\PaymentWay;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function analytics(Request $request): array
    {
        $filterData = $this->getDateFilter($request);
        $value = (int) ($request->analytics_number ?? 5);

        return [
            'filter' => [
                'type' => $filterData['filterType'],
                'start_date' => $filterData['startDate']->format('Y-m-d'),
                'end_date' => $filterData['endDate']->format('Y-m-d'),
            ],
            'statistics' => [
                'top_clients_by_debt' => $this->getTopClientsByDebt($value),
                'top_clients_by_installments' => $this->getTopClientsByInstallments($value),
                'top_overdue_installments' => $this->getTopOverdueInstallments($value),
                'upcoming_installments' => $this->getUpcomingInstallments($value),
                'top_payment_ways_by_send' => $this->getTopPaymentWaysBySend($filterData['startDate'], $filterData['endDate'], $value),
                'top_payment_ways_by_receive' => $this->getTopPaymentWaysByReceive($filterData['startDate'], $filterData['endDate'], $value),
                'top_payment_ways_by_balance' => $this->getTopPaymentWaysByBalance($value),
                'top_payment_ways_nearing_send_limit' => $this->getTopPaymentWaysNearingSendLimit($value),
                'top_payment_ways_nearing_receive_limit' => $this->getTopPaymentWaysNearingReceiveLimit($value),
                'top_products_by_installments' => $this->getTopProductsByInstallments($value),
                'last_send_transactions' => $this->getLastSendTransactions($filterData['startDate'], $filterData['endDate'], $value),
                'last_receive_transactions' => $this->getLastReceiveTransactions($filterData['startDate'], $filterData['endDate'], $value),
                'total_revenue' => $this->getTotalRevenue($filterData['startDate'], $filterData['endDate']),
                'total_payment_ways_balance' => $this->getTotalPaymentWaysBalance(),
            ],
        ];
    }

    private function getTotalPaymentWaysBalance(): float|int
    {
        return PaymentWay::sum('balance');
    }

    private function getDateFilter(Request $request): array
    {
        $filterType = $request->input('filter_type', 'today');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($filterType === 'today') {
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
        } elseif ($filterType === 'month') {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::today()->endOfDay();
        } elseif ($filterType === 'custom' && $startDate && $endDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
        } else {
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
        }

        return [
            'filterType' => $filterType,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];
    }

    private function getTopClientsByDebt(int $value)
    {
        return Client::query()
            ->select(['id', 'name', 'debt'])
            ->whereDoesntHave('installmentContracts')
            ->orderByDesc('debt')
            ->take($value)
            ->get()
            ->map(fn ($client) => [
                'id' => $client->id,
                'name' => $client->name,
                'total_remaining_amount' => $client->debt,
            ]);
    }

    private function getTopClientsByInstallments(int $value)
    {
        return Client::query()
            ->select(['id', 'name'])
            ->withCount('installmentContracts')
            ->orderByDesc('installment_contracts_count')
            ->take($value)
            ->get()
            ->map(fn ($client) => [
                'id' => $client->id,
                'name' => $client->name,
                'installment_count' => $client->installment_contracts_count,
            ]);
    }

    private function getTopOverdueInstallments(int $value)
    {
        return Installment::query()
            ->join('installment_contracts', 'installment_contracts.id', '=', 'installments.installment_contract_id')
            ->leftJoin('clients', 'clients.id', '=', 'installment_contracts.client_id')
            ->where('installments.status', 'late')
            ->select([
                'installments.id',
                'clients.name as client_name',
                'installments.due_date',
                DB::raw('installments.required_amount - installments.paid_amount as overdue_amount'),
            ])
            ->orderByDesc('overdue_amount')
            ->take($value)
            ->get()
            ->map(fn ($installment) => [
                'id' => $installment->id,
                'client_name' => $installment->client_name ?? '',
                'due_date' => $installment->due_date->format('Y-m-d'),
                'overdue_amount' => $installment->overdue_amount,
            ]);
    }

    private function getUpcomingInstallments(int $value)
    {
        return Installment::query()
            ->join('installment_contracts', 'installment_contracts.id', '=', 'installments.installment_contract_id')
            ->leftJoin('clients', 'clients.id', '=', 'installment_contracts.client_id')
            ->where('installments.status', 'pending')
            ->where('installments.due_date', '>=', Carbon::today())
            ->select([
                'installments.id',
                'clients.name as client_name',
                'installments.due_date',
                'installments.required_amount',
            ])
            ->orderBy('installments.due_date')
            ->take($value)
            ->get()
            ->map(fn ($installment) => [
                'id' => $installment->id,
                'client_name' => $installment->client_name ?? '',
                'due_date' => $installment->due_date->format('Y-m-d'),
                'required_amount' => $installment->required_amount,
            ]);
    }

    private function getTopPaymentWaysBySend(Carbon $startDate, Carbon $endDate, int $value)
    {
        return PaymentWay::query()
            ->select(['id', 'name'])
            ->withCount([
                'transactions' => function ($query) use ($startDate, $endDate) {
                    $query->where('type', 'send')->whereBetween('created_at', [$startDate, $endDate]);
                },
            ])
            ->orderByDesc('transactions_count')
            ->take($value)
            ->get()
            ->map(fn ($paymentWay) => [
                'id' => $paymentWay->id,
                'name' => $paymentWay->name,
                'transaction_count' => $paymentWay->transactions_count,
            ]);
    }

    private function getTopPaymentWaysByReceive(Carbon $startDate, Carbon $endDate, int $value)
    {
        return PaymentWay::query()
            ->select(['id', 'name'])
            ->withCount([
                'transactions' => function ($query) use ($startDate, $endDate) {
                    $query->where('type', 'receive')->whereBetween('created_at', [$startDate, $endDate]);
                },
            ])
            ->orderByDesc('transactions_count')
            ->take($value)
            ->get()
            ->map(fn ($paymentWay) => [
                'id' => $paymentWay->id,
                'name' => $paymentWay->name,
                'transaction_count' => $paymentWay->transactions_count,
            ]);
    }

    private function getTopPaymentWaysByBalance(int $value)
    {
        return PaymentWay::query()
            ->select(['id', 'name', 'balance'])
            ->orderByDesc('balance')
            ->take($value)
            ->get()
            ->map(fn ($paymentWay) => [
                'id' => $paymentWay->id,
                'name' => $paymentWay->name,
                'balance' => $paymentWay->balance,
            ]);
    }

    private function getTopPaymentWaysNearingSendLimit(int $value)
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        return PaymentWay::query()
            ->select([
                'payment_ways.id',
                'payment_ways.name',
                'payment_ways.send_limit',
                DB::raw('COALESCE(payment_way_limits.send_used, 0) as send_used'),
            ])
            ->leftJoin('payment_way_limits', function ($join) use ($currentYear, $currentMonth) {
                $join->on('payment_way_limits.payment_way_id', '=', 'payment_ways.id')
                    ->where('payment_way_limits.year', $currentYear)
                    ->where('payment_way_limits.month', $currentMonth);
            })
            ->whereNotNull('payment_ways.send_limit')
            ->orderByDesc(DB::raw('COALESCE(payment_way_limits.send_used, 0) / NULLIF(payment_ways.send_limit, 0)'))
            ->take($value)
            ->get()
            ->map(function ($paymentWay) {
                return [
                    'id' => $paymentWay->id,
                    'name' => $paymentWay->name,
                    'send_limit' => $paymentWay->send_limit,
                    'send_used' => $paymentWay->send_used,
                    'percentage_used' => $paymentWay->send_limit ? ($paymentWay->send_used / $paymentWay->send_limit * 100) : 0,
                ];
            })->values();
    }

    private function getTopPaymentWaysNearingReceiveLimit(int $value)
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        return PaymentWay::query()
            ->select([
                'payment_ways.id',
                'payment_ways.name',
                'payment_ways.receive_limit',
                DB::raw('COALESCE(payment_way_limits.receive_used, 0) as receive_used'),
            ])
            ->leftJoin('payment_way_limits', function ($join) use ($currentYear, $currentMonth) {
                $join->on('payment_way_limits.payment_way_id', '=', 'payment_ways.id')
                    ->where('payment_way_limits.year', $currentYear)
                    ->where('payment_way_limits.month', $currentMonth);
            })
            ->whereNotNull('payment_ways.receive_limit')
            ->orderByDesc(DB::raw('COALESCE(payment_way_limits.receive_used, 0) / NULLIF(payment_ways.receive_limit, 0)'))
            ->take($value)
            ->get()
            ->map(function ($paymentWay) {
                return [
                    'id' => $paymentWay->id,
                    'name' => $paymentWay->name,
                    'receive_limit' => $paymentWay->receive_limit,
                    'receive_used' => $paymentWay->receive_used,
                    'percentage_used' => $paymentWay->receive_limit ? ($paymentWay->receive_used / $paymentWay->receive_limit * 100) : 0,
                ];
            })->values();
    }

    private function getTopProductsByInstallments(int $value)
    {
        return Product::query()
            ->select(['id', 'name'])
            ->withCount('installmentContracts')
            ->orderByDesc('installment_contracts_count')
            ->take($value)
            ->get()
            ->map(fn ($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'installment_contract_count' => $product->installment_contracts_count,
            ]);
    }

    private function getLastSendTransactions(Carbon $startDate, Carbon $endDate, int $value)
    {
        return Transaction::where('transactions.type', 'send')
            ->leftJoin('clients', 'clients.id', '=', 'transactions.client_id')
            ->leftJoin('payment_ways', 'payment_ways.id', '=', 'transactions.payment_way_id')
            ->select([
                'transactions.id',
                'clients.name as client_name',
                'payment_ways.name as payment_way',
                'transactions.amount',
                'transactions.created_at',
            ])
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->orderByDesc('transactions.created_at')
            ->take($value)
            ->get()
            ->map(fn ($transaction) => [
                'id' => $transaction->id,
                'client_name' => $transaction->client_name ?? '',
                'payment_way' => $transaction->payment_way ?? '',
                'amount' => $transaction->amount,
                'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
            ]);
    }

    private function getLastReceiveTransactions(Carbon $startDate, Carbon $endDate, int $value)
    {
        return Transaction::where('transactions.type', 'receive')
            ->leftJoin('clients', 'clients.id', '=', 'transactions.client_id')
            ->leftJoin('payment_ways', 'payment_ways.id', '=', 'transactions.payment_way_id')
            ->select([
                'transactions.id',
                'clients.name as client_name',
                'payment_ways.name as payment_way',
                'transactions.amount',
                'transactions.created_at',
            ])
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->orderByDesc('transactions.created_at')
            ->take($value)
            ->get()
            ->map(fn ($transaction) => [
                'id' => $transaction->id,
                'client_name' => $transaction->client_name ?? '',
                'payment_way' => $transaction->payment_way ?? '',
                'amount' => $transaction->amount,
                'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
            ]);
    }

    private function getTotalRevenue(Carbon $startDate, Carbon $endDate): float|int
    {
        return Transaction::where('type', 'receive')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
    }
}
