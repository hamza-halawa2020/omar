<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'image' => $this->image,
            'description' => $this->description,
            'purchase_price' => round($this->purchase_price),
            'sale_price' => round($this->sale_price),
            'stock' => $this->stock,
            'creator' => new UserResource($this->whenLoaded('creator')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
