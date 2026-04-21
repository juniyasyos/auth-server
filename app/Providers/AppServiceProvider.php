<?php

namespace App\Providers;

use App\Models\Session;
use App\Models\User;
use App\Observers\SessionObserver;
use App\Observers\UserApplicationRoleObserver;
use App\Observers\UserObserver;
use App\Services\AppRegistry;
use App\Services\BackchannelDatabaseSessionHandler;
use App\Services\Contracts\AppRegistryContract;
use App\Domain\Iam\Services\BackchannelLogoutService;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AppRegistryContract::class, AppRegistry::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Gate $gate): void
    {
        $gate->define('viewPulse', function (User $user) {
            return true;
        });

        User::observe(UserObserver::class);
        Session::observe(SessionObserver::class);
        \App\Domain\Iam\Models\UserApplicationRole::observe(UserApplicationRoleObserver::class);
        \App\Models\UserAccessProfile::observe(\App\Observers\UserAccessProfileObserver::class);

        $this->app['session']->extend('database', function ($app) {
            $connection = $app['db']->connection($app['config']['session.connection']);

            return new BackchannelDatabaseSessionHandler(
                $connection,
                $app['config']['session.table'],
                $app['config']['session.lifetime'],
                $app->make(BackchannelLogoutService::class),
                $app,
            );
        });
    }
}
