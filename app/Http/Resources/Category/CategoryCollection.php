<?php

namespace App\Http\Resources\Category;

use App\Http\Resources\Images\ImagesCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryCollection extends JsonResource
{
    public function toArray($request)
    {
        if (!empty($this->items)) {
            return [];
        }

        $array = [
            'id'               => $this->id,
            'name'             => $this->name,
            'description'      => $this->description,
            'active'           => $this->active,
            'slug'             => $this->slug,
            'createdAt'        => $this->created_at,
            'updatedAt'        => $this->updated_at,
        ];

        return $array;
    }
}
