<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Installment;
use App\Models\PaymentWay;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        return Client::whereDoesntHave('installmentContracts')->with('installmentContracts.installments')
            ->get()
            ->sortByDesc('debt')
            ->take($value)
            ->map(fn ($client) => [
                'id' => $client->id,
                'name' => $client->name,
                'total_remaining_amount' => $client->debt,
            ])->values();
    }

    private function getTopClientsByInstallments(int $value)
    {
        return Client::withCount('installmentContracts')
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
        return Installment::where('status', 'late')
            ->with(['contract.client'])
            ->get()
            ->sortByDesc(fn ($installment) => $installment->required_amount - $installment->paid_amount)
            ->take($value)
            ->map(fn ($installment) => [
                'id' => $installment->id,
                'client_name' => $installment->contract->client->name,
                'due_date' => $installment->due_date->format('Y-m-d'),
                'overdue_amount' => $installment->required_amount - $installment->paid_amount,
            ])->values();
    }

    private function getUpcomingInstallments(int $value)
    {
        return Installment::where('status', 'pending')
            ->where('due_date', '>=', Carbon::today())
            ->with(['contract.client'])
            ->orderBy('due_date')
            ->take($value)
            ->get()
            ->map(fn ($installment) => [
                'id' => $installment->id,
                'client_name' => $installment->contract->client->name,
                'due_date' => $installment->due_date->format('Y-m-d'),
                'required_amount' => $installment->required_amount,
            ]);
    }

    private function getTopPaymentWaysBySend(Carbon $startDate, Carbon $endDate, int $value)
    {
        return PaymentWay::withCount(['transactions' => function ($query) use ($startDate, $endDate) {
            $query->where('type', 'send')->whereBetween('created_at', [$startDate, $endDate]);
        }])->orderByDesc('transactions_count')
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
        return PaymentWay::withCount(['transactions' => function ($query) use ($startDate, $endDate) {
            $query->where('type', 'receive')->whereBetween('created_at', [$startDate, $endDate]);
        }])->orderByDesc('transactions_count')
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
        return PaymentWay::orderByDesc('balance')
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

        return PaymentWay::whereNotNull('send_limit')
            ->with(['monthlyLimits' => function ($query) use ($currentYear, $currentMonth) {
                $query->where('year', $currentYear)->where('month', $currentMonth);
            }])
            ->get()
            ->sortByDesc(function ($paymentWay) {
                $monthlyLimit = $paymentWay->monthlyLimits->first();
                return $monthlyLimit ? ($monthlyLimit->send_used / $paymentWay->send_limit) : 0;
            })
            ->take($value)
            ->map(function ($paymentWay) {
                $monthlyLimit = $paymentWay->monthlyLimits->first();
                return [
                    'id' => $paymentWay->id,
                    'name' => $paymentWay->name,
                    'send_limit' => $paymentWay->send_limit,
                    'send_used' => $monthlyLimit ? $monthlyLimit->send_used : 0,
                    'percentage_used' => $monthlyLimit && $paymentWay->send_limit ? ($monthlyLimit->send_used / $paymentWay->send_limit * 100) : 0,
                ];
            })->values();
    }

    private function getTopPaymentWaysNearingReceiveLimit(int $value)
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        return PaymentWay::whereNotNull('receive_limit')
            ->with(['monthlyLimits' => function ($query) use ($currentYear, $currentMonth) {
                $query->where('year', $currentYear)->where('month', $currentMonth);
            }])
            ->get()
            ->sortByDesc(function ($paymentWay) {
                $monthlyLimit = $paymentWay->monthlyLimits->first();
                return $monthlyLimit ? ($monthlyLimit->receive_used / $paymentWay->receive_limit) : 0;
            })
            ->take($value)
            ->map(function ($paymentWay) {
                $monthlyLimit = $paymentWay->monthlyLimits->first();
                return [
                    'id' => $paymentWay->id,
                    'name' => $paymentWay->name,
                    'receive_limit' => $paymentWay->receive_limit,
                    'receive_used' => $monthlyLimit ? $monthlyLimit->receive_used : 0,
                    'percentage_used' => $monthlyLimit && $paymentWay->receive_limit ? ($monthlyLimit->receive_used / $paymentWay->receive_limit * 100) : 0,
                ];
            })->values();
    }

    private function getTopProductsByInstallments(int $value)
    {
        return Product::withCount('installmentContracts')
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
        return Transaction::where('type', 'send')
            ->with(['client', 'paymentWay'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('created_at')
            ->take($value)
            ->get()
            ->map(fn ($transaction) => [
                'id' => $transaction->id,
                'client_name' => $transaction->client ? $transaction->client->name : '',
                'payment_way' => $transaction->paymentWay ? $transaction->paymentWay->name : '',
                'amount' => $transaction->amount,
                'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
            ]);
    }

    private function getLastReceiveTransactions(Carbon $startDate, Carbon $endDate, int $value)
    {
        return Transaction::where('type', 'receive')
            ->with(['client', 'paymentWay'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('created_at')
            ->take($value)
            ->get()
            ->map(fn ($transaction) => [
                'id' => $transaction->id,
                'client_name' => $transaction->client ? $transaction->client->name : '',
                'payment_way' => $transaction->paymentWay ? $transaction->paymentWay->name : '',
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
