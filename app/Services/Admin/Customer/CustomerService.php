<?php

namespace App\Services\Admin\Customer;

use App\Models\Customer;

class CustomerService
{
    protected $customer;

    public function __construct(
        Customer $customer
    ) {
        $this->customer = $customer;
    }

    public function index($params)
    {
        $customers = $this->customer->with('image')->orderBy($params['sort_key'] ?? 'id', $params['order_by'] ?? 'DESC');

        if (isset($params['keywords'])) {
            $customers = $customers->where('name', 'LIKE', '%' . $params['keywords'] . '%');
        }

        if (isset($params['per_page'])) {
            $customers = $customers
                ->paginate(
                    $params['per_page'],
                    ['*'],
                    'page',
                    $params['page'] ?? 1
                );
        } else {
            $customers = $customers->get();
        }

        if (isset($params['active'])) {
            $customers = $customers->where('active', $params['active']);
        }

        $customers->map(function ($customer) {
            $customer->name        = limitTo($customer->name, 10);
            $customer->description = limitTo($customer->description, 10);
        });

        return $customers;
    }

    public function show($id)
    {
        $customer = $this->customer->with('image')->find($id);

        return $customer;
    }

    public function getBannerById($id)
    {
        $customer = $this->customer->with('image')->find($id);

        return $customer;
    }

    public function getBanners()
    {
        $customer = $this->customer->where('active',config('constant.active'))->with('image')->get();

        return $customer;
    }

    public function store($data)
    {
        $customer = $this->customer->create($data);
        $dataImage = ['path' => $data['images'][0]['url']];
        $customer->image()->create($dataImage);

        return $customer;
    }

    public function update($id, $data)
    {
        $customer = $this->getBannerById($id);
        if (isset($data['images'][0]['url'])) {
            $customer->image()->delete();
            $dataImage = ['path' => $data['images'][0]['url']];
            $customer->image()->create($dataImage);
        }
        $customer->update($data);

        return $customer;
    }

    public function active($id, $data)
    {
        $customer = $this->getBannerById($id);
        $customer->update(['active' => $data['active']]);

        return $customer;
    }

    public function delete($id)
    {
        $customer = $this->getBannerById($id);
        $customer->image()->delete();
        $customer->delete();

        return $customer;
    }
}
