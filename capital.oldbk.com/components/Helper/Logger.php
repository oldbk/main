<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 26.11.2015
 */

namespace components\Helper;


use components\Component\Slim\Slim;

class Logger
{
    public static function write(\Exception $ex)
    {
        $app = Slim::getInstance();
        $app->log->critical('Message ' . $ex->getMessage());
    }
}