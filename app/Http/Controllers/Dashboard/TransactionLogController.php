<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Resources\TransactionLogResource;
use App\Services\TransactionLogService;
use Illuminate\Routing\Controller as BaseController;

class TransactionLogController extends BaseController
{
    public function __construct(private readonly TransactionLogService $transactionLogService)
    {
        $this->middleware('check.permission:transaction_logs_index')->only('index');
    }

    public function index()
    {
        $logs = $this->transactionLogService->list();

        return response()->json(['status' => true, 'message' => __('messages.transaction_logs_fetched_successfully'), 'data' => TransactionLogResource::collection($logs)]);
    }
}
