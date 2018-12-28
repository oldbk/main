<?

if (strpos($_SERVER['REQUEST_URI'], '/?/') !== false) {
	header('Location: '.str_replace('/?/', '/', $_SERVER['REQUEST_URI']), true, 301);
	die();
}

if (strpos($_SERVER['REQUEST_URI'], 'index.php') !== false) {
	$link = str_replace('/index.php', '', $_SERVER['REQUEST_URI']);
	if($link == '') { $link = '/'; }

	header('Location: '.$link, true, 301);
	die();
}

require_once __DIR__  . '/components/bootstrap_web.php';
require_once __DIR__  . '/components/routes.php';


$app->run();