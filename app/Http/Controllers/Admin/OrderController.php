<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderCollection;
use App\Services\Admin\Order\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(
        OrderService $orderService
    ) {
        $this->middleware("permission:" . config('permissions')['orders']['order.list'] .   "|" . config('permissions')['super_admin'] . "|" . config('permissions')['develop'] . "", ['only' => ['index', 'show']]);
        $this->middleware("permission:" . config('permissions')['orders']['order.edit'] .   "|" . config('permissions')['super_admin'] . "|" . config('permissions')['develop'] . "", ['only' => ['update', 'active']]);

        $this->orderService = $orderService;
    }

    public function getAllFront(Request $request)
    {
        try {
            $result = $this->orderService->getOrders();

            return response()->json([
                'data' => $result
            ]);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }

    public function index(Request $request)
    {
        try {
            $params = [
                'keywords' => $request->keywords,
                'page'     => $request->page,
                'per_page' => $request->per_page,
                'order_by' => $request->order_by,
                'sort_key' => $request->sort_key,
                'status'   => $request->status,
            ];

            $resultCollection = $this->orderService->index($params);

            $result = OrderCollection::collection($resultCollection);

            return $result;
        } catch (\Throwable $e) {
            Log::info($e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $this->orderService->store($data);

            return response()->json([
                'result'        => 0,
                'message'       => "Tạo mới thành công!",
            ], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }

    public function show($id)
    {
        $banner           = $this->orderService->getOrderById($id);
        $banner['active'] = $banner['active'] === 1 ? true : false;

        return response()->json([
            'data'        => $banner,
        ]);
    }

    // public function update(Request $request, $id)
    // {
    //     try {
    //         $data = $request->all();
    //         $data['active'] = $data['active'] == true ? 1 : 0;
    //         $this->orderService->update($id, $data);

    //         return response()->json([
    //             'result'        => 0,
    //             'message'       => "Cập nhật thành công!",
    //         ], 200);
    //     } catch (\Throwable $th) {
    //         Log::info($th->getMessage());
    //     }
    // }

    // public function active(Request $request, $id)
    // {
    //     try {
    //         $data = $request->all();
    //         $data['active'] = $data['active'] == true ? 1 : 0;
    //         $this->orderService->active($id, $data);

    //         return response()->json([
    //             'result'  => 0,
    //             'message' => "Cập nhật thành công!",
    //         ], 200);
    //     } catch (\Exception $e) {
    //         Log::info($e->getMessage());
    //         return $this->errorBack('Đã xảy ra lỗi');
    //     }
    // }
}
