<?php

namespace components\Facade;

use components\Component\Slim\Facade\Facade;

/**
 * Class Config
 * @package components\Facade
 */
class ConfigKo extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'configKo';
    }
}