<?php

namespace App\Repositories\Repository;

use App\Models\Role;
use App\Repositories\BaseRepository;
use App\Repositories\Interface\RoleRepositoryInterface;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    protected $role;

    public function __construct(
        Role $role
    ) {
        $this->role = $role;
    }

    public function show()
    {
        $role = $this->role->find(auth()->id());

        return $role;
    }

    public function getRole($id)
    {
        $role = $this->role->find($id);

        return $role;
    }

    public function getRoles()
    {
        $roles = $this->role->all();

        return $roles;
    }

    public function getRoleFilters($data)
    {
        $roles = $this->role
            ->whereNotIn('id', [config('constant')['super_admin_id'], config('constant')['develop_id']])
            ->orderBy('id', 'desc')
            ->filter($data);

        return $roles;
    }

    // public function getListRoles($data)
    // {
    //     $data['order'] = $data['order'][0];
    //     $model = $this->getRoleFilters($data);
    //     $recordsTotal = $model->count();

    //     $roles = $model->offset($data['start'])
    //         ->limit($data['length'])
    //         ->get();

    //     $roles->map(function ($role) {
    //         $role->name = limitCharacter($role->name, 40);

    //         $role->action = view('admin.roles.elements.actions', ['roleId' => $role->id])->render();
    //     });
    //     return [
    //         'result' => $roles,
    //         'recordsTotal' => $recordsTotal,
    //         'recordsFiltered' => $recordsTotal
    //     ];
    // }
}
