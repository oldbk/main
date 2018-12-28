<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 11.05.2016
 */

namespace components\Component\Loto\validators;


abstract class AbstractValidator implements iValidator
{
    abstract protected function run();

    public function __construct()
    {
        $this->run();
    }

    protected $app;
    public function setApp($app)
    {
        $this->app = $app;
        return $this;
    }

    protected $loto_id;
    public function setLotoId($loto_id)
    {
        $this->loto_id = $loto_id;
        return $this;
    }

    protected $item;
    public function setItem($item)
    {
        $this->item = $item;
        return $this;
    }

    protected $message;
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }
}