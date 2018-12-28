<?php
	$skin_id = $rowm['prototype']-550;

	if ($skin_id >= 1 && $skin_id <= 6) {
		$skin_id = 'ny2016_'.$skin_id.'_';
	} elseif ($skin_id == 7) {
		$skin_id = 'val1m_';
	} elseif ($skin_id == 8) {
		$skin_id = 'val1g_';
	} else {
		echo 'Îøèáêà...';
		return;
	}


	$effect = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$user['id']}'  and `type` = '301' LIMIT 1;")); 
	if (!$effect['id']) {			
		$add_time_eff=time()+($magic['time']*60);

		mysql_query("INSERT INTO `effects` SET `type`= '301',`name`='Êàğíàâàëüíûé îáğàç',`time`='{$add_time_eff}',`owner`='{$user[id]}', add_info='".$skin_id."'");

		$bet=1;
		$sbet=1;
		$MAGIC_OK=1;
		echo 'Âû ïîäâåğãëèñü èëëşçèè.';
	} else {
		echo 'Ó âàñ óæå åñòü êàğíàâàëüíûé îáğàç.';
	}
	
?>