<?php

namespace components\Exceptions;


use Exception;

/**
 * Class RegistrationException
 * @package components\Exceptions
 */
class RegistrationException extends Exception
{
    public function __construct($dict, $params = [])
    {
        parent::__construct(\Lang::get($dict, $params));
    }
}