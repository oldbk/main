<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 26.11.2015
 */

namespace components\Component\Slim;
use Slim\LogWriter as BaseWriter;

class LogWriter extends BaseWriter
{
    /** @var Slim $app */
    protected $app;
    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->app = Slim::getInstance();
    }

    public function write($message, $level = null)
    {
        $messages = $this->app->session->get('logger_message', array());
        if(!isset($messages[$level])) {
            $messages[$level] = array();
        }

        $messages[$level][] = $message;
        $this->app->session->set('logger_message', $messages);

        return fwrite($this->resource, (string) $message . PHP_EOL);
    }
}