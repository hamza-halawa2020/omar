<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\PaymentWay\StorePaymentWayRequest;
use App\Http\Requests\PaymentWay\UpdatePaymentWayRequest;
use App\Http\Resources\PaymentWayResource;
use App\Services\PaymentWayService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class PaymentWayController extends BaseController
{
    public function __construct(private readonly PaymentWayService $paymentWayService)
    {
        $this->middleware('check.permission:payment_ways_index')->only('index', 'list');
        $this->middleware('check.permission:payment_ways_store')->only('store');
        $this->middleware('check.permission:payment_ways_show')->only('show', 'showList');
        $this->middleware('check.permission:payment_ways_update')->only('update');
        $this->middleware('check.permission:payment_ways_destroy')->only('destroy');
        $this->middleware('check.permission:payment_ways_reorder')->only('reorder');
    }

    public function index()
    {
        return view('dashboard.payment_ways.index', $this->paymentWayService->indexData());
    }

    public function getSubCategoryOnCategory($id)
    {
        return response()->json($this->paymentWayService->getSubCategories((int) $id));
    }

    public function list()
    {
        $paymentWays = $this->paymentWayService->list();

        return response()->json(['status' => true, 'message' => __('messages.payment_ways_fetched_successfully'), 'data' => PaymentWayResource::collection($paymentWays)]);
    }

    public function store(StorePaymentWayRequest $request)
    {
        $paymentWay = $this->paymentWayService->store($request->validated());

        return response()->json(['status' => true, 'message' => __('messages.payment_way_created_successfully'), 'data' => new PaymentWayResource($paymentWay)], 201);
    }

    public function show()
    {
        return view('dashboard.payment_ways.show');
    }

    public function showList($id)
    {
        $result = $this->paymentWayService->showList((int) $id, (string) request('time', 'today'), request('start_date'), request('end_date'));

        return response()->json(['status' => true,'message' => __('messages.payment_way_fetched_successfully'),'data' => new PaymentWayResource($result['paymentWay']),'statistics' => $result['statistics']]);
    }

    public function update(UpdatePaymentWayRequest $request, $id)
    {
        $paymentWay = $this->paymentWayService->update((int) $id, $request->validated());

        return response()->json(['status' => true, 'message' => __('messages.payment_way_updated_successfully'), 'data' => new PaymentWayResource($paymentWay)]);
    }

    public function destroy($id)
    {
        $this->paymentWayService->destroy((int) $id);

        return response()->json(['status' => true, 'message' => __('messages.payment_way_deleted_successfully')]);
    }

    public function reorder(Request $request)
    {
        if (! $request->user()?->hasRole('Super admin')) {
            return response()->json(['status' => false, 'message' => __('messages.unauthorized')], 403);
        }

        if ($this->isMobileRequest($request)) {
            return response()->json(['status' => false, 'message' => __('messages.reorder_desktop_only')], 403);
        }

        $this->paymentWayService->reorder((array) $request->input('order', []));

        return response()->json(['status' => true, 'message' => 'Order updated successfully']);
    }

    private function isMobileRequest(Request $request): bool
    {
        $userAgent = (string) $request->userAgent();

        return (bool) preg_match('/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|Mobile/i', $userAgent);
    }
}
