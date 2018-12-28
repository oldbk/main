<?php
namespace components\Helper\Exception;
use components\Component\Slim\Slim;
use Exception;

/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 24.11.2015
 */
class ToViewException extends Exception
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        Slim::getInstance()->flash('error', $message);
    }
}