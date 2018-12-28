<?php

namespace components\Providers;


use Illuminate\Support\ServiceProvider;

class XssServiceProvider extends ServiceProvider
{
    protected $defer = false;


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('xss', function()
        {
            return new \components\Helper\Xss();
        });
    }

}
