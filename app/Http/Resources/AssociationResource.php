<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssociationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'total_members' => $this->total_members,
            'monthly_amount' => $this->monthly_amount,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'members' => AssociationMemberResource::collection($this->whenLoaded('members')),
            'payments' => AssociationPaymentResource::collection($this->whenLoaded('payments')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
