<?php

namespace components\Providers;

use Illuminate\Translation\TranslationServiceProvider as TSP;
use Illuminate\Translation\FileLoader;


class TranslationServiceProvider extends TSP
{

    /**
     * Register the translation line loader.
     *
     * @return void
     */
    protected function registerLoader()
    {
        $this->app->singleton('translation.loader', function ($app) {
            return new FileLoader($app['files'], $app->config->get('path.lang'));
        });
    }

}
