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
        // Uncomment middleware if you want to restrict access
        // $this->middleware('check.permission:dashboard_index')->only('index');
    }

    public function index(){
        return view('dashboard.index');
    }

    public function analytics(Request $request)
    {
        // Handle date filtering
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

        // 1. Top 5 clients with the highest debt
        $topClientsByDebt = Client::with('installmentContracts.installments')
            ->get()
            ->sortByDesc('total_remaining_amount')
            ->take(5)
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'total_remaining_amount' => $client->total_remaining_amount,
                ];
            })->values();

        // 2. Top 5 clients by number of transactions (send)
        $topClientsBySendTransactions = Client::withCount(['transactions' => function ($query) use ($startDate, $endDate) {
            $query->where('type', 'send')->whereBetween('created_at', [$startDate, $endDate]);
        }])
            ->orderByDesc('transactions_count')
            ->take(5)
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'transaction_count' => $client->transactions_count,
                ];
            });

        // 3. Top 5 clients by number of transactions (receive)
        $topClientsByReceiveTransactions = Client::withCount(['transactions' => function ($query) use ($startDate, $endDate) {
            $query->where('type', 'receive')->whereBetween('created_at', [$startDate, $endDate]);
        }])
            ->orderByDesc('transactions_count')
            ->take(5)
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'transaction_count' => $client->transactions_count,
                ];
            });

        // 4. Top 5 clients with the most installments
        $topClientsByInstallments = Client::withCount('installmentContracts')
            ->orderByDesc('installment_contracts_count')
            ->take(5)
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'installment_count' => $client->installment_contracts_count,
                ];
            });

        // 5. Top 5 installments with the highest overdue amount
        $topOverdueInstallments = Installment::where('status', 'late')
            ->with(['contract.client'])
            ->get()
            ->sortByDesc(function ($installment) {
                return $installment->required_amount - $installment->paid_amount;
            })
            ->take(5)
            ->map(function ($installment) {
                return [
                    'id' => $installment->id,
                    'client_name' => $installment->contract->client->name,
                    'due_date' => $installment->due_date->format('Y-m-d'),
                    'overdue_amount' => $installment->required_amount - $installment->paid_amount,
                ];
            })->values();

        // 6. Top 5 upcoming installment due dates
        $upcomingInstallments = Installment::where('status', 'pending')
            ->where('due_date', '>=', Carbon::today())
            ->with(['contract.client'])
            ->orderBy('due_date')
            ->take(5)
            ->get()
            ->map(function ($installment) {
                return [
                    'id' => $installment->id,
                    'client_name' => $installment->contract->client->name,
                    'due_date' => $installment->due_date->format('Y-m-d'),
                    'required_amount' => $installment->required_amount,
                ];
            });

        // 7. Top 5 payment ways by number of transactions (send)
        $topPaymentWaysBySend = PaymentWay::withCount(['transactions' => function ($query) use ($startDate, $endDate) {
            $query->where('type', 'send')->whereBetween('created_at', [$startDate, $endDate]);
        }])
            ->orderByDesc('transactions_count')
            ->take(5)
            ->get()
            ->map(function ($paymentWay) {
                return [
                    'id' => $paymentWay->id,
                    'name' => $paymentWay->name,
                    'transaction_count' => $paymentWay->transactions_count,
                ];
            });

        // 8. Top 5 payment ways by number of transactions (receive)
        $topPaymentWaysByReceive = PaymentWay::withCount(['transactions' => function ($query) use ($startDate, $endDate) {
            $query->where('type', 'receive')->whereBetween('created_at', [$startDate, $endDate]);
        }])
            ->orderByDesc('transactions_count')
            ->take(5)
            ->get()
            ->map(function ($paymentWay) {
                return [
                    'id' => $paymentWay->id,
                    'name' => $paymentWay->name,
                    'transaction_count' => $paymentWay->transactions_count,
                ];
            });

        // 9. Top 5 payment ways by balance
        $topPaymentWaysByBalance = PaymentWay::orderByDesc('balance')
            ->take(5)
            ->get()
            ->map(function ($paymentWay) {
                return [
                    'id' => $paymentWay->id,
                    'name' => $paymentWay->name,
                    'balance' => $paymentWay->balance,
                ];
            });

        // 10. Top 5 payment ways nearing send limit
        $topPaymentWaysNearingSendLimit = PaymentWay::whereNotNull('send_limit')
            ->with(['monthlyLimits' => function ($query) use ($startDate, $endDate) {
                $query->where('year', Carbon::now()->year)
                      ->where('month', Carbon::now()->month);
            }])
            ->get()
            ->sortByDesc(function ($paymentWay) {
                $monthlyLimit = $paymentWay->monthlyLimits->first();
                return $monthlyLimit ? ($monthlyLimit->send_used / $paymentWay->send_limit) : 0;
            })
            ->take(5)
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

        // 11. Top 5 payment ways nearing receive limit
        $topPaymentWaysNearingReceiveLimit = PaymentWay::whereNotNull('receive_limit')
            ->with(['monthlyLimits' => function ($query) use ($startDate, $endDate) {
                $query->where('year', Carbon::now()->year)
                      ->where('month', Carbon::now()->month);
            }])
            ->get()
            ->sortByDesc(function ($paymentWay) {
                $monthlyLimit = $paymentWay->monthlyLimits->first();
                return $monthlyLimit ? ($monthlyLimit->receive_used / $paymentWay->receive_limit) : 0;
            })
            ->take(5)
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

        // 12. Top 5 products sold via installment contracts
        $topProductsByInstallments = Product::withCount('installmentContracts')
            ->orderByDesc('installment_contracts_count')
            ->take(5)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'installment_contract_count' => $product->installment_contracts_count,
                ];
            });

        // 13. Last 5 transactions (send)
        $lastSendTransactions = Transaction::where('type', 'send')
            ->with(['client', 'paymentWay'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'client_name' => $transaction->client ? $transaction->client->name : 'N/A',
                    'payment_way' => $transaction->paymentWay ? $transaction->paymentWay->name : 'N/A',
                    'amount' => $transaction->amount,
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                ];
            });

        // 14. Last 5 transactions (receive)
        $lastReceiveTransactions = Transaction::where('type', 'receive')
            ->with(['client', 'paymentWay'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'client_name' => $transaction->client ? $transaction->client->name : 'N/A',
                    'payment_way' => $transaction->paymentWay ? $transaction->paymentWay->name : 'N/A',
                    'amount' => $transaction->amount,
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                ];
            });

        // Additional statistic: Total revenue from transactions in the period
        $totalRevenue = Transaction::where('type', 'receive')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        // Additional statistic: Total overdue amount across all installments
        $totalOverdueAmount = Installment::where('status', 'late')
            ->get()
            ->sum(function ($installment) {
                return $installment->required_amount - $installment->paid_amount;
            });

        // Return the data as JSON response
        return response()->json([
            'filter' => [
                'type' => $filterType,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'statistics' => [
                'top_clients_by_debt' => $topClientsByDebt,
                'top_clients_by_send_transactions' => $topClientsBySendTransactions,
                'top_clients_by_receive_transactions' => $topClientsByReceiveTransactions,
                'top_clients_by_installments' => $topClientsByInstallments,
                'top_overdue_installments' => $topOverdueInstallments,
                'upcoming_installments' => $upcomingInstallments,
                'top_payment_ways_by_send' => $topPaymentWaysBySend,
                'top_payment_ways_by_receive' => $topPaymentWaysByReceive,
                'top_payment_ways_by_balance' => $topPaymentWaysByBalance,
                'top_payment_ways_nearing_send_limit' => $topPaymentWaysNearingSendLimit,
                'top_payment_ways_nearing_receive_limit' => $topPaymentWaysNearingReceiveLimit,
                'top_products_by_installments' => $topProductsByInstallments,
                'last_send_transactions' => $lastSendTransactions,
                'last_receive_transactions' => $lastReceiveTransactions,
                'total_revenue' => $totalRevenue,
                'total_overdue_amount' => $totalOverdueAmount,
            ],
        ]);
    }
}