<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Role\CreateRequest;
use App\Repositories\Interface\PermissionRepositoryInterface;
use App\Repositories\Interface\RoleRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    protected $roleRepository;
    protected $permissionRepository;

    public function __construct(
        RoleRepositoryInterface $roleRepository,
        PermissionRepositoryInterface $permissionRepository,
    ) {
        $this->middleware("permission:" . config('permissions')['roles']['role.list'] .   "|" . config('permissions')['super_admin'] . "|" . config('permissions')['develop'] . "", ['only' => ['index', 'show']]);
        $this->middleware("permission:" . config('permissions')['roles']['role.create'] . "|" . config('permissions')['super_admin'] . "|" . config('permissions')['develop'] . "", ['only' => ['store']]);
        $this->middleware("permission:" . config('permissions')['roles']['role.edit'] .   "|" . config('permissions')['super_admin'] . "|" . config('permissions')['develop'] . "", ['only' => ['update', 'active']]);
        $this->middleware("permission:" . config('permissions')['roles']['role.delete'] . "|" . config('permissions')['super_admin'] . "|" . config('permissions')['develop'] . "", ['only' => ['destroy']]);

        $this->roleRepository       = $roleRepository;
        $this->permissionRepository = $permissionRepository;
    }

    public function index(Request $request)
    {
        try {
            $roles = $this->roleRepository->getRoleFilters($request->all());
            $roleKeys = config('constant.roles');
            $permissions = $this->permissionRepository->getPermissions()->groupBy('module_name');

            return response()->json([
                'data'        => $roles,
                'roleKey'     => $roleKeys,
                'permissions' => $permissions,
            ], 200);
        } catch (\Throwable $e) {
            Log::info($e->getMessage());
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            $data = $request->all();
            DB::transaction(function () use ($data) {
                $this->roleRepository->createRole($data);
            });

            return response()->json([
                'result'  => 0,
                'message' => "Tạo mới thành công!",
            ], 200);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return $this->errorBack('Đã xảy ra lỗi');
        }
    }

    public function show(Request $request, $id)
    {
        $role        = $this->roleRepository->getRole($id);
        $roleKeys    = config('constant.roles');
        $permission  = $role->permissions->pluck('id')->toArray();
        $permissions = $this->permissionRepository->getPermissions()->groupBy('module_name');

        return response()->json([
            'role'        => $role,
            'permission'  => $permission,
            'roleKey'     => $roleKeys,
            'permissions' => $permissions,
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $request->all();
            DB::transaction(function () use ($data, $id) {
                $this->roleRepository->updateRole($data, $id);
            });

            return response()->json([
                'result'  => 0,
                'message' => "Cập nhật thành công!",
            ], 200);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return $this->errorBack('Đã xảy ra lỗi');
        }
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $this->roleRepository->deleteRole($id);
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
