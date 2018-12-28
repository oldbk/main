<?php
	$data = file_get_contents('https://www.dan.me.uk/torlist/');
	if ($data !== FALSE && strlen($data) > 0) {
		file_put_contents('./data',$data);
	}
?>