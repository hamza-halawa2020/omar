<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentWayLogResource;
use App\Models\PaymentWayLog;

class PaymentWayLogController extends Controller
{
    public function index()
    {
        $logs = PaymentWayLog::with(['paymentWay', 'creator'])->latest()->get();

        return response()->json(['status'  => true, 'message' => __('messages.payment_way_logs_fetched_successfully'), 'data'    => PaymentWayLogResource::collection($logs),]);
    }
}
