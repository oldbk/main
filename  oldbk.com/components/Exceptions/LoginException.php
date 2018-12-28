<?php

namespace components\Exceptions;


use Exception;

/**
 * Class LoginException
 * @package components\Exceptions
 */
class LoginException extends Exception
{
    public function __construct($dict, $params = [])
    {
        parent::__construct(\Lang::get($dict, $params));
    }
}