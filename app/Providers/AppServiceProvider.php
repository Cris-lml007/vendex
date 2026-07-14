<?php

namespace App\Providers;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        Gate::define('isAdmin', function(User $user){
            return $user->role == Role::ADMIN;
        });

        Gate::define('isSeller', function(User $user){
            return $user->role == Role::SELLER || $user->role == Role::ADMIN || $user->role == Role::PRIVILEGIED;
        });

        Gate::define('isPrivilegied', function(User $user){
            return $user->role == Role::PRIVILEGIED || $user->role == Role::ADMIN;
        });

    }
}
