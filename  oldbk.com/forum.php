<?

require_once __DIR__ . '/components/bootstrap_web.php';
$qrr = array();

$host = $mode == 'production' ? \Config::get('url.oldbk') : \Config::get('url.oldbk') . ':81';

header('Location: '.$host.'/forum/topic/forum.php' . ($_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : ''));
die();