<?php
	session_start();
	include "connect.php";
	include "functions.php";

	if (!ADMIN) die();

	include "ruines_config.php";

	$src='http://i.oldbk.com/i/sh/';
	$dest = '/www/ruinescache/';

	while(list($k,$v) = each($ritems)) {
		$q = mysql_query('SELECT * FROM oldbk.shop WHERE id = '.$k);
		if ($q !== FALSE) {
			$data = mysql_fetch_assoc($q);
			if ($data !== FALSE) {
				$filename = $src.$data['img'];
				$img = file_get_contents($filename);
				file_put_contents($dest.$data['img'],$img);
			}
		}
	}
?>