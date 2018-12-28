<?php
/**
 * Created by PhpStorm.
 * User: nnikitchenko
 * Date: 08.05.2016
 */

require_once __DIR__ . '/../vendor/autoload.php';

$jobby = new \Jobby\Jobby();

$jobby->add('quest_check_finish', array(
    'command' => '/usr/bin/php /www/capitalcity.oldbk.com/components/cli.php quest checkFinish',
    'schedule' => '*/5 * * * *',
    'output' => 'tmp/jobby/quest_check_finish.log',
    'enabled' => true,
));

$jobby->run();