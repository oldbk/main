<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 08.06.2016
 */

$mode = 'production';
echo sprintf('Mode: '.$mode).PHP_EOL;

require_once __DIR__ . '/bootstrap_base.php';

echo sprintf('Require bootstrap_base : success').PHP_EOL;