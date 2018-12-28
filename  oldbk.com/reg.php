<?

require_once __DIR__ . '/components/bootstrap_web.php';

$host = PRODUCTION_MODE ? \Config::get('url.oldbk') : \Config::get('url.oldbk') . ':81';

header('Location: '.$host.'/f/reg' . ($_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : ''));
die();