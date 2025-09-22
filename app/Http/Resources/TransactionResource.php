<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'type'        => $this->type,
            'amount'      => $this->amount,
            'commission'  => $this->commission,
            'notes'       => $this->notes,
            'attachment'  => $this->attachment,
            'paymentWay'  => new PaymentWayResource($this->whenLoaded('paymentWay')),
            'creator'     => new UserResource($this->creator),
            'logs'        => TransactionLogResource::collection($this->whenLoaded('logs')),
            'created_at'  => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'  => $this->updated_at?->format('Y-m-d H:i:s'),
            'client' => [
                'id' => $this->client?->id,
                'name' => $this->client?->name,
                'debt' => $this->client?->debt,
            ],
        ];
    }
}
