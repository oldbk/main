<?php


namespace components\SchemaManager;

/**
 * Class SchemaManager
 * @package components\SchemaManager
 */
class SchemaManager
{

    protected $app;

    /**
     * SchemaManager constructor.
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
     * @param $table
     */
    public function create($table)
    {
        $table = 'components\SchemaManager\\' . $table .'';

        if (class_exists($table)) {
            $migration = new $table();
            $migration->up();
            die('create done!');
        }
    }
}