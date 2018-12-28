<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.11.2015
 */

namespace components\Component\Slim\Middleware;

class ModelManager
{
    private $db_conn;
    private $namespace;

    public function __construct($db_conn, $namespace)
    {
        $this->db_conn = $db_conn;
        $this->namespace = $namespace;
    }

    /**
     * @param $model_name
     * @return iModel
     */
    public function get($model_name)
    {
        try {
            $args = array($this->db_conn);
            $class = new \ReflectionClass(sprintf('%s\\%s', $this->namespace, ucfirst(strtolower($model_name))));

            return $class->newInstanceArgs($args);
        } catch (\Exception $ex) {
            //@TODO Exception
        }
    }
}