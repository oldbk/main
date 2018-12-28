<?php

namespace components\Facade;

use components\Component\Slim\Facade\Facade;

/**
 * Class Config
 * @package components\Facade
 */
class Config extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'config';
    }
}