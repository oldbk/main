<?php


namespace components\Facade;


use components\Component\Slim\Facade\Facade;

class Xss extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'xss';
    }
}