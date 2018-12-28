<?php
namespace components\Component\Slim\Middleware;

/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 18.11.2015
 */
class Db extends \Slim\Middleware
{
    /** @var array  */
    protected $settings = array();
    /** @var string */
    protected $service_name;

    public function __construct($service_name, $settings = array())
    {
        $this->service_name = $service_name;
        $this->settings = $settings;
    }

    /**
     * Call
     */
    public function call()
    {
        $this->registerHelper();
        $this->next->call();
    }

    protected function registerHelper()
    {
        $config = $this->settings;
        $this->app->container->singleton($this->service_name, function () use ($config) {
            try {
                $db = new \PDO(
                    sprintf("mysql:host=%s;dbname=%s", $config['host'], $config['dbname']),
                    $config['user'],
                    $config['password']
                );
                $db->exec("SET NAMES CP1251");
                $db->exec("SET time_zone = '+3:00';");
            } catch (\Exception $ex) {
                var_dump($ex->getMessage());die;
                //@TODO error
            }

            return $db;
        });
    }

}