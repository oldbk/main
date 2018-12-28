<?php
namespace components\Component\Slim\Middleware\ClientScript;

/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 18.11.2015
 */
class ClientScriptRegister extends \Slim\Middleware
{
    /** @var array  */
    protected $settings = array();

    public function __construct($settings = array())
    {

    }

    /**
     * Call
     */
    public function call()
    {
        $this->registerHelper();
        $this->next->call();
    }

    protected function registerHelper()
    {
        $settings = array(
            'host'  => $this->app->request->getHost(),
            'https' => false,
        );
        $this->app->container->singleton('clientScript', function () use ($settings) {
            return new ClientScript($settings);
        });
    }
}