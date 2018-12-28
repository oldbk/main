<?php


namespace components\IndexManager;

/**
 * Class IndexManager
 * @package components\IndexManager
 */
class IndexManager
{

    protected $app;

    /**
     * IndexManager constructor.
     * @param $app
     * @param $action
     * @param mixed $param
     */
    public function __construct($app, $action, $param = false)
    {
        $this->app = $app;
        $this->{$action}($param);
    }

    /**
     * @param $param
     */
    public function create($param)
    {
        $table = array_shift($param);

        if (class_exists($table)) {
            $migration = new $table();
            $migration->up(array_pop($param));
        }
    }
}