<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstallmentPaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'payment_date' => $this->payment_date->format('Y-m-d'),
            'paid_by' => new UserResource($this->whenLoaded('paid_by')),
            'installment' => new InstallmentResource($this->whenLoaded('installment')),
            'transaction' => new TransactionResource($this->whenLoaded('transaction')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
