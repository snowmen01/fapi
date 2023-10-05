<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            $check = $user->hasRole(config('constant.super_admin_id') || config('constant.develop')) ? true : null;
            return $check;
        });

        Gate::define('viewApiDocs', function (User $user) {
            return in_array($user->email, ['admin@gmail.com']);
        });
    }
}
