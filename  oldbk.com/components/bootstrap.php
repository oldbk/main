<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 19.10.2015
 */

define('ROOT_DIR', realpath(__DIR__ . '/../'));
define('PRODUCTION_MODE', true);

if (!PRODUCTION_MODE) require(__DIR__ . '/config/debugmode.php');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/ClassLoader.php';
use Symfony\Component\ClassLoader\ClassLoader;

$loader = new ClassLoader();
$loader->register();

$loader->addPrefix(false, realpath(__DIR__.'/../'));

$main_config = require(__DIR__ . '/config/prod.php');
