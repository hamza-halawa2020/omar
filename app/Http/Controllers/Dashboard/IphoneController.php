<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Iphone\StoreIphoneRequest;
use App\Http\Requests\Iphone\StoreIphoneLogRequest;
use App\Http\Requests\Iphone\UpdateIphoneRequest;
use App\Http\Resources\IphoneLogResource;
use App\Http\Resources\IphoneResource;
use App\Models\Client;
use App\Models\PaymentWay;
use App\Services\IphoneService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class IphoneController extends BaseController
{
    public function __construct(private readonly IphoneService $iphoneService)
    {
        $this->middleware('check.permission:iphones_index')->only('index', 'list');
        $this->middleware('check.permission:iphones_store')->only('store');
        $this->middleware('check.permission:iphones_update')->only('update');
        $this->middleware('check.permission:iphones_destroy')->only('destroy');
        $this->middleware('check.permission:iphones_logs')->only('logs', 'storeLog');
    }

    public function index()
    {
        return view('dashboard.iphones.index', [
            'paymentWays' => PaymentWay::orderBy('position')->get(),
            'clients' => Client::orderBy('name')->get(),
        ]);
    }

    public function list(Request $request)
    {
        $iphones = $this->iphoneService->list($request);

        return response()->json([
            'status' => true,
            'message' => __('messages.iphones_fetched_successfully'),
            'data' => IphoneResource::collection($iphones),
        ]);
    }

    public function store(StoreIphoneRequest $request)
    {
        $iphone = $this->iphoneService->create($request->validated());

        return response()->json(['status' => true, 'message' => __('messages.iphone_created_successfully'), 'data' => new IphoneResource($iphone)], 201);
    }

    public function update(UpdateIphoneRequest $request, $id)
    {
        $iphone = $this->iphoneService->update((int) $id, $request->validated());

        return response()->json(['status' => true, 'message' => __('messages.iphone_updated_successfully'), 'data' => new IphoneResource($iphone)]);
    }

    public function destroy($id)
    {
        $this->iphoneService->delete((int) $id);

        return response()->json(['status' => true, 'message' => __('messages.iphone_deleted_successfully')]);
    }

    public function logs($id)
    {
        $logs = $this->iphoneService->logs((int) $id);

        return response()->json(['status' => true, 'message' => __('messages.iphone_logs_fetched_successfully'), 'data' => IphoneLogResource::collection($logs)]);
    }

    public function storeLog(StoreIphoneLogRequest $request, $id)
    {
        $log = $this->iphoneService->storeLog((int) $id, $request->validated());

        return response()->json(['status' => true, 'message' => __('messages.iphone_log_created_successfully'), 'data' => new IphoneLogResource($log)], 201);
    }
}
