<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 02.02.2016
 */

namespace components\Component;


use components\Component\Slim\Slim;

abstract class AbstractComponent
{
    /** @var Slim */
    private $_app;
    public function __construct($app = null)
    {
        if($app === null) {
            $this->_app = Slim::getInstance();
        } else {
            $this->_app = $app;
        }

        $this->run();
    }

    abstract protected function run();

    /**
     * @return Slim
     */
    protected function app()
    {
        return $this->_app;
    }
}