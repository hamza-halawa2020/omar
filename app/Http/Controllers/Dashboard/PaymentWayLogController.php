<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Resources\PaymentWayLogResource;
use App\Services\PaymentWayLogService;
use Illuminate\Routing\Controller as BaseController;

class PaymentWayLogController extends BaseController
{
    public function __construct(private readonly PaymentWayLogService $paymentWayLogService)
    {
        $this->middleware('check.permission:payment_way_logs_index')->only('index');
    }

    public function index()
    {
        $logs = $this->paymentWayLogService->list();

        return response()->json(['status' => true, 'message' => __('messages.payment_way_logs_fetched_successfully'), 'data' => PaymentWayLogResource::collection($logs)]);
    }
}
