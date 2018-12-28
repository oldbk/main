<?php
namespace components\Controller\_base;
use components\Component\Slim\Slim;
use components\Helper\Json;
/**
 * Created by PhpStorm.
 */

abstract class BaseController
{
    /** @var string */
    protected $layout = 'main';
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

    public function renderJSON(array $data)
    {
        echo Json::encode($data);
        exit;
    }

    /**
     * @param $_view
     * @param null $_data_
     * @param boolean $_return
     * @return string
     * @throws \Exception
     */
    public function render($_view, $_data_ = null, $_return = false)
    {
        $html = $this->app->view()
            ->setControllerId($this->getControllerId())
            ->setLayout($this->layout)
            ->render($_view, $_data_);

        if($_return) {
            return $html;
        } else
            echo $html;

        return null;
    }

    /**
     * @param $_view
     * @param $_data_
     * @param bool|false $_return
     * @return string
     * @throws \Exception
     */
    public function renderPartial($_view, $_data_ = null, $_return = false)
    {
        $this->app->view()->setControllerId($this->getControllerId());
        $html = $this->app->view()->renderPartial($_view, $_data_);
        if($_return) {
            return $html;
        } else
            echo $html;

        return null;
    }

    protected function redirect($link, $code = 301)
    {
        $this->app->redirect($link, $code);
    }

    protected function getControllerId()
    {
        $ref = (new \ReflectionClass($this));

        return str_replace('controller', '', strtolower($ref->getShortName()));
    }
}