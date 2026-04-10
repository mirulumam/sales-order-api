<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_no' => $this->order_no,
            'status' => $this->status,
            'total_amount' => (float) $this->total_amount,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'created_by' => $this->whenLoaded('creator', fn () => [
                'id' => $this->creator->id,
                'username' => $this->creator->username,
            ]),
            'items' => OrderDetailResource::collection($this->whenLoaded('details')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
