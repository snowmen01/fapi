<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\Customer\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(
        CustomerService $customerService
    ) {
        $this->middleware("permission:" . config('permissions')['orders']['order.list'] .   "|" . config('permissions')['super_admin'] . "|" . config('permissions')['develop'] . "", ['only' => ['index', 'show']]);
        $this->middleware("permission:" . config('permissions')['orders']['order.create'] . "|" . config('permissions')['super_admin'] . "|" . config('permissions')['develop'] . "", ['only' => ['store']]);
        $this->middleware("permission:" . config('permissions')['orders']['order.edit'] .   "|" . config('permissions')['super_admin'] . "|" . config('permissions')['develop'] . "", ['only' => ['update', 'active']]);
        $this->middleware("permission:" . config('permissions')['orders']['order.delete'] . "|" . config('permissions')['super_admin'] . "|" . config('permissions')['develop'] . "", ['only' => ['destroy']]);

        $this->customerService = $customerService;
    }

    public function index()
    {
    }
    public function store()
    {
    }
    public function update()
    {
    }
    public function active()
    {
    }
    public function destroy()
    {
    }
    public function show(Request $request)
    {
        $user = $this->customerService->show($request->email);

        return response()->json([
            'user'      => $user,
        ], 200);
    }
}
