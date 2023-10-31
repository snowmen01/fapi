<?php

namespace App\Models\Filters;

use App\Helpers\QueryFilter;

class CategoryFilter extends QueryFilter
{
    protected $columns;

    public function columns($columns)
    {
        $this->columns = $columns;
    }

    public function status($value)
    {
        return $this->where('active', $value);
    }

    public function keywords($value)
    {
        return $this->where(function ($query) use ($value) {
            return $query->where('name', 'LIKE', '%' . $value . '%')
                ->orWhere('slug', 'LIKE', '%' . $value . '%')
                ->orWhere('description', 'LIKE', '%' . $value . '%')
                ->orWhere('id', 'LIKE', '%' . $value . '%');
        });
    }

    public function order($order)
    {
        $field = $this->columns[$order['column']]['data'];
        $value = $order['dir'];

        return $this->orderBy($field, $value);
    }
}
