<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderCollection extends JsonResource
{
    public function toArray($request)
    {
        if (!empty($this->items)) {
            return [];
        }

        $array = [
            'id'               => $this->id,
            'customer_id'      => $this->customer_id,
            'description'      => $this->description,
            'code'             => $this->code,
            'address'          => $this->address,
            'createdAt'        => $this->created_at,
            'status'           => $this->status,
        ];

        return $array;
    }
}
