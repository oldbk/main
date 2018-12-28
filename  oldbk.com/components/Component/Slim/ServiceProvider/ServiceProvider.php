<?php

namespace components\Component\Slim\ServiceProvider;


use components\Component\Slim\AliasLoader;
use components\Component\Slim\Facade\Facade;
use Illuminate\Container\Container;
use Slim\Slim;

/**
 * Class ServiceProvider
 * @package components\Component\Slim\ServiceProvider
 */
class ServiceProvider extends Container
{
    /**
     * @var Slim
     */
    protected $app;

    /**
     * @var \Slim\Helper\Set
     */
    protected $container;

    /**
     * @var array
     */
    protected $providers = [];

    /**
     * ServiceProvider constructor.
     * @param Slim $app
     */
    public function __construct(Slim $app)
    {
        $this->app = $app;
        $this->container = $app->container;//Slim container
    }

    /**
     * @return Slim
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @return \Slim\Helper\Set
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Boots all of the service providers
     */
    public function bootProviders()
    {
        foreach ($this->providers as $provider) {
            if (method_exists($provider, 'boot')) {
                $provider->boot();
            }
        }

        return $this;
    }

    /**
     * @param array $providers
     * @return $this|Container
     */
    public function addProviders(array $providers)
    {
        foreach ($providers as $provider) {
            $p = new $provider($this);
            $p->register();
            $this->providers[] = $p;
        }

        $this->bootProviders();

        return $this;
    }

    public function pushProvider($provider)
    {
        $p = new $provider($this);
        $p->register();
        $this->providers[] = $p;
        $provider->boot();

        return $this;
    }


    /**
     * @param array $aliases
     */
    public function addAliases(array $aliases)
    {
        Facade::setFacadeApplication($this->app);
        AliasLoader::getInstance($aliases)->register();
    }

    /**
     * Binds all of the service providers to both the Illuminate container and Slim's container
     * @param array|string $abstract
     * @param null $concrete
     * @param bool $shared
     */
    public function bind($abstract, $concrete = null, $shared = false)
    {
        parent::bind($abstract, $concrete, $shared);
        $container = $this;
        $this->container[$abstract] = function ($c) use ($container, $abstract) {
            return $container->make($abstract);
        };
    }

    public function resourcePath()
    {
        return ROOT_DIR . '/template';
    }
}