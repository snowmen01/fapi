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

use Dedoc\Scramble\Scramble;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;

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
        Scramble::routes(function (Route $route) {
            return Str::startsWith($route->uri, 'api/');
        });
    }
}
