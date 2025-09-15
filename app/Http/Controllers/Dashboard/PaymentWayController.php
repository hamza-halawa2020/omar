<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentWay\StorePaymentWayRequest;
use App\Http\Requests\PaymentWay\UpdatePaymentWayRequest;
use App\Http\Resources\PaymentWayResource;
use App\Models\Category;
use App\Models\PaymentWay;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;

class PaymentWayController extends Controller
{

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

        $paymentWays = PaymentWay::with(['category', 'subCategory', 'creator', 'transactions', 'logs'])->get();

        return response()->json(['status'  => true, 'message' => 'Payment ways fetched successfully', 'data' => PaymentWayResource::collection($paymentWays)]);
    }

    public function store(StorePaymentWayRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();

        $paymentWay = PaymentWay::create($data);

        $paymentWay->logs()->create([
            'created_by' => Auth::id(),
            'action' => 'create',
            'data' => $paymentWay->toArray(),
        ]);

        return response()->json(['status'  => true, 'message' => 'Payment way created successfully', 'data' => new PaymentWayResource($paymentWay->load(['creator']))], 201);
    }


    public function show()
    {

        return view('dashboard.payment_ways.show');
    }


    // public function showList($id)
    // {
    //     $paymentWay = PaymentWay::with(['category', 'subCategory', 'creator', 'transactions', 'logs'])->findOrFail($id);

    //     $timeFilter = request('time', 'today');
    //     $startDate = request('start_date');
    //     $endDate = request('end_date');
    //     $transactions = $paymentWay->transactions();

    //     try {
    //         if ($timeFilter === 'custom' && $startDate && $endDate) {
    //             $start = Carbon::parse($startDate)->startOfDay();
    //             $end = Carbon::parse($endDate)->endOfDay();
    //             $transactions->whereBetween('created_at', [$start, $end]);
    //         } elseif ($timeFilter === 'today') {
    //             $start = Carbon::today()->startOfDay();
    //             $end = Carbon::today()->endOfDay();
    //             $transactions->whereBetween('created_at', [$start, $end]);
    //         }
    //     } catch (Exception $e) {
    //         return response()->json(['status' => false, 'message' => 'Invalid date format'], 400);
    //     }

    //     $paymentWay->transactions = $transactions->get();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Payment way fetched successfully',
    //         'data' => new PaymentWayResource($paymentWay)
    //     ]);
    // }





    public function showList($id)
    {
        $paymentWay = PaymentWay::with(['category', 'subCategory', 'creator', 'transactions', 'logs'])->findOrFail($id);

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
            return response()->json(['status' => false, 'message' => 'Invalid date format'], 400);
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

        return response()->json([
            'status' => true,
            'message' => 'Payment way fetched successfully',
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
            ]
        ]);
    }
    public function update(UpdatePaymentWayRequest $request, $id)
    {
        $paymentWay = PaymentWay::findOrFail($id);
        $paymentWay->update($request->validated());

        $paymentWay->logs()->create([
            'created_by' => Auth::id(),
            'action' => 'update',
            'data' => $paymentWay->toArray(),
        ]);



        return response()->json(['status'  => true, 'message' => 'Payment way updated successfully', 'data' => new PaymentWayResource($paymentWay->load(['creator']))]);
    }

    public function destroy($id)
    {
        $paymentWay = PaymentWay::findOrFail($id);

        if ($paymentWay->transactions()->exists()) {
            return response()->json(['status' => false, 'message' => 'Cannot delete this PAwment Way because it has transactions.'], 400);
        }

        $paymentWay->logs()->create([
            'created_by' => Auth::id(),
            'action' => 'delete',
            'data' => $paymentWay->toArray(),
        ]);

        $paymentWay->delete();

        return response()->json(['status'  => true, 'message' => 'Payment way deleted successfully']);
    }
}
