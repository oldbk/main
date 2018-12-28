<?php

namespace components\Exceptions;


use Exception;

/**
 * Class ForumException
 * @package components\Exceptions
 */
class ForumException extends Exception
{

    public function __construct($dict, $params = [])
    {
        parent::__construct(\Lang::get($dict, $params));
    }

}