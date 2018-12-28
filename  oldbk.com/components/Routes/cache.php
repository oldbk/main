<?php

/**
 * Cache clear tools
 */
$app->map('/cache(/:action)', function ($action = 'index') use ($app) {
    new \components\Controller\CacheController($app, $action);
})->via('GET')->name('cache');