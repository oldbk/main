<?php
namespace components\Command\_base;
use components\Component\Slim\Slim;
use components\Helper\Json;

/**
 * Created by PhpStorm.
 */

abstract class BaseCommand
{
    /** @var Slim */
    protected $app;

    public function __construct(Slim $container, $action)
    {
        $this->app = $container;

        $this->run($action);
    }

    /**
     * @param $service_name
     * @return mixed
     */
    protected function get($service_name)
    {
        return $this->app->container->get($service_name);
    }

    protected function run($action)
    {
        $actionMethod = method_exists($this, $action.'Action') ? $action.'Action' : 'indexAction';
        if(!method_exists($this, $actionMethod))
            throw new \Exception(sprintf('Action not found. Action: %s', $actionMethod));

        if($this->beforeAction($action)) {
            $this->{$actionMethod}();

            $this->afterAction($action);
        }
    }

    protected function beforeAction($action)
    {
        return true;
    }

    protected function afterAction($action)
    {

    }
}