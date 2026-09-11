<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = $this->data;

        if (! ($request->user()?->can('purchase_prices_view') ?? false)) {
            $data = $this->withoutPurchasePriceData($data);
        }

        return [
            'id' => $this->id,
            'action' => $this->action,
            'data' => $data,
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

    private function withoutPurchasePriceData($data)
    {
        if (! is_array($data)) {
            return $data;
        }

        if (isset($data['products']) && is_array($data['products'])) {
            $data['products'] = array_map(function ($product) {
                if (is_array($product)) {
                    unset($product['unit_price'], $product['cost_total'], $product['unit_cost']);
                }

                return $product;
            }, $data['products']);
        }

        foreach (['old_data', 'new_data'] as $key) {
            if (isset($data[$key]) && is_array($data[$key])) {
                unset($data[$key]['purchase_price'], $data[$key]['cost_total'], $data[$key]['unit_cost']);
            }
        }

        return $data;
    }
}
