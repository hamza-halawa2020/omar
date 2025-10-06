<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\PaymentWay\StorePaymentWayRequest;
use App\Http\Requests\PaymentWay\UpdatePaymentWayRequest;
use App\Http\Resources\PaymentWayResource;
use App\Models\Category;
use App\Models\PaymentWay;
use Carbon\Carbon;
use Exception;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class PaymentWayController extends BaseController
{
    public function __construct()
    {
        $this->middleware('check.permission:payment_ways_index')->only('index', 'list');
        $this->middleware('check.permission:payment_ways_store')->only('store');
        $this->middleware('check.permission:payment_ways_show')->only('show', 'showList');
        $this->middleware('check.permission:payment_ways_update')->only('update');
        $this->middleware('check.permission:payment_ways_destroy')->only('destroy');
    }

    public function index()
    {
        $categories = Category::where('parent_id', null)->get();

        return view('dashboard.payment_ways.index', compact('categories'));
    }

    public function getSubCategoryOnCategory($id)
    {
        $subCategories = Category::where('parent_id', $id)->get();

        return response()->json($subCategories);
    }

    public function list()
    {
        $paymentWays = PaymentWay::with(['category', 'subCategory', 'creator', 'transactions', 'logs', 'monthlyLimits'])->latest()->get();

        return response()->json(['status' => true, 'message' => __('messages.payment_ways_fetched_successfully'), 'data' => PaymentWayResource::collection($paymentWays)]);
    }

    public function store(StorePaymentWayRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();

        $paymentWay = PaymentWay::create($data);

        if ($data['type'] == 'wallet') {

            $paymentWay->monthlyLimits()->create([
                'month' => now()->month,
                'year' => now()->year,
                'send_limit' => $paymentWay->send_limit,
                'receive_limit' => $paymentWay->receive_limit,
                'send_used' => 0,
                'receive_used' => 0,
            ]);
        }

        $paymentWay->logs()->create([
            'created_by' => Auth::id(),
            'action' => 'create',
            'data' => [
                'id' => $paymentWay->id,
                'name' => $paymentWay->name,
                'category' => optional($paymentWay->category)->name,
                'category_id' => $paymentWay->category_id,
                'sub_category' => optional($paymentWay->subCategory)->name,
                'sub_category_id' => $paymentWay->sub_category_id,
                'type' => $paymentWay->type,     // cash, wallet, balance_machine
                'phone_number' => $paymentWay->phone_number,
                'send_limit' => $paymentWay->send_limit,
                'receive_limit' => $paymentWay->receive_limit,
                'balance' => $paymentWay->balance,
                'creator' => optional($paymentWay->creator)->name,
                'created_by' => $paymentWay->created_by,
            ],

        ]);

        // event(new CreateBackup);

        return response()->json(['status' => true,     'message' => __('messages.payment_way_created_successfully'), 'data' => new PaymentWayResource($paymentWay->load(['creator']))], 201);
    }

    public function show()
    {

        return view('dashboard.payment_ways.show');
    }

    public function showList($id)
    {
        $paymentWay = PaymentWay::with(['category', 'subCategory', 'creator', 'transactions.client',  'transactions.installmentPayment', 'logs', 'monthlyLimits'])->findOrFail($id);

        $timeFilter = request('time', 'today');
        $startDate = request('start_date');
        $endDate = request('end_date');
        $transactions = $paymentWay->transactions();

        try {
            if ($timeFilter === 'custom' && $startDate && $endDate) {
                $start = Carbon::parse($startDate)->startOfDay();
                $end = Carbon::parse($endDate)->endOfDay();
                $transactions->whereBetween('created_at', [$start, $end]);
            } elseif ($timeFilter === 'today') {
                $start = Carbon::today()->startOfDay();
                $end = Carbon::today()->endOfDay();
                $transactions->whereBetween('created_at', [$start, $end]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => false,     'message' => __('messages.invalid_date_format')], 400);
        }

        $paymentWay->transactions = $transactions->get();

        // حساب الإحصائيات
        $receive_transactions = $paymentWay->transactions->where('type', 'receive');
        $send_transactions = $paymentWay->transactions->where('type', 'send');

        $receive_amount = $receive_transactions->sum('amount'); // إجمالي الاستلام بدون عمولة
        $receive_commission = $receive_transactions->sum('commission'); // إجمالي العمولات للاستلام
        $receive_total = $receive_amount + $receive_commission; // صافي الاستلام

        $send_amount = $send_transactions->sum('amount'); // إجمالي الإرسال بدون عمولة
        $send_commission = $send_transactions->sum('commission'); // إجمالي العمولات للإرسال
        $send_total = $send_amount + $send_commission; // التكلفة الكلية للإرسال

        $grand_net = $receive_total - $send_total; // الصافي الكلي (ربح/خسارة)

        $currentLimit = $paymentWay->monthlyLimits()
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->first();

        $limitStats = [
            'send_limit' => $currentLimit ? number_format($currentLimit->send_limit, 2, '.', '') : 0,
            'send_used' => $currentLimit ? number_format($currentLimit->send_used, 2, '.', '') : 0,
            'send_remaining' => $currentLimit ? number_format($currentLimit->send_limit - $currentLimit->send_used, 2, '.', '') : 0,
            'receive_limit' => $currentLimit ? number_format($currentLimit->receive_limit, 2, '.', '') : 0,
            'receive_used' => $currentLimit ? number_format($currentLimit->receive_used, 2, '.', '') : 0,
            'receive_remaining' => $currentLimit ? number_format($currentLimit->receive_limit - $currentLimit->receive_used, 2, '.', '') : 0,
        ];

        return response()->json([
            'status' => true,
            'message' => __('messages.payment_way_fetched_successfully'),
            'data' => new PaymentWayResource($paymentWay),
            'statistics' => [
                'receive' => [
                    'receive_amount' => number_format($receive_amount, 2, '.', ''), // بدون عمولة
                    'receive_commission' => number_format($receive_commission, 2, '.', ''), // العمولات
                    'receive_total' => number_format($receive_total, 2, '.', ''), // صافي الاستلام
                ],
                'send' => [
                    'send_amount' => number_format($send_amount, 2, '.', ''), // بدون عمولة
                    'send_commission' => number_format($send_commission, 2, '.', ''), // العمولات
                    'send_total' => number_format($send_total, 2, '.', ''), // التكلفة الكلية
                ],
                'grand_net' => number_format($grand_net, 2, '.', ''), // الصافي الكلي
                'limits' => $limitStats,
            ],
        ]);
    }

    public function update(UpdatePaymentWayRequest $request, $id)
    {
        $paymentWay = PaymentWay::findOrFail($id);

        $oldSendLimit = $paymentWay->send_limit;
        $oldReceiveLimit = $paymentWay->receive_limit;

        $paymentWay->update($request->validated());

        if ($oldSendLimit != $paymentWay->send_limit || $oldReceiveLimit != $paymentWay->receive_limit) {
            $currentLimit = $paymentWay->monthlyLimits()
                ->where('month', now()->month)
                ->where('year', now()->year)
                ->first();

            if ($currentLimit) {
                $currentLimit->update([
                    'send_limit' => $paymentWay->send_limit,
                    'receive_limit' => $paymentWay->receive_limit,
                ]);
            } else {
                $paymentWay->monthlyLimits()->create([
                    'month' => now()->month,
                    'year' => now()->year,
                    'send_limit' => $paymentWay->send_limit,
                    'receive_limit' => $paymentWay->receive_limit,
                    'send_used' => 0,
                    'receive_used' => 0,
                ]);
            }
        }

        $paymentWay->logs()->create([
            'created_by' => Auth::id(),
            'action' => 'update',
            'data' => [
                'id' => $paymentWay->id,
                'name' => $paymentWay->name,
                'category' => optional($paymentWay->category)->name,
                'category_id' => $paymentWay->category_id,
                'sub_category' => optional($paymentWay->subCategory)->name,
                'sub_category_id' => $paymentWay->sub_category_id,
                'type' => $paymentWay->type,     // cash, wallet, balance_machine
                'phone_number' => $paymentWay->phone_number,
                'send_limit' => $paymentWay->send_limit,
                'receive_limit' => $paymentWay->receive_limit,
                'balance' => $paymentWay->balance,
                'creator' => optional($paymentWay->creator)->name,
                'created_by' => $paymentWay->created_by,
            ],

        ]);

        // event(new CreateBackup);

        return response()->json(['status' => true,    'message' => __('messages.payment_way_updated_successfully'), 'data' => new PaymentWayResource($paymentWay->load(['creator']))]);
    }

    public function destroy($id)
    {
        $paymentWay = PaymentWay::findOrFail($id);

        if ($paymentWay->transactions()->exists()) {
            return response()->json(['status' => false,     'message' => __('messages.cannot_delete_payment_way_has_transactions')], 400);
        }

        $paymentWay->logs()->create([
            'created_by' => Auth::id(),
            'action' => 'delete',
            'data' => [
                'id' => $paymentWay->id,
                'name' => $paymentWay->name,
                'category' => optional($paymentWay->category)->name,
                'category_id' => $paymentWay->category_id,
                'sub_category' => optional($paymentWay->subCategory)->name,
                'sub_category_id' => $paymentWay->sub_category_id,
                'type' => $paymentWay->type,     // cash, wallet, balance_machine
                'phone_number' => $paymentWay->phone_number,
                'send_limit' => $paymentWay->send_limit,
                'receive_limit' => $paymentWay->receive_limit,
                'balance' => $paymentWay->balance,
                'creator' => optional($paymentWay->creator)->name,
                'created_by' => $paymentWay->created_by,
            ],

        ]);

        $paymentWay->delete();

        // event(new CreateBackup);

        return response()->json(['status' => true,     'message' => __('messages.payment_way_deleted_successfully')]);
    }
}
