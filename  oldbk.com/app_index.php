<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 18.11.2015
 */
require_once __DIR__ . '/components/bootstrap_web.php';

$app->group('/action', function () use ($app) {
    $app->map('/like/:action', function ($action) use ($app) {
        new \components\Controller\LikeController($app, $action);
    })->via('GET', 'POST')->name('like');
});

$app->run();
