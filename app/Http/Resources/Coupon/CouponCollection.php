<?php

namespace App\Http\Resources\Coupon;

use Illuminate\Http\Resources\Json\JsonResource;

class CouponCollection extends JsonResource
{
    public function toArray($request)
    {
        if (!empty($this->items)) {
            return [];
        }

        $array = [
            'id'               => $this->id,
            'name'             => $this->name,
            'code'             => $this->code,
            'value'            => $this->value,
            'count'            => $this->count,
            'type'             => $this->type,
            'active'           => $this->active,
            'description'      => $this->description,
            'expiredDate'      => $this->expired_at
        ];

        return $array;
    }
}
