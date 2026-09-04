<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Requests\Transaction\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Services\TransactionService;
use Illuminate\Routing\Controller as BaseController;

class TransactionController extends BaseController
{
    public function __construct(private readonly TransactionService $transactionService)
    {
        $this->middleware('check.permission:transactions_index')->only(['list', 'index']);
        $this->middleware('check.permission:transactions_store')->only('store');
        $this->middleware('check.permission:transactions_show')->only('show');
        $this->middleware('check.permission:transactions_update')->only('update');
    }

    public function index()
    {
        $data = $this->transactionService->paginatedForIndex(request());

        return view('dashboard.transactions.index', $data);
    }

    public function list()
    {
        $transactions = $this->transactionService->list();

        return response()->json(['status' => true,'message' => __('messages.transactions_fetched_successfully'),'data' => TransactionResource::collection($transactions)]);
    }

    public function store(StoreTransactionRequest $request)
    {
        $transaction = $this->transactionService->store($request->validated(), $request);
        $whatsapp = $transaction->getAttribute('whatsapp');
        $message = __('messages.transaction_created_successfully');

        if (is_array($whatsapp)) {
            if (($whatsapp['success'] ?? false) || ($whatsapp['sent'] ?? false)) {
                $message .= ' - ' . __('messages.whatsapp_message_sent');
            } else {
                $message .= ' - ' . __('messages.whatsapp_message_not_sent') . ': ' . $this->translatedWhatsAppError($whatsapp['error'] ?? null);
            }

            if (array_key_exists('remainingPoints', $whatsapp)) {
                $message .= ' - ' . __('messages.whatsapp_points_balance', ['points' => $whatsapp['remainingPoints']]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => new TransactionResource($transaction),
            'whatsapp' => $whatsapp,
        ], 201);
    }

    public function show($id)
    {
        $transaction = $this->transactionService->show((int) $id);

        return response()->json(['status' => true,'message' => __('messages.transaction_fetched_successfully'),'data' => new TransactionResource($transaction)]);
    }

    public function update(UpdateTransactionRequest $request, $id)
    {
        $transaction = $this->transactionService->update((int) $id, $request->validated(), $request);

        return response()->json(['status' => true,'message' => __('messages.transaction_updated_successfully'),'data' => new TransactionResource($transaction)]);
    }

    private function translatedWhatsAppError(?string $error): string
    {
        if (! $error) {
            return __('messages.whatsapp_error_unknown');
        }

        if ($error === 'Insufficient wallet points') {
            return __('messages.whatsapp_error_insufficient_points');
        }

        if (preg_match('/Daily message limit reached \((\d+) messages\)\./', $error, $matches)) {
            return __('messages.whatsapp_error_daily_limit', ['limit' => $matches[1]]);
        }

        return $error;
    }
}
