<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderCollection;
use App\Services\Admin\Order\OrderService;
use Carbon\Carbon;
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
                'keywords'         => $request->keywords,
                'page'             => $request->page,
                'per_page'         => $request->per_page,
                'order_by'         => $request->order_by,
                'sort_key'         => $request->sort_key,
                'status'           => $request->status,
                'payment_type'     => $request->payment_type,
                'status_payment'   => $request->status_payment,
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
            $data = $this->orderService->store($data);

            return response()->json($data);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }

    public function show($id)
    {
        $order           = $this->orderService->getOrderById($id);

        return response()->json([
            'data'        => $order,
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $request->all();
            $this->orderService->update($id, $data);

            return response()->json([
                'result'        => 0,
                'message'       => "Cập nhật thành công!",
            ], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }

    public function payment(Request $request, $code)
    {
        try {
            $data = $request->all();
            $data['status_payment'] = config('constant.status_payment.paid');
            $data['payment_at']     = Carbon::now()->toDateTimeString();
            $this->orderService->payment($code, $data);

            return response()->json([
                'result'  => 0,
                'message' => "Cập nhật thành công!",
            ], 200);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return $this->errorBack('Đã xảy ra lỗi');
        }
    }
}
