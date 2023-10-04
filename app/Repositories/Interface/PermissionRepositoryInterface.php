<?php

namespace App\Repositories\Interface;

use App\Repositories\BaseRepositoryInterface;

interface PermissionRepositoryInterface extends BaseRepositoryInterface
{
    public function getPermissions();
}
