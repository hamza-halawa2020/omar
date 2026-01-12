<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'amount' => round($this->amount),
            'commission' => round($this->commission),
            'notes' => $this->notes,
            'attachment' => $this->attachment,
            'balance_before_transaction' => round($this->balance_before_transaction),
            'balance_after_transaction' => round($this->balance_after_transaction),
            'debt_before' => $this->debtLog ? $this->debtLog->debt_before : null,
            'debt_after' => $this->debtLog ? $this->debtLog->debt_after : null,
            'client_id' => $this->client_id,
            'product_id' => $this->product_id,
            'payment_way_id' => $this->payment_way_id,
            'quantity' => $this->quantity,
            'paymentWay' => new PaymentWayResource($this->whenLoaded('paymentWay')),
            'creator' => new UserResource($this->creator),
            'logs' => TransactionLogResource::collection($this->whenLoaded('logs')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'client' => new ClientResource($this->whenLoaded('client')),
            'product' => new ProductResource($this->whenLoaded('product')),
            // 'installmentPayment' => new InstallmentPaymentResource($this->whenLoaded('installmentPayment')),
            'installmentPayment' => InstallmentPaymentResource::collection($this->whenLoaded('installmentPayment')),

        ];
    }
}
