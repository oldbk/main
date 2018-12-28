<?php

namespace components\Providers;

use components\Component\ConfigKo;
use components\Component\Slim\Slim;
use Illuminate\Support\ServiceProvider;

/**
 * Class ConfigKoServiceProvider
 * @package components\Providers
 */
class ConfigKoServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
		/** @var Slim $app */
		$app = $this->app;

		$app->singleton('configKo', function () use ($app) {
            return new ConfigKo($app);
        });
    }
}
