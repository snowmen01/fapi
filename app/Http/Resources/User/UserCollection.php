<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserCollection extends JsonResource
{
    public function toArray($request)
    {
        if (!empty($this->items)) {
            return [];
        }

        $array = [
            'id'          => $this->id,
            'name'        => $this->name,
            'phone'       => $this->phone,
            'image'            => $this->whenLoaded('image', function () {
                return $this->image;
            }),
            'dob'         => $this->dob,
            'roleId'      => $this->roleId,
            'gender'      => $this->gender,
            'province_id' => $this->province_id,
            'district_id' => $this->district_id,
            'ward_id'     => $this->ward_id,
            'address'     => $this->address,
            'email'       => $this->email,
            'password'    => $this->password,
            'active'      => $this->active,
            'createdAt'   => $this->created_at,
            'updatedAt'   => $this->updated_at,
        ];

        return $array;
    }
}
