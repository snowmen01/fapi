<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductCollection;
use App\Models\Product;
use App\Services\Admin\Brand\BrandService;
use App\Services\Admin\Category\CategoryService;
use App\Services\Admin\Product\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kjmtrue\VietnamZone\Models\District;
use Kjmtrue\VietnamZone\Models\Province;
use Kjmtrue\VietnamZone\Models\Ward;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    protected $productService;
    protected $categoryService;
    protected $brandService;
    protected $province;
    protected $district;
    protected $ward;

    public function __construct(
        ProductService $productService,
        CategoryService $categoryService,
        BrandService $brandService,
        Province $province,
        District $district,
        Ward $ward,
    ) {
        $this->middleware("permission:" . config('permissions')['products']['product.list'] .   "|" . config('permissions')['super_admin'] . "|" . config('permissions')['develop'] . "", ['only' => ['index', 'show']]);
        $this->middleware("permission:" . config('permissions')['products']['product.create'] . "|" . config('permissions')['super_admin'] . "|" . config('permissions')['develop'] . "", ['only' => ['store']]);
        $this->middleware("permission:" . config('permissions')['products']['product.edit'] .   "|" . config('permissions')['super_admin'] . "|" . config('permissions')['develop'] . "", ['only' => ['update', 'active']]);
        $this->middleware("permission:" . config('permissions')['products']['product.delete'] . "|" . config('permissions')['super_admin'] . "|" . config('permissions')['develop'] . "", ['only' => ['destroy']]);

        $this->productService   = $productService;
        $this->categoryService  = $categoryService;
        $this->brandService     = $brandService;
        $this->province         = $province;
        $this->district         = $district;
        $this->ward             = $ward;
    }

    public function category()
    {
        try {
            $categories = $this->categoryService->getCategories();

            return response()->json([
                'data' => $categories,
            ]);
        } catch (\Throwable $e) {
            Log::info($e->getMessage());
        }
    }

    public function brand()
    {
        try {
            $brands = $this->brandService->getBrands();

            return response()->json([
                'data' => $brands,
            ]);
        } catch (\Throwable $e) {
            Log::info($e->getMessage());
        }
    }

    public function index(Request $request)
    {
        try {
            $params = [
                'keywords'       => $request->keywords,
                'category_id'    => $request->category_id,
                'brand_id'       => $request->brand_id,
                'page'           => $request->page,
                'per_page'       => $request->per_page,
                'order_by'       => $request->order_by,
                'sort_key'       => $request->sort_key,
                'active'         => $request->active,
            ];

            $resultCollection = $this->productService->index($params);

            $result = ProductCollection::collection($resultCollection);

            return $result;
        } catch (\Throwable $e) {
            Log::info($e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $data['slug']   = Str::slug($data['name'], '-');
            $data['active'] = $data['active'] == true ? 1 : 0;
            $data['trending'] = $data['trending'] == true ? 1 : 0;
            $data['many_version'] = $data['many_version'] == true ? 1 : 0;
            $this->productService->store($data);
            
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
        $product                 = $this->productService->getProductById($id);
        $product['active']       = $product['active'] === 1 ? true : false;
        $product['trending']     = $product['trending'] == 1 ? true : false;
        $product['many_version'] = $product['many_version'] == 1 ? true : false;

        return response()->json([
            'data'        => $product,
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $request->all();
            $data['slug']   = Str::slug($data['name'], '-');
            $data['active'] = $data['active'] == true ? 1 : 0;
            $data['trending'] = $data['trending'] == true ? 1 : 0;
            $data['many_version'] = $data['many_version'] == true ? 1 : 0;
            $this->productService->update($id, $data);
            
            return response()->json([
                'result'        => 0,
                'message'       => "Cập nhật thành công!",
            ], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }

    public function destroy(Product $product)
    {
    }
}
