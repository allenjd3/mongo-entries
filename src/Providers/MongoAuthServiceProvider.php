<?php

namespace Allenjd3\Mongo\Providers;

use Allenjd3\Mongo\Auth\MongoUserProvider;
use Illuminate\Support\Facades\Auth;
use Statamic\Providers\AddonServiceProvider;

class MongoAuthServiceProvider extends AddonServiceProvider
{
    public function boot()
    {
        Auth::provider('mongo', function () {
            $config = $this->app['config']['auth.providers.users'];
            return new MongoUserProvider($this->app['hash'], $config['model']);
        });
    }
}
