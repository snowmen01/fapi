<?php

namespace App\Services\Admin\Role;

use App\Models\Role;
use App\Models\UserRole;
use App\Services\Admin\Permission\PermissionService;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\PermissionRegistrar;


class RoleService
{
    protected $role;
    protected $spatieRole;
    protected $permissionService;
    protected $userRole;

    public function __construct(
        Role $role,
        UserRole $userRole,
        SpatieRole $spatieRole,
        PermissionService $permissionService,
    ) {
        $this->role = $role;
    }

    public function index($params)
    {
        $roles = $this->role->whereNotIn('id', [1, 2])->orderBy($params['sort_key'] ?? 'id', $params['order_by'] ?? 'DESC');

        if (isset($params['keywords'])) {
            $roles = $roles->where('name', 'LIKE', '%' . $params['keywords'] . '%');
        }

        if (isset($params['per_page'])) {
            $roles = $roles
                ->paginate(
                    $params['per_page'],
                    ['*'],
                    'page',
                    $params['page'] ?? 1
                );
        } else {
            $roles = $roles->get();
        }

        $roles->map(function($role){
            $role->name        = limitTo($role->name, 10);
        });

        return $roles;
    }

    public function show($id)
    {
        $role = $this->role->find($id);

        return $role;
    }

    public function store($data)
    {
        $role = $this->role->create($data);

        return $role;
    }

    public function update($id, $data)
    {
        $role = $this->show($id);
        $role->update($data);

        return $role;
    }

    public function delete($id)
    {
        $role = $this->show($id);
        $role->delete();

        return $role;
    }

}
