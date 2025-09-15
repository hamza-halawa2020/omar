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


    public function showList($id)
    {
        $paymentWay = PaymentWay::with(['category', 'subCategory', 'creator', 'transactions', 'logs'])->findOrFail($id);

        $timeFilter = request('time', 'today');
        $startDate = request('start_date');
        $endDate = request('end_date');
        $transactions = $paymentWay->transactions();

        if ($timeFilter === 'custom' && $startDate && $endDate) {
            try {
                $start = Carbon::parse($startDate)->startOfDay();
                $end = Carbon::parse($endDate)->endOfDay();
                $transactions->whereBetween('created_at', [$start, $end]);
            } catch (Exception $e) {
                return response()->json(['status' => false, 'message' => 'Invalid date format'], 400);
            }
        }

        $paymentWay->transactions = $transactions->get();

        return response()->json(['status' => true, 'message' => 'Payment way fetched successfully', 'data' => new PaymentWayResource($paymentWay)]);
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
