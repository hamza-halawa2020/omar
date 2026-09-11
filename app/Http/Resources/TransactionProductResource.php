<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $canViewPurchasePrices = $request->user()?->can('purchase_prices_view') ?? false;
        $transactionType = $this->whenLoaded('transaction', fn () => $this->transaction?->type);
        $isPurchaseLine = $transactionType === 'send';

        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'unit_price' => $this->when(! $isPurchaseLine || $canViewPurchasePrices, round($this->unit_price, 2)),
            'total' => round($this->total, 2),
            'cost_total' => $this->when($canViewPurchasePrices, round($this->cost_total, 2)),
            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
