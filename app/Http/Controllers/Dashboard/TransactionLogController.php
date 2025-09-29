<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Resources\TransactionLogResource;
use App\Models\TransactionLog;
use Illuminate\Routing\Controller as BaseController;

class TransactionLogController extends BaseController
{
    public function __construct()
    {
        $this->middleware('check.permission:transaction_logs_index')->only('index');
    }

    public function index()
    {
        $logs = TransactionLog::with(['transaction', 'creator'])->latest()->get();

        return response()->json(['status' => true, 'message' => __('messages.transaction_logs_fetched_successfully'), 'data' => TransactionLogResource::collection($logs)]);
    }
}
