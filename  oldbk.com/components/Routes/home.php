<?php

$app->map(
    '/',
    function ($action = 'index') use ($app) {
        new \components\Controller\HomeController($app, $action);
    }
)->via('GET', 'POST')->name('home');

/**
 *  Главная
 */
$app->group('/f', function () use ($app) {
    $app->map(
        '/reminder',
        function () use ($app) {
            new \components\Controller\HomeController($app, 'reminder');
        }
    )->via('GET', 'POST')->name('reminder');

    $app->map(
        '/about',
        function () use ($app) {
            new \components\Controller\HomeController($app, 'about');
        }
    )->via('GET')->name('about');

    $app->map(
        '/help',
        function () use ($app) {
            new \components\Controller\HomeController($app, 'help');
        }
    )->via('GET')->name('help');

    $app->map(
        '/screen',
        function () use ($app) {
            new \components\Controller\HomeController($app, 'screen');
        }
    )->via('GET')->name('screen');

    /**
     * Routing for registration
     */
    $app->map(
        '/reg(/:action)',
        function ($action = 'index') use ($app) {
            new \components\Controller\auth\RegisterController($app, $action);
        }
    )->via('GET', 'POST')->name('registration');

    /**
     * Log In
     */
    $app->map('/login', function () use ($app) {
        new \components\Controller\auth\LoginController($app, 'login');
    })->via('GET', 'POST')->name('login');

    /**
     * Cache clear tools
     */
    $app->map('/cache(/:action)', function ($action = 'index') use ($app) {
        new \components\Controller\CacheController($app, $action);
    })->via('GET')->name('f_cache');


});