<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionLogResource;
use App\Models\TransactionLog;

class TransactionLogController extends Controller
{
    public function index()
    {
        $logs = TransactionLog::with(['transaction', 'creator'])->latest()->get();

        return response()->json(['status'  => true, 'message' => __('messages.transaction_logs_fetched_successfully'), 'data' => TransactionLogResource::collection($logs)]);
    }
}
