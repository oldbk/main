<?php

namespace components\Exceptions;


use Exception;

/**
 * Class NewsException
 * @package components\Exceptions
 */
class NewsException extends Exception
{

    public function __construct($dict, $params = [])
    {
        parent::__construct(\Lang::get($dict, $params));
    }

}