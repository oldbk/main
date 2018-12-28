#!/usr/bin/env php
<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 08.06.2016
 */

require_once __DIR__ . '/bootstrap_cli.php';

// convert all the command line arguments into a URL
$argv = $GLOBALS['argv'];
array_shift($GLOBALS['argv']);
$pathInfo = '/' . implode('/', $argv);

// Set up the environment so that Slim can route
$app->environment = Slim\Environment::mock(array(
    'PATH_INFO'   => $pathInfo
));

// CLI-compatible not found error handler
$app->notFound(function () use ($app) {
    $url = $app->environment['PATH_INFO'];
    echo "Error: Cannot route to $url";
    $app->stop();
});

// Format errors for CLI
$app->error(function (\Exception $e) use ($app) {
    echo $e;
    $app->stop();
});

$app->get('/quest/:action', function ($action) use ($app) {
    new \components\Command\QuestCommand($app, $action);
});

$app->run();