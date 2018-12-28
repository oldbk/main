<?php
	function CronIsWork($name) {
		$out = array();
		$t = system('ps aux',$out);
		$i = 0;
		print_r($out);
		while(list($k,$v) = each($out)) {
			if (strpos($v,$name) !== false) {
				$i++;
			}
		}
		if ($i == 2) return true;
		return false;
	}

	if (!isset($_GET['key']) || $_GET['key'] != "245789vyu54ymureiw4gjinsgerkjnkj3e5r") die();

	switch($_GET['mode']) {
		case "arch":
			if (CronIsWork('cron_dt_arch')) {
				// делаем файл
				$fp = fopen('./cron/cron_dtarch_stop','w+');
				fclose($fp);
			} else {
				// проверяем если есть лок - прибиваем
				if (file_exists('./../capitalcity_lock/cron_dt_arch.lock')) {
					rmdir('./../capitalcity_lock/cron_dt_arch.lock');
				}
			}
		break;
		case "map":
			if (CronIsWork('cron_map')) {
				// делаем файл
				$fp = fopen('./cron/cron_map_stop','w+');
				fclose($fp);
			} else {
				// проверяем если есть лок - прибиваем
				if (file_exists('./../capitalcity_lock/cron_map.lock')) {
					rmdir('./../capitalcity_lock/cron_map.lock');
				}
			}
		break;
	}
?>