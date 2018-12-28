<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.10.2015
 */

$mode = 'production';
if($_SERVER['SERVER_NAME'] == 'capitalcity.oldbk.local') {
    $mode = 'local';
} elseif($_SERVER['SERVER_NAME'] == 'capitalcity.oldbk.test' || file_exists(__DIR__.'/DEV_ENV')) {
    $mode = 'development';
}

require_once __DIR__ . '/bootstrap_base.php';

$app->add(new \components\Component\Slim\Middleware\Session\Session());
$app->add(new \components\Component\Slim\Middleware\ClientScript\ClientScriptRegister());

$app->container->singleton('bk_security', function () use ($app) {
	return new \components\Component\Security\TwoFA();
});

$app->hook('slim.after.router', function() use ($app, $mode) {
    if($mode != 'production') {
        $messages = $app->session->get('logger_message', array());
        $key = \Slim\Log::CRITICAL;
        if(isset($messages[$key])) {
            foreach ($messages[$key] as $message) {
                $app->log->critical($message);
            }
        }

        $app->session->delete('logger_message');
    }
});