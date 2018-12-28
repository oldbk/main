<?php
	require_once('memcache.php');

	header("Cache-Control: public, max-age=7200");

	if (isset($_GET['id'])) {
		$sql = 'SELECT img FROM dt_items WHERE id = '.intval($_GET['id']);
		$cache = getCache(md5("mysql_query".$sql));
		if ($cache !== FALSE) {
			header('Content-Type: image/gif');
			if (file_exists('/www/data/dtcache/'.$cache[0]['img'])) {
				$data = file_get_contents('/www/data/dtcache/'.$cache[0]['img']);
				echo $data;
			}
		} else {
			include "connect.php";
			$r = mysql_query($sql);
			if (($rows = mysql_num_rows($r)) !== 0) {
				for ($i=0;$i<$rows;$i++) {
					$fields = mysql_num_fields($r);
					$row = mysql_fetch_array($r);
					for ($j=0;$j<$fields;$j++) {
	                        		if ($i === 0) {
							$columns[$j] = mysql_field_name($r,$j);
						}
						$cache[$i][$columns[$j]] = $row[$j];
					}
				}
				if (setCache(md5("mysql_query".$sql),$cache,3*3600)) {
					header('Content-Type: image/gif');
					if (file_exists('/www/data/dtcache/'.$cache[0]['img'])) {
						$data = file_get_contents('/www/data/dtcache/'.$cache[0]['img']);
						echo $data;
					}
				} else {
					header('Content-Type: image/gif');
					if (file_exists('/www/data/dtcache/'.$cache[0]['img'])) {
						$data = file_get_contents('/www/data/dtcache/'.$cache[0]['img']);
						echo $data;
					}
				}
			}

		}
	}
?>