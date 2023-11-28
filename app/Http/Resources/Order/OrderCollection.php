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
            'product_id'       => $this->product_id,
            'order_id'         => $this->order_id,
            'sku_id'           => $this->sku_id,
            'quantity'         => $this->quantity,
            'price'            => $this->price,
            'description'      => $this->description,
            'code'             => $this->code,
            'address'          => $this->address,
            'createdAt'        => $this->createdAt,
            'status'           => $this->status,
            'total'            => $this->total,
            'filename'         => $this->filename,
        ];

        return $array;
    }
}
