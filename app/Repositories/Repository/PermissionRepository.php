<?php

namespace App\Repositories\Repository;

use App\Models\Permission;
use App\Repositories\BaseRepository;
use App\Repositories\Interface\PermissionRepositoryInterface;

class PermissionRepository extends BaseRepository implements PermissionRepositoryInterface
{
    protected $permission;

    public function __construct(
        Permission $permission
    ) {
        $this->permission = $permission;
    }

    public function getPermissions()
    {
        $permissions = $this->permission->get();
        foreach ($permissions as $key => $permission) {
            if ($permission->name == 'super_admin' || $permission->name =='develop' ) {
                unset($permissions[$key]);
            }
        }

        return $permissions;
    }

}
