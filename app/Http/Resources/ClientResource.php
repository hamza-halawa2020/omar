<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray($request)
    {

        $totalInstallments = $this->installmentContracts?->sum('remaining_amount') ?? 0;

        $originalDebt = $this->debt - $totalInstallments;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone_number' => $this->phone_number,
            'debt' => ceil($this->debt),
            // 'original_debt' => $originalDebt,
            'original_debt' => ceil($originalDebt),

            'totalInstallments' => ceil($totalInstallments),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'transactions' => TransactionResource::collection($this->whenLoaded('transactions')),
            'installment_contracts' => InstallmentContractResource::collection($this->whenLoaded('installmentContracts')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
