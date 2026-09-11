<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $canViewPurchasePrices = $request->user()?->can('purchase_prices_view') ?? false;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'image' => $this->image,
            'description' => $this->description,
            'purchase_price' => $this->when($canViewPurchasePrices, (float) $this->purchase_price),
            'sale_price' => (float) $this->sale_price,
            'stock' => $this->stock,
            'purchase_batches' => $this->whenLoaded('purchaseBatches', fn () => $this->purchaseBatches->map(fn ($batch) => [
                'id' => $batch->id,
                'remaining_quantity' => (int) $batch->remaining_quantity,
                'unit_cost' => $this->when($canViewPurchasePrices, (float) $batch->unit_cost),
                'created_at' => $batch->created_at?->format('Y-m-d H:i:s'),
            ])->values()),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
