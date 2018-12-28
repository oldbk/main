<?php


namespace components\Facade;


use components\Component\Slim\Facade\Facade;

class App extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return self::$slim;
    }

    public static function make($key)
    {
        return self::$app[$key];
    }
}