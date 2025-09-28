<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstallmentContractResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'total_amount' => $this->total_amount,
            'installment_count' => $this->installment_count,
            'installment_amount' => $this->installment_amount,
            'down_payment' => $this->down_payment,
            'down_payment_percentage' => $this->down_payment_percentage,
            'start_date' => $this->start_date,
            'client' => new ClientResource($this->whenLoaded('client')),
            'product' => new ProductResource($this->whenLoaded('product')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'installments' => InstallmentResource::collection($this->whenLoaded('installments')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
