<?php

namespace App\Services;

use App\Models\Iphone;
use App\Models\IphoneLog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class IphoneService
{
    public function __construct(private readonly TransactionService $transactionService)
    {
    }

    public function list(Request $request): Collection
    {
        return Iphone::with(['creator', 'logs.client'])
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('device_type', 'like', '%' . $request->search . '%')
                        ->orWhere('device_details', 'like', '%' . $request->search . '%')
                        ->orWhere('currency', 'like', '%' . $request->search . '%');
                });
            })
            ->latest()
            ->get();
    }

    public function create(array $data): Iphone
    {
        $data['created_by'] = Auth::id();
        $data = $this->calculateTotals($data);

        return Iphone::create($data);
    }

    public function update(int $id, array $data): Iphone
    {
        $iphone = Iphone::findOrFail($id);
        $data = $this->calculateTotals($data);
        $iphone->update($data);

        return $iphone;
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $iphone = Iphone::findOrFail($id);

            if ($iphone->logs()->exists()) {
                throw new HttpResponseException(response()->json(['status' => false, 'message' => __('messages.cannot_delete_iphone_with_logs')], 400));
            }

            $iphone->delete();
        });
    }

    public function logs(int $id): Collection
    {
        return Iphone::findOrFail($id)
            ->logs()
            ->with(['creator', 'paymentWay', 'client', 'transaction'])
            ->get();
    }

    public function storeLog(int $id, array $data): IphoneLog
    {
        return DB::transaction(function () use ($id, $data) {
            $iphone = Iphone::findOrFail($id);
            $this->guardValidAction($iphone, $data);

            $transaction = $this->transactionService->store([
                'payment_way_id' => $data['payment_way_id'],
                'client_id' => $data['client_id'] ?? null,
                'product_id' => null,
                'quantity' => 1,
                'type' => $this->transactionTypeForAction($data['action_type']),
                'amount' => $data['amount'],
                'commission' => 0,
                'notes' => $this->buildTransactionNotes($iphone, $data),
            ], new Request());

            $log = $iphone->logs()->create([
                'transaction_id' => $transaction->id,
                'payment_way_id' => $data['payment_way_id'],
                'client_id' => $data['client_id'] ?? null,
                'action_type' => $data['action_type'],
                'amount' => $data['amount'],
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $this->syncStatusFromLog($iphone, $data['action_type']);
            $this->syncSalePriceFromLog($iphone, $data['action_type'], (float) $data['amount']);

            return $log->load(['creator', 'paymentWay', 'client', 'transaction']);
        });
    }

    private function calculateTotals(array $data): array
    {
        $purchaseEgp = (float) ($data['purchase_price_egp'] ?? 0);
        $extraExpenses = (float) ($data['extra_expenses'] ?? 0);
        $totalPurchase = $purchaseEgp + $extraExpenses;

        $data['total_purchase_with_expenses'] = $totalPurchase;
        $data['net_profit_after_sale'] = array_key_exists('sale_price_egp', $data) && $data['sale_price_egp'] !== null && $data['sale_price_egp'] !== ''
            ? (float) $data['sale_price_egp'] - $totalPurchase
            : null;

        return $data;
    }

    private function syncStatusFromLog(Iphone $iphone, string $actionType): void
    {
        $status = match ($actionType) {
            'sale' => 'sold',
            'return' => 'returned',
            'maintenance' => 'maintenance',
            default => $iphone->status ?? 'available',
        };

        $iphone->update(['status' => $status]);
    }

    private function syncSalePriceFromLog(Iphone $iphone, string $actionType, float $amount): void
    {
        if ($actionType !== 'sale') {
            return;
        }

        $iphone->sale_price_egp = $amount;
        $iphone->net_profit_after_sale = $amount - (float) $iphone->total_purchase_with_expenses;
        $iphone->save();
    }

    private function transactionTypeForAction(string $actionType): string
    {
        return $actionType === 'sale' ? 'receive' : 'send';
    }

    private function guardValidAction(Iphone $iphone, array $data): void
    {
        $actionType = $data['action_type'];

        if ($actionType === 'sale' && $iphone->status === 'sold') {
            throw new HttpResponseException(response()->json(['status' => false, 'message' => __('messages.iphone_already_sold')], 400));
        }

        if ($actionType === 'return' && ! $iphone->logs()->where('action_type', 'sale')->exists()) {
            throw new HttpResponseException(response()->json(['status' => false, 'message' => __('messages.iphone_return_requires_sale')], 400));
        }

        $saleLog = $iphone->logs()
            ->where('action_type', 'sale')
            ->whereNotNull('client_id')
            ->oldest()
            ->first();

        if (! $saleLog || $actionType === 'sale') {
            return;
        }

        if ($actionType === 'return' && empty($data['client_id'])) {
            throw new HttpResponseException(response()->json(['status' => false, 'message' => __('messages.iphone_action_requires_sale_client')], 400));
        }

        if (! empty($data['client_id']) && (int) $data['client_id'] !== (int) $saleLog->client_id) {
            throw new HttpResponseException(response()->json(['status' => false, 'message' => __('messages.iphone_action_must_use_sale_client')], 400));
        }
    }

    private function buildTransactionNotes(Iphone $iphone, array $data): string
    {
        $action = __('messages.iphone_action_' . $data['action_type']);
        $notes = $data['notes'] ?? '';

        return trim("{$action} - {$iphone->device_type} #{$iphone->id} {$notes}");
    }
}
