<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.10.2015
 */

define('PRODUCTION_MODE', $mode == 'production');
if (!PRODUCTION_MODE) require_once(__DIR__ . '/config/debugmode.php');


define('ROOT_DIR', realpath(__DIR__ . '/../'));
define('APP_PHP_LOG', '/www/logs/php/');

$loader = require_once __DIR__ . '/../vendor/autoload.php';
$loader->add('phpFastCache', __DIR__.'/libs/phpfastcache-4.3.17/src/');

class_alias('\Illuminate\Database\Eloquent\Model', '\Eloquent');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use components\Component\Db\CapitalDb as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

$app = new \components\Component\Slim\Slim(array(
    'mode'              => $mode,
    'templates.path'    => ROOT_DIR . '/template',
    'view'              => '\components\Component\Slim\View',
));

// Only invoked if mode is "production"
$app->configureMode('production', function () use ($app) {
    $app->config(require_once(__DIR__ . '/config/prod.php'));
});
$app->configureMode('development', function () use ($app) {
    $app->config(require_once(__DIR__ . '/config/dev.php'));
});
$app->configureMode('local', function () use ($app) {
    header('Content-Type: text/html; charset=cp1251');

    $app->config(require_once(__DIR__ . '/config/local.php'));
});

$app->container->set('exp', require(__DIR__ . '/config/exp.php'));
$app->container->set('rune', require(__DIR__ . '/config/exp/rune.php'));
$app->container->set('ruine', require(__DIR__ . '/config/ruine.php'));
$app->container->set('class_desc', require(__DIR__ . '/config/class_desc.php'));
$app->container->set('magic_desc', require(__DIR__ . '/config/magic_desc.php'));
$app->container->set('config.craft', [
	'exp' => require(__DIR__ . '/config/craft/exp.php'),
	'params' => require(__DIR__ . '/config/craft/params.php'),
]);
$app->container->set('config.unik', require(__DIR__.'/config/unik.php'));

$app->container->singleton('cache', function () use ($app) {
    /*$config = array(
        "storage"   =>  "files", // memcached, redis, ssdb, ..etc
        //"path"      =>  ROOT_DIR."/components/tmp/cache/",
        "path"      =>  "/www/data/combat_logs/quest_logs /",
		'securityKey' => 'cache',
    );*/
	$config = array(
		'memcache' => array(
			array('127.0.0.1', 11211, 1),
			//  array("new.host.ip",11211,1),
		),
	);
	
    \phpFastCache\CacheManager::setup($config);

    $Cache = \phpFastCache\CacheManager::Memcached();

    return $Cache;
});

$app->container->singleton('rabbitmq', function () use ($app) {
	$config = $app->config('rabbitmq');
	if(!$config) {
		return null;
	}

	return new AMQPStreamConnection(
		$config['server']['host'],
		$config['server']['port'],
		$config['server']['user'],
		$config['server']['password'],
		$config['server']['vhost'],
		false,
		'AMQPLAIN',
		null,
		'en_US',
		30.0,
		15.0
	);
});

$app->container->singleton('email', function() use ($app) {
	$config = $app->config('emails');
	if(!$config) {
		return null;
	}

	return new \components\Component\Email\Notification($config, $app);
});

$db_config = $app->config('db.capital');
//region DB
$capsule = new Capsule;
$capsule->addConnection([
	'driver'    => 'mysql',
	'host'      => $db_config['host'],
	'database'  => $db_config['dbname'],
	'username'  => $db_config['username'],
	'password'  => $db_config['password'],
	'charset'   => $db_config['charset'],
	'prefix'    => '',
	'options' => array(
		\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET time_zone = \'+03:00\';',
	),
	'collation' => null,
], 'capital');
// Set the event dispatcher used by Eloquent models... (optional)
$capsule->setEventDispatcher(new Dispatcher(new Container));
// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();
// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();
//$capsule->getConnection()->raw('SET time_zone = "+3:00";');
//$capsule->getConnection()->raw('SET NAMES CP1251;');
//endregion

//region elasticsearch
$elastic_config = $app->config('elasticsearch');
$app->container->singleton('elasticsearch', function() use ($elastic_config) {
	$config = [
		'host' => $elastic_config['host'],
		'port' => $elastic_config['port'],
		'username' => $elastic_config['username'],
		'password' => $elastic_config['password'],
	];
	return new \Elastica\Client($config);
});
//endregion

//region logger
$app->container->singleton('logger', function() use ($app) {
	$log = new \Monolog\Logger('application');

	$options = [
		'index' 		=> 'logs',
		'type' 			=> 'logs',
		'ignore_error' 	=> true,
	];
	//write all errors to elasticsearch
	$handler = new \Monolog\Handler\ElasticSearchHandler($app->elasticsearch, $options);
	$log->pushHandler($handler);


	$logPath = APP_PHP_LOG;
	if(!is_dir($logPath)) {
		@mkdir($logPath, 0777, true);
	}

	//write only >= error message
	$handler = new \Monolog\Handler\RotatingFileHandler(sprintf('%s/error.log', rtrim($logPath, '/')), 100, \Monolog\Logger::ERROR);
	$log->pushHandler($handler);

	//write only >= emergency
	$handler = new \Monolog\Handler\SlackHandler('xoxb-130693138466-441975457333-9bpGqIDPP2FvVVJwNl4secXJ', 'logs', 'chatter', true, null, \Monolog\Logger::CRITICAL);
	$log->pushHandler($handler);

	$log->pushProcessor(function ($record) {
		if( !empty( $_SERVER ) ){
			$record['extra']['_SERVER'] = $_SERVER;
		}
		if( !empty( $_SESSION ) ){
			$record['extra']['_SESSION'] = $_SESSION;
		}
		if( !empty( $_POST ) ){
			$record['extra']['_POST'] = $_POST;
		}
		$record['extra']['is_cli'] = (php_sapi_name() === 'cli');

		return $record;
	});

	return $log;
});
//endregion

$app->container->singleton('quest', function () use ($app) {
	return new \components\Component\Quests\QuestContainer($app);
});

$app->container->singleton('configKo', function () use ($app) {
	return new \components\Component\ConfigKo($app);
});

$app->container->singleton('webUser', function () use ($app) {
    return new \components\Component\WebUser();
});

if($mode != 'production') {
    $debugbar = new \Slim\Middleware\DebugBar();
    //$pdo = new DebugBar\DataCollector\PDO\TraceablePDO($app->container->get('db'));
	$pdo = new DebugBar\DataCollector\PDO\TraceablePDO(Capsule::connection()->getPdo());
    $debugbar->addCollector(new DebugBar\DataCollector\PDO\PDOCollector($pdo));
    $app->add($debugbar);
}

$app->container->singleton('dbConfig', function () use ($app) {
	return \components\models\Settings::getAll($app);
});

$app->hook('slim.before', function () use ($app) {
    \components\models\Settings::getAll($app);
});

try {
	$hooks = require_once(__DIR__ . '/hooks.php');
	foreach ($hooks as $hook_name => $hook_script) {
		require_once $hook_script;
	}
} catch (Exception $ex) {

}

if(\components\Component\Config::LOGGING_DB)
{
    register_shutdown_function(function() use ($app) {
		$db_pdo = \components\Component\Db\CapitalDb::connection()->getPdo();
		$pdo = new \DebugBar\DataCollector\PDO\TraceablePDO($db_pdo);

        $data = false;
        foreach ($pdo->getExecutedStatements() as $stmt) {
            if(preg_match('/user_badge/ui', $stmt->getSql()) || $stmt->getDuration() < 0.9) {
                continue;
            }
            $array = [
				'sql_with_params' 	=> $stmt->getSqlWithParams('<>'),
				'sql' 				=> $stmt->getSql(),
				'row_count' 		=> $stmt->getRowCount(),
				'stmt_id' 			=> $stmt->getPreparedId(),
				'prepared_stmt' 	=> $stmt->getSql(),
				'params' 			=> (object) $stmt->getParameters(),
				'duration' 			=> $stmt->getDuration(),
				'duration_str' 		=> $stmt->getDuration(),
				'memory' 			=> $stmt->getMemoryUsage(),
				'memory_str' 		=> $stmt->getMemoryUsage(),
				'end_memory' 		=> $stmt->getEndMemory(),
				'end_memory_str' 	=> $stmt->getEndMemory(),
				'is_success' 		=> $stmt->isSuccess(),
				'error_code' 		=> $stmt->getErrorCode(),
				'error_message' 	=> $stmt->getErrorMessage(),
				'event' 			=> 'sql_register_shutdown_function'
			];
			$app->logger->addCritical('', $array);

			$data = true;
        }

        if($data) {
            $message = sprintf('TOTAL:%s|%s|%s|%s',
                    count($pdo->getFailedExecutedStatements()), $pdo->getAccumulatedStatementsDuration(),
                    $pdo->getMemoryUsage(), $pdo->getPeakMemoryUsage()).PHP_EOL;

			$app->logger->addCritical($message, ['event' => 'sql_register_shutdown_function']);
        }
    });
}


if(\components\Component\Config::LOGGING_ERROR)
{
    register_shutdown_function(function() use ($app) {
        $error = error_get_last();
        if(isset($error)) {
            $message = sprintf('[%s] Level error: %s | message: %s | file: %s | line: %s', date('d.m.Y H:i:s'), $error['type'], $error['message'], $error['file'], $error['line']).PHP_EOL;

            switch ($error['type']) {
                case E_ERROR:
                case E_PARSE:
                case E_COMPILE_ERROR:
                case E_CORE_ERROR:
                    $error_level = \Monolog\Logger::CRITICAL;
                    break;
                case E_USER_ERROR:
                case E_RECOVERABLE_ERROR:
					$error_level = \Monolog\Logger::ERROR;
                    break;
                case E_WARNING:
                case E_CORE_WARNING:
                case E_COMPILE_WARNING:
                case E_USER_WARNING:
					$error_level = 0;
                    break;
                case E_NOTICE:
                case E_USER_NOTICE:
					$error_level = 0;
                    break;
                case E_STRICT:
					$error_level = 0;
                    break;
				default:
					$error_level = \Monolog\Logger::INFO;
					break;
            }

            if($error_level > 0) {
				$app->logger->addRecord($error_level, $message, ['event' => 'php_register_shutdown_function']);
			}
        }
    });
}