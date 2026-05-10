<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'action' => $this->action,
            'data' => $this->data,
            'has_payment_way_change' => $this->has_payment_way_change,
            'old_payment_way_id' => $this->old_payment_way_id,
            'new_payment_way_id' => $this->new_payment_way_id,
            'old_payment_way_name' => $this->old_payment_way_name,
            'new_payment_way_name' => $this->new_payment_way_name,
            'transaction' => new TransactionResource($this->whenLoaded('transaction')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
