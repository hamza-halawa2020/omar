<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IphoneLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'iphone_id' => $this->iphone_id,
            'transaction_id' => $this->transaction_id,
            'payment_way_id' => $this->payment_way_id,
            'client_id' => $this->client_id,
            'action_type' => $this->action_type,
            'amount' => $this->amount,
            'notes' => $this->notes,
            'payment_way' => new PaymentWayResource($this->whenLoaded('paymentWay')),
            'client' => new ClientResource($this->whenLoaded('client')),
            'transaction' => new TransactionResource($this->whenLoaded('transaction')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
