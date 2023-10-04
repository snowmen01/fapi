<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interface\{
    AuthRepositoryInterface,
    PermissionRepositoryInterface,
    RoleRepositoryInterface,
    UserRepositoryInterface,
};
use App\Repositories\Repository\{
    AuthRepository,
    PermissionRepository,
    RoleRepository,
    UserRepository,
};

class AppServiceProvider extends ServiceProvider
{
    protected static $repositories = [
        'auth' => [
            AuthRepositoryInterface::class,
            AuthRepository::class
        ],
        'permission' => [
            PermissionRepositoryInterface::class,
            PermissionRepository::class
        ],
        'role' => [
            RoleRepositoryInterface::class,
            RoleRepository::class
        ],
        'user' => [
            UserRepositoryInterface::class,
            UserRepository::class
        ],
    ];
    
    public function register(): void
    {
        foreach (static::$repositories as $repository) {
            $this->app->bind($repository[0], $repository[1]);
        }
    }

    public function boot(): void
    {
    }
}
