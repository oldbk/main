<?php

namespace components\Providers;

use Illuminate\Support\ServiceProvider;

class CacheServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('cache', function ($app) {
            $config = array(
                "storage"       =>  "files", // memcached, redis, ssdb, ..etc
                "path"          =>  $app->config->get('path.cache'),
                'securityKey'   => 'cache'
            );
            \phpFastCache\CacheManager::setup($config);

            return \phpFastCache\CacheManager::Files();
        });
    }
}
