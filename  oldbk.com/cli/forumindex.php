<?php

require_once __DIR__  . '/../components/bootstrap_cli.php';

$params = \components\Component\Cli\CommandLine::parseArgs($argv);

new \components\IndexManager\IndexManager(
    $app,
    'create',
    [\components\IndexManager\ForumIndex::class, $params]
);