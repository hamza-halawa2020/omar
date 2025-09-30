<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstallmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'due_date' => $this->due_date->format('Y-m-d'),
            'required_amount' => $this->required_amount,
            'paid_amount' => $this->paid_amount,
            'status' => $this->status,
            'contract' => new InstallmentContractResource($this->whenLoaded('contract')),
            'payments' => InstallmentPaymentResource::collection($this->whenLoaded('payments')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
