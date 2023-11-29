<?php

namespace Allenjd3\Mongo\Providers;

use Allenjd3\Mongo\Auth\MongoUserProvider;
use Allenjd3\Mongo\Auth\MongoUserRepository;
use Illuminate\Support\Facades\Auth;
use Statamic\Providers\AddonServiceProvider;

class MongoAuthServiceProvider extends AddonServiceProvider
{
    public function register()
    {
        app(\Statamic\Auth\UserRepositoryManager::class)->extend('mongo', function ($app, $config) {
            $guard = $this->app['config']['statamic.users.guards.cp'];
            $provider = $this->app['config']["auth.guards.$guard.provider"];
            $config['model'] = $this->app['config']["auth.providers.$provider.model"];

            return new MongoUserRepository($config);
        });
    }

    public function boot()
    {
        Auth::provider('mongo', function () {
            $config = $this->app['config']['auth.providers.users'];
            return new MongoUserProvider($this->app['hash'], $config['model']);
        });
    }
}
