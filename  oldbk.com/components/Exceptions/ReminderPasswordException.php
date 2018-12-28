<?php

namespace components\Exceptions;


use Exception;

/**
 * Class ReminderPasswordException
 * @package components\Exceptions
 */
class ReminderPasswordException extends Exception
{

    public function __construct($dict, $params = [])
    {
        parent::__construct(\Lang::get($dict, $params));
    }

}