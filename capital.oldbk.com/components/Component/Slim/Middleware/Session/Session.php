<?php
namespace components\Component\Slim\Middleware\Session;
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 18.11.2015
 */
class Session extends \Slim\Middleware
{
    /**
     * @var array
     */
    protected $settings;
    /**
     * Constructor
     *
     * @param array $settings
     */
    public function __construct($settings = array())
    {
        $defaults = array(
            'lifetime' => 0,
            'path' => '/',
            'domain' => '.oldbk.com',
            'secure' => false,
            'httponly' => false,
            'name' => 'PHPSESSID',
            'autorefresh' => false
        );
        $settings = array_merge($defaults, $settings);
        if (is_string($lifetime = $settings['lifetime'])) {
            $settings['lifetime'] = strtotime($lifetime) - time();
        }
        $this->settings = $settings;
        ini_set('session.gc_probability', 1);
        ini_set('session.gc_divisor', 1);
        ini_set('session.gc_maxlifetime', 30 * 24 * 60 * 60);
    }
    /**
     * Call
     */
    public function call()
    {
        $this->registerHelper();
        $this->startSession();
        $this->next->call();
    }
    /**
     * Register helper
     *
     * It registers a session helper singleton to $app->session, so you can use
     * that to manage sessions or instantiate the helper class for yourself.
     */
    protected function registerHelper()
    {
        $this->app->container->singleton('session', function () {
            return new Helper();
        });
    }
    /**
     * Start session
     */
    protected function startSession()
    {
        if (session_id()) {
            return;
        }
        $settings = $this->settings;
        $name = $settings['name'];
        /*(session_set_cookie_params(
            $settings['lifetime'],
            $settings['path'],
            $settings['domain'],
            $settings['secure'],
            $settings['httponly']
        );
        session_name($name);
        session_cache_limiter(false);*/
        session_start();
        /*if ($settings['autorefresh'] && isset($_COOKIE[$name])) {
            setcookie(
                $name,
                $_COOKIE[$name],
                time() + $settings['lifetime'],
                $settings['path'],
                $settings['domain'],
                $settings['secure'],
                $settings['httponly']
            );
        }*/
    }
}