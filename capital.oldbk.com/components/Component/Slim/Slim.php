<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 18.11.2015
 */

namespace components\Component\Slim;
use components\Component\Config;
use components\Component\ConfigKo;
use components\Component\Email\Notification;
use components\Component\Security\iSecurity;
use components\Component\Slim\Middleware\Session\Helper;
use components\models\Settings;
use Elastica\Client;
use Monolog\Logger;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use phpFastCache\Core\DriverAbstract;

/**
 * Class Slim
 * @package components\Slim
 *
 * @property @deprecated \database\DB $db
 * @property \components\Component\Slim\Middleware\Session\Helper $session
 * @property \components\Component\Slim\Middleware\ClientScript\ClientScript $clientScript
 * @property \components\Component\Quests\QuestContainer $quest
 * @property \components\Component\BotDialog\BotDialog $botDialog
 * @property \components\Component\WebUser $webUser
 * @property Client $elasticsearch
 * @property AMQPStreamConnection $rabbitmq
 * @property DriverAbstract $cache
 * @property Settings $app_config
 * @property Config $dbConfig
 * @property array $ruine
 * @property ConfigKo $configKo
 * @property iSecurity $bk_security
 * @property Notification $email
 * @property Logger $logger
 * @property Request $request
 *
 */
class Slim extends \Slim\Slim
{
    public function __construct(array $userSettings = array())
    {
        parent::__construct($userSettings);

        $this->container->singleton('router', function ($c) {
            return new Router();
        });

		$this->container->singleton('request', function ($c) {
			return new Request($c['environment']);
		});

        $this->container->singleton('session', function ($c) {
            //quick fix
            if (!session_id()) {
                session_start();
            }

            return new Helper();
        });

        $this->container->singleton('logWriter', function ($c) {
            $logWriter = $c['settings']['log.writer'];

            return is_object($logWriter) ? $logWriter : new LogWriter($c['environment']['slim.errors']);
        });
    }

    /**
     * @param string $name
     * @return self
     */
    public static function getInstance($name = 'default')
    {
        return parent::getInstance($name);
    }


    /**
     * @param null $viewClass
     * @return View
     */
    public function view($viewClass = null)
    {
        return parent::view($viewClass);
    }

    public function redirect2($url, $status = 302)
    {
        header('Location: '.$url, true, $status);
        exit;
    }

    public function createUrl($name, $params = array())
	{
		return $this->router->urlFor($name, $params);
	}
}