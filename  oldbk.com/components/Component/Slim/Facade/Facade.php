<?php
namespace components\Component\Slim\Facade;

use Illuminate\Support\Facades\Facade as IlluminateFacade;

class Facade extends IlluminateFacade
{
    protected static $slim;

    public static function setFacadeApplication($app)
    {
        static::$app = $app->container;
        static::$slim = $app;
    }
}