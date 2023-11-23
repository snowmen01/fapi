<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Property\OptionCollection;
use App\Services\Admin\Property\OptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OptionController extends Controller
{
    protected $optionService;

    public function __construct(
        OptionService $optionService,
    ) {
        $this->middleware("permission:" . config('permissions')['products']['product.list'] .   "|" . config('permissions')['super_admin'] . "|" . config('permissions')['develop'] . "", ['only' => ['index', 'show']]);
        $this->middleware("permission:" . config('permissions')['products']['product.create'] . "|" . config('permissions')['super_admin'] . "|" . config('permissions')['develop'] . "", ['only' => ['store']]);
        $this->middleware("permission:" . config('permissions')['products']['product.edit'] .   "|" . config('permissions')['super_admin'] . "|" . config('permissions')['develop'] . "", ['only' => ['update', 'active']]);
        $this->middleware("permission:" . config('permissions')['products']['product.delete'] . "|" . config('permissions')['super_admin'] . "|" . config('permissions')['develop'] . "", ['only' => ['destroy']]);

        $this->optionService   = $optionService;
    }

    public function index(Request $request, $propertyId)
    {
        try {
            $params = [
                'keywords'       => $request->keywords,
                'page'           => $request->page,
                'per_page'       => $request->per_page,
                'order_by'       => $request->order_by,
                'sort_key'       => $request->sort_key,
                'active'         => $request->active,
            ];

            $resultCollection = $this->optionService->index($params, $propertyId);

            $result = OptionCollection::collection($resultCollection);

            return $result;
        } catch (\Throwable $e) {
            Log::info($e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $data['active'] = $data['active'] == true ? 1 : 0;
            $this->optionService->store($data);
            
            return response()->json([
                'result'        => 0,
                'message'       => "Tạo mới thành công!",
            ], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }

    public function show($categoryId, $optionId)
    {
        $product                 = $this->optionService->getOptionById($optionId);
        $product['active']       = $product['active'] === 1 ? true : false;

        return response()->json([
            'data'        => $product,
        ]);
    }

    public function update(Request $request, $categoryId, $optionId)
    {
        try {
            $data = $request->all();
            $data['active'] = $data['active'] == true ? 1 : 0;
            $this->optionService->update($optionId, $data);
            
            return response()->json([
                'result'        => 0,
                'message'       => "Cập nhật thành công!",
            ], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }

    public function destroy($categoryId, $optionId)
    {
        
        try {
            DB::transaction(function () use ($optionId) {
            $this->optionService->delete($optionId);
            });

            return response()->json([
                'result'  => 0,
                'message' => "Xoá thành công!",
            ], 200);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return $this->errorBack('Đã xảy ra lỗi');
        }
    }
}
