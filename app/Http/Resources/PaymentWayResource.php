<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentWayResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $currentLimit = $this->monthlyLimits
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->first();

        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'sub_category_id' => $this->sub_category_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'subCategory' => new CategoryResource($this->whenLoaded('subCategory')),
            'name' => $this->name,
            'type' => $this->type,
            'phone_number' => $this->phone_number,
            'receive_limit' => $this->receive_limit,
            'receive_limit_alert' => $this->receive_limit_alert,
            'send_limit' => $this->send_limit,
            'send_limit_alert' => $this->send_limit_alert,
            'balance' => $this->balance,
            'creator' => new UserResource($this->whenLoaded('creator')),
            'transactions' => TransactionResource::collection($this->whenLoaded('transactions')),
            'logs' => PaymentWayLogResource::collection($this->whenLoaded('logs')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            'monthly_limits' => [ 
                'send_limit' => $currentLimit ? number_format($currentLimit->send_limit, 2, '.', '') : number_format($this->send_limit ?? 0, 2, '.', ''),
                'send_used' => $currentLimit ? number_format($currentLimit->send_used, 2, '.', '') : '0.00',
                'send_remaining' => $currentLimit ? number_format($currentLimit->send_limit - $currentLimit->send_used, 2, '.', '') : number_format($this->send_limit ?? 0, 2, '.', ''),
                'receive_limit' => $currentLimit ? number_format($currentLimit->receive_limit, 2, '.', '') : number_format($this->receive_limit ?? 0, 2, '.', ''),
                'receive_used' => $currentLimit ? number_format($currentLimit->receive_used, 2, '.', '') : '0.00',
                'receive_remaining' => $currentLimit ? number_format($currentLimit->receive_limit - $currentLimit->receive_used, 2, '.', '') : number_format($this->receive_limit ?? 0, 2, '.', ''),
                'month' => $currentMonth,
                'year' => $currentYear,
                'month_name' => now()->monthName, 
            ],
        ];
    }
}
