<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentWayResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'category_id'      => $this->category_id,
            'sub_category_id'      => $this->sub_category_id,
            'category'    => new CategoryResource($this->whenLoaded('category')),
            'subCategory' => new CategoryResource($this->whenLoaded('subCategory')),
            'name'         => $this->name,
            'type'         => $this->type,
            'phone_number' => $this->phone_number,
            'receive_limit' => $this->receive_limit,
            'receive_limit_alert' => $this->receive_limit_alert,
            'send_limit' => $this->send_limit,
            'send_limit_alert' => $this->send_limit_alert,
            'balance'      => $this->balance,
            'creator'      => new UserResource($this->whenLoaded('creator')),
            'transactions' => TransactionResource::collection($this->whenLoaded('transactions')),
            'logs'         => PaymentWayLogResource::collection($this->whenLoaded('logs')),
            'created_at'   => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'   => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
