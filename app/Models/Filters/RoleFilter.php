<?php

namespace App\Models\Filters;

use App\Helpers\QueryFilter;

class RoleFilter extends QueryFilter
{
    protected $columns;

    public function columns($columns)
    {
        $this->columns = $columns;
    }

    public function keywords($value)
    {
        return $this->where(function ($query) use ($value) {
            return $query->where('name', 'LIKE', '%' . $value . '%')
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
