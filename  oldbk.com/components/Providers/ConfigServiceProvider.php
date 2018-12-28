<?php

namespace components\Providers;

use components\Component\Config;
use Illuminate\Support\ServiceProvider;

/**
 * Class ConfigServiceProvider
 * @package components\Providers
 */
class ConfigServiceProvider extends ServiceProvider
{
    /**
     *
     */
    public function register()
    {
        $this->app->singleton('config', function ($c) {
            return new Config($c);
        });
    }
}