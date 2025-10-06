<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Category;
use App\Models\Client;
use App\Models\Installment;
use App\Models\InstallmentContract;
use App\Models\InstallmentPayment;
use App\Models\PaymentWay;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends BaseController
{
    public function __construct()
    {
        // $this->middleware('check.permission:dashboard_index')->only('index');
    }

    public function index(){
        return view('dashboard.index');
    }

    /**
     * Handle analytics request and fetch all statistics.
     */
    public function analytics(Request $request)
    {

        // Handle date filtering
        $filterData = $this->getDateFilter($request);
        $value = $request->analytics_number ?? 5;
        
        // Fetch all statistics
        $statistics = [
            'top_clients_by_debt' => $this->getTopClientsByDebt($value),
            'top_clients_by_installments' => $this->getTopClientsByInstallments($value),
            'top_overdue_installments' => $this->getTopOverdueInstallments($value),
            'upcoming_installments' => $this->getUpcomingInstallments($value),
            'top_payment_ways_by_send' => $this->getTopPaymentWaysBySend($filterData['startDate'], $filterData['endDate'],$value),
            'top_payment_ways_by_receive' => $this->getTopPaymentWaysByReceive($filterData['startDate'], $filterData['endDate'],$value),
            'top_payment_ways_by_balance' => $this->getTopPaymentWaysByBalance($value),
            'top_payment_ways_nearing_send_limit' => $this->getTopPaymentWaysNearingSendLimit($value),
            'top_payment_ways_nearing_receive_limit' => $this->getTopPaymentWaysNearingReceiveLimit($value),
            'top_products_by_installments' => $this->getTopProductsByInstallments($value),
            'last_send_transactions' => $this->getLastSendTransactions($filterData['startDate'], $filterData['endDate'],$value),
            'last_receive_transactions' => $this->getLastReceiveTransactions($filterData['startDate'], $filterData['endDate'],$value),
            'total_revenue' => $this->getTotalRevenue($filterData['startDate'], $filterData['endDate']),
            'total_payment_ways_balance' => $this->getTotalPaymentWaysBalance(),
        ];

        // Return the data as JSON response
        return response()->json([
            'filter' => [
                'type' => $filterData['filterType'],
                'start_date' => $filterData['startDate']->format('Y-m-d'),
                'end_date' => $filterData['endDate']->format('Y-m-d'),
            ],
            'statistics' => $statistics,
        ]);
    }


    /**
     * Get total balance across all payment ways.
     * @return float
     */
    private function getTotalPaymentWaysBalance()
    {
        return PaymentWay::sum('balance');
    }

    /**
     * Get date filter based on request parameters.
     */
    private function getDateFilter(Request $request)
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
            // Default to today if invalid filter
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
        }

        return [
            'filterType' => $filterType,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];
    }

    /**
     * Get top $value clients with the highest debt.
     */
    private function getTopClientsByDebt($value)
    {
        return Client::whereDoesntHave('installmentContracts')->with('installmentContracts.installments')
            ->get()
            ->sortByDesc('debt')
            ->take($value)
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'total_remaining_amount' => $client->debt,
                ];
            })->values();
    }


    /**
     * Get top $value clients with the most installments.
     */
    private function getTopClientsByInstallments($value)
    {
        return Client::withCount('installmentContracts')
            ->orderByDesc('installment_contracts_count')
            ->take($value)
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'installment_count' => $client->installment_contracts_count,
                ];
            });
    }

    /**
     * Get top $value overdue installments.
     */
    private function getTopOverdueInstallments($value)
    {
        return Installment::where('status', 'late')
            ->with(['contract.client'])
            ->get()
            ->sortByDesc(function ($installment) {
                return $installment->required_amount - $installment->paid_amount;
            })
            ->take($value)
            ->map(function ($installment) {
                return [
                    'id' => $installment->id,
                    'client_name' => $installment->contract->client->name,
                    'due_date' => $installment->due_date->format('Y-m-d'),
                    'overdue_amount' => $installment->required_amount - $installment->paid_amount,
                ];
            })->values();
    }

    /**
     * Get top $value upcoming installments.
     */
    private function getUpcomingInstallments($value)
    {
        return Installment::where('status', 'pending')
            ->where('due_date', '>=', Carbon::today())
            ->with(['contract.client'])
            ->orderBy('due_date')
            ->take($value)
            ->get()
            ->map(function ($installment) {
                return [
                    'id' => $installment->id,
                    'client_name' => $installment->contract->client->name,
                    'due_date' => $installment->due_date->format('Y-m-d'),
                    'required_amount' => $installment->required_amount,
                ];
            });
    }

    /**
     * Get top $value payment ways by send transactions.
     */
    private function getTopPaymentWaysBySend($startDate, $endDate,$value)
    {
        return PaymentWay::withCount(['transactions' => function ($query) use ($startDate, $endDate) {
            $query->where('type', 'send')->whereBetween('created_at', [$startDate, $endDate]);
        }])
            ->orderByDesc('transactions_count')
            ->take($value)
            ->get()
            ->map(function ($paymentWay) {
                return [
                    'id' => $paymentWay->id,
                    'name' => $paymentWay->name,
                    'transaction_count' => $paymentWay->transactions_count,
                ];
            });
    }

    /**
     * Get top $value payment ways by receive transactions.
     */
    private function getTopPaymentWaysByReceive($startDate, $endDate,$value)
    {
        return PaymentWay::withCount(['transactions' => function ($query) use ($startDate, $endDate) {
            $query->where('type', 'receive')->whereBetween('created_at', [$startDate, $endDate]);
        }])
            ->orderByDesc('transactions_count')
            ->take($value)
            ->get()
            ->map(function ($paymentWay) {
                return [
                    'id' => $paymentWay->id,
                    'name' => $paymentWay->name,
                    'transaction_count' => $paymentWay->transactions_count,
                ];
            });
    }

    /**
     * Get top $value payment ways by balance.
     */
    private function getTopPaymentWaysByBalance($value)
    {
        return PaymentWay::orderByDesc('balance')
            ->take($value)
            ->get()
            ->map(function ($paymentWay) {
                return [
                    'id' => $paymentWay->id,
                    'name' => $paymentWay->name,
                    'balance' => $paymentWay->balance,
                ];
            });
    }

    /**
     * Get top $value payment ways nearing send limit.
     */
    private function getTopPaymentWaysNearingSendLimit($value)
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        return PaymentWay::whereNotNull('send_limit')
            ->with(['monthlyLimits' => function ($query) use ($currentYear, $currentMonth) {
                $query->where('year', $currentYear)
                      ->where('month', $currentMonth);
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

    /**
     * Get top $value payment ways nearing receive limit.
     */
    private function getTopPaymentWaysNearingReceiveLimit($value)
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        return PaymentWay::whereNotNull('receive_limit')
            ->with(['monthlyLimits' => function ($query) use ($currentYear, $currentMonth) {
                $query->where('year', $currentYear)
                      ->where('month', $currentMonth);
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

    /**
     * Get top $value products by installment contracts.
     */
    private function getTopProductsByInstallments($value)
    {
        return Product::withCount('installmentContracts')
            ->orderByDesc('installment_contracts_count')
            ->take($value)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'installment_contract_count' => $product->installment_contracts_count,
                ];
            });
    }

    /**
     * Get last $value send transactions.
     */
    private function getLastSendTransactions($startDate, $endDate,$value)
    {
        return Transaction::where('type', 'send')
            ->with(['client', 'paymentWay'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('created_at')
            ->take($value)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'client_name' => $transaction->client ? $transaction->client->name : '',
                    'payment_way' => $transaction->paymentWay ? $transaction->paymentWay->name : '',
                    'amount' => $transaction->amount,
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }

    /**
     * Get last $value receive transactions.
     */
    private function getLastReceiveTransactions($startDate, $endDate,$value)
    {
        return Transaction::where('type', 'receive')
            ->with(['client', 'paymentWay'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('created_at')
            ->take($value)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'client_name' => $transaction->client ? $transaction->client->name : '',
                    'payment_way' => $transaction->paymentWay ? $transaction->paymentWay->name : '',
                    'amount' => $transaction->amount,
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }

    /**
     * Get total revenue from receive transactions.
     */
    private function getTotalRevenue($startDate, $endDate)
    {
        return Transaction::where('type', 'receive')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
    }

    /**
     * Get total overdue amount across all installments.
     */

}