<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    public function toArray(Request $request)
    {
        if (!empty($this->items)) {
            return [];
        }

        $array = [
            'id'          => $this->id,
            'name'        => $this->name,
            'phone'       => $this->phone,
            'dob'         => $this->dob,
            'gender'      => $this->gender,
            'province_id' => $this->province_id,
            'district_id' => $this->district_id,
            'ward_id'     => $this->ward_id,
            'address'     => $this->address,
            'email'       => $this->email,
            'password'    => $this->password,
            'active'      => $this->active,
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];

        return $array;
    }
}
