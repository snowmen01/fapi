<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Repositories\Interface\PermissionRepositoryInterface;
use App\Repositories\Interface\RoleRepositoryInterface;
use Illuminate\Http\Request;

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
        $roles = $this->roleRepository->getRoleFilters($request->all());
        $roles = $roles->get();
        $roleKeys = config('constant.roles');
        $permissions = $this->permissionRepository->getPermissions()->groupBy('module_name');

        return response()->json([
            'data'        => $roles,
            'roleKey'     => $roleKeys,
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request)
    {
    }

    public function show(Request $request, $id)
    {
        $role = $this->roleRepository->getRole($id);
        $roleKeys = config('constant.roles');
        $permissions = $this->permissionRepository->getPermissions()->groupBy('module_name');

        return response()->json([
            'role'        => $role,
            'roleKey'     => $roleKeys,
            'permissions' => $permissions,
        ]);
    }

    public function update(Request $request)
    {
    }

    public function destroy()
    {
    }
}
