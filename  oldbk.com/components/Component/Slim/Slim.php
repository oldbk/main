<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 18.11.2015
 */
namespace components\Component\Slim;


use components\Component\ConfigKo;
use components\Component\CustomPdoCollector;
use components\Component\Slim\Middleware\Session\Helper;
use components\Component\Slim\ServiceProvider\ServiceProvider;
use phpFastCache\Core\DriverAbstract;

/**
 * Class Slim
 * @package components\Component\Slim
 * @property \database\DB|\Illuminate\Database\DatabaseManager $db
 * @property \components\Component\Slim\Middleware\Session\Helper $session
 * @property \components\Component\Slim\Middleware\ClientScript\ClientScript $clientScript
 * @property array $ruine
 * @property DriverAbstract $cache
 * @property ConfigKo $configKo
 * @property Request  $request
 * @property ServiceProvider  $provider
 */
class Slim extends \Slim\Slim
{
    public function __construct(array $userSettings = array())
    {

        parent::__construct($userSettings);

        // Default environment
        $this->container->singleton('environment', function ($c) {
            $env = \Slim\Environment::getInstance();
            $env['slim.tests.ignore_multibyte'] = true;
            return $env;
        });


        // Default request
        $this->container->singleton('request', function ($c) {
            return new Request($c['environment']);
        });

        $this->container->singleton('router', function ($c) {
            return new Router();
        });

        $this->container->singleton('session', function ($c) {
            //quick fix
            if (!session_id()) {
                session_start();
            }

            return new Helper();
        });

        /**
         * Service provider через него можно регистрировать компоненты Illuminate и прочие
         */
        $this->container->singleton('provider', function ($c) {
            return new ServiceProvider($this);
        });

        // Default log writer
        $this->container->singleton('logWriter', function ($c) {
            $logWriter = $c['settings']['log.writer'];

            return is_object($logWriter) ? $logWriter : new \Slim\LogWriter($c['environment']['slim.errors']);
        });

    }

    protected function mapRoute($args)
    {
        $pattern = array_shift($args);
        $callable = array_pop($args);
        $route = new Route($pattern, $callable, $this->settings['routes.case_sensitive']);
        $this->router->map($route);
        if (count($args) > 0) {
            $route->setMiddleware($args);
        }

        return $route;
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

    /**
     * @param string $route
     * @param array $params
     * @param int $status
     */
    public function redirectTo($route, $params = array(), $status = 302){
        $this->redirect($this->urlFor($route, $params), $status);
    }

    /**
     * @param $url
     * @param int $status
     */
    public function redirect2($url, $status = 302)
    {
        header('Location: '.$url, true, $status);
        exit;
    }

    /**
     * @param $error
     * @param bool $redirect
     */
    public function redirectWithError($error, $redirect = false)
    {
        $this->flash('noty', [
            'type' => 'error',
            'msg' => $error,
        ]);

        $this->redirect($redirect !== false ? $redirect : $this->request->getReferer());
    }

    /**
     * @return \components\Component\Slim\Log\MonologWriter
     */
    public function getWriter()
    {
        return $this->getLog()->getWriter();
    }

    /**
     * @return \Monolog\Logger
     */
    public function getMonolog()
    {
        return $this->getWriter()->getMonolog();
    }

    /**
     *
     */
    public function loggingError()
    {
        if($this->config('log.enabled'))
        {
            $this->customSlimError();

            $handler = new \Monolog\ErrorHandler($this->getMonolog());
            $handler->registerFatalHandler();
        }
    }

    /**
     *
     */
    public function customSlimError()
    {
        $this->error(function ($e) {

            $logId = '';

            try {

                $writer = $this->getWriter();

                if (!$writer instanceof \components\Component\Slim\Log\MonologWriter) {
                    throw new \Exception();
                }

                $monolog = $this->getMonolog();

                foreach ($monolog->getProcessors() as $processor) {
                    if ($processor instanceof \Monolog\Processor\UidProcessor) {
                        $logId = $processor->getUid();
                        break;
                    }
                }

                $level = $writer->slim2writer($e instanceof \Exception ? $e->getCode() : \Slim\Log::DEBUG);

            } catch (\Exception $ex) {
                $level = 'debug';
            }

            $this->getLog()->{$level}($e);

            $this->render('common/error',compact('logId'));

        });
    }

    /**
     * @throws \DebugBar\DebugBarException
     */
    public function phpDebugBar()
    {
        if ($this->config('mode') !== 'production' && $this->config('debug')) {

            $debugbar = new \Slim\Middleware\DebugBar();

            $debugbar->addCollector(
                new CustomPdoCollector($this->getTraceablePdo(), $this->config('database.default'))
            );
            $this->add($debugbar);

        }
    }

    /**
     * @return mixed
     */
    private function getTraceablePdo()
    {
        return $this->db instanceof \Illuminate\Database\DatabaseManager
            ? new \DebugBar\DataCollector\PDO\TraceablePDO($this->db->connection($this->config('database.default'))->getPdo())
            : new \DebugBar\DataCollector\PDO\TraceablePDO($this->db);
    }
}