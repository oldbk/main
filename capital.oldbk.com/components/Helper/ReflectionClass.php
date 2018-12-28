<?php
namespace components\Helper;

/**
 * Created by PhpStorm.
 */
class ReflectionClass
{
    protected $class_instance = null;

    public function __construct($class_name, $arguments)
    {
        if (count($arguments) > 0)
        {
            if (method_exists($class_name,  '__construct') === false) {
                throw new \Exception("Constructor for the class <strong>$class_name</strong> does not exist, you should not pass arguments to the constructor of this class!");
            }

            $refMethod = new \ReflectionMethod($class_name,  '__construct');
            $params = $refMethod->getParameters();

            $re_args = array();

            foreach($params as $key => $param) {
                if(array_key_exists($param->getName(), $arguments)) {
                    if ($param->isPassedByReference())
                        $re_args[$param->getName()] = &$arguments[$param->getName()];
                    else
                        $re_args[$param->getName()] = $arguments[$param->getName()];
                } else {
                    if ($param->isPassedByReference())
                        $re_args[$key] = &$arguments[$key];
                    else
                        $re_args[$key] = $arguments[$key];
                }
            }

            $refClass = new \ReflectionClass($class_name);
            $class_instance = $refClass->newInstanceArgs((array) $re_args);
        } else
            $class_instance = new $class_name();

        $this->setClassInstance($class_instance);
    }

    /**
     * @return null
     */
    public function getClassInstance()
    {
        return $this->class_instance;
    }

    /**
     * @param null $class_instance
     * @return $this
     */
    public function setClassInstance($class_instance)
    {
        $this->class_instance = $class_instance;
        return $this;
    }
}