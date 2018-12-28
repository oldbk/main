<?php

define('PRODUCTION_MODE', $mode == 'production');
if (!PRODUCTION_MODE) require(__DIR__ . '/config/debugmode.php');

define("DS", DIRECTORY_SEPARATOR);
define('ROOT_DIR', realpath(__DIR__ . '/../'));
define('LOG_DIR', realpath(implode(DS, ['', 'www', 'logs', 'php'])));


require_once ROOT_DIR . '/vendor/autoload.php';
require_once ROOT_DIR . '/alg.php';
require_once ROOT_DIR . '/render_func.php';



$app = new \components\Component\Slim\Slim([
    'mode'              => $mode,
    'templates.path'    => ROOT_DIR . '/template',
    'view'              => '\components\Component\Slim\View',
    'log.writer'        => \components\Component\Slim\Log\MonologWriter::register(
        'oldbk-front',
        'critical',
        [
            'day'   => 30,
            'level' => 'debug'
        ],
        true,
        false,
        'log'
    )
]);


// Only invoked if mode is "production"
$app->configureMode('production', function () use ($app) {
    $app->config(require(__DIR__ . '/config/prod.php'));
});
$app->configureMode('development', function () use ($app) {
    $app->config(require(__DIR__ . '/config/dev.php'));
});
$app->configureMode('local', function () use ($app) {
    ini_set('default_charset', 'windows-1251');
    $app->config(require(__DIR__ . '/config/local.php'));
});


/**
 * Slims middleware
 */
if (!PRODUCTION_MODE && $app->config('debug')) {
    $app->add(new \Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware);
}
$app->add(new \components\Component\Slim\Middleware\Session\Session);
$app->add(new \components\Component\Slim\Middleware\ClientScript\ClientScriptRegister);

$uri = false;
if (php_sapi_name() !== 'cli') {
    $uri = urldecode(
        parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
    );
}


//тут временно костыль на библиотеку. юзает либу sorskod/db
if (strpos($uri, 'encicl') !== false) {
    $app->provider->addProviders([
            \components\Providers\ConfigServiceProvider::class,
            \components\Providers\CacheServiceProvider::class,
            \components\Providers\ConfigKoServiceProvider::class,
            \Illuminate\Filesystem\FilesystemServiceProvider::class,
        ]);

    $app->provider->addAliases([
        'Config'    => \Illuminate\Support\Facades\Config::class,
        'File'      => \Illuminate\Support\Facades\File::class,
        'Storage'   => \Illuminate\Support\Facades\Storage::class,
    ]);

    $db_config = $app->config('database.connections')['capital'];


    \database\DB::setConfig(array(
        'dsn'       => sprintf("mysql:host=%s;dbname=%s;charset=%s", $db_config['host'], $db_config['database'], $db_config['charset']),
        'username'  => $db_config['username'],
        'password'  => $db_config['password'],
    ), \components\Model\AbstractCapitalModel::connectionName());

    $app->container->singleton('db', function () {
        $db = \database\DB::getInstance(\components\Model\AbstractCapitalModel::connectionName());
        $db->execQueryString('SET time_zone = "+3:00";');
        $db->execQueryString('SET NAMES CP1251;');
        return $db;
    });

    $app->hook('slim.before', function () use ($app) {
        \components\Model\Settings::getAll();
    }, 10);

}
else {
    /**
     * Set up app providers and aliases
     */
    $app->provider->addProviders($app->config('providers'));
    $app->provider->addAliases($app->config('aliases'));

    $app->hook('slim.before', function () use ($app) {
        \components\Eloquent\Settings::all();
    }, 10);
}

$app->phpDebugBar();
$app->loggingError();