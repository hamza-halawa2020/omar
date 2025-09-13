<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentWayLog\StorePaymentWayLogRequest;
use App\Http\Resources\PaymentWayLogResource;
use App\Models\PaymentWayLog;
use Illuminate\Support\Facades\Auth;

class PaymentWayLogController extends Controller
{
    public function index()
    {
        $logs = PaymentWayLog::with(['paymentWay', 'creator'])->latest()->get();

        return response()->json(['status'  => true, 'message' => 'Payment way logs fetched successfully', 'data'    => PaymentWayLogResource::collection($logs),]);
    }

}
