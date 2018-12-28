<?php
	global $memcache;
	$memcache = new Memcache;
	$memcache->addServer("127.0.0.1");

	function getCache($key) {
        	global $memcache;
        	return ($memcache) ? $memcache->get($key) : false;
	}

    	function setCache($key,$object,$timeout = 60) {
        	global $memcache;
        	return ($memcache) ? $memcache->set($key,$object,0,$timeout) : false;
    	}

    	function mysql_query_cache($sql,$linkIdentifier = false,$timeout = 180) {
		$cache = getCache(md5("mysql_query" . $sql));
		if ($cache === false) {
			$r = ($linkIdentifier !== false) ? mysql_query($sql,$linkIdentifier) : mysql_query($sql);
			if (!is_resource($r) && !is_object($r)) return false;

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
				if (!setCache(md5("mysql_query" . $sql),$cache,$timeout)) {
	                    		return $cache;
				}
			} else {
				return array();
			}
		}
		return $cache;
	}

	function GetMCacheFromQuery($r) {
		$cache = array();
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
                    	return $cache;
		}
		return array();
	}

?>
