<?php

	$to = mysql_fetch_assoc(mysql_query('SELECT * FROM users WHERE login = "'.$_POST['target'].'" and room = '.$user['room'].' and id != '.$user['id']));

	if ($to) {
		$sex[0] = "кинула";
		$sex[1] = "кинул";

		$mesta = array("корпус","голову","лоб","пах","ноги","нос","глаз");

		$mesto = $mesta[mt_rand(0,count($mesta)-1)];

		addch('<img src=i/magic/tomat.gif>Персонаж &quot;<b>'.$user['login'].'</b>&quot; '.$sex[$user['sex']].' помидором в &quot;<b>'.$to['login'].'</b>&quot;, попав прямо в '.$mesto.'!') or die();
		addchp ('<font color=red>Внимание!</font> Персонаж &quot;<b>'.$user['login'].'</b>&quot; попал в вас помидором. Получен эффект: Первоапрельский образ','{[]}'.$to['login'].'{[]}',$to['room'],$to['id_city']);


		$add_time_eff=time()+($magic['time']*60);

		$female_obraz = array(1,3,6);
		$male_obraz = array(2,4,5,7,8,9);

		if ($to['sex'] == 0) {
			$skin_id = '1april_obrazevent_0'.$female_obraz[mt_rand(0,count($female_obraz)-1)];
		} else {
			$skin_id = '1april_obrazevent_0'.$male_obraz[mt_rand(0,count($male_obraz)-1)];
		}

		mysql_query('DELETE FROM effects WHERE owner = '.$to['id'].' and type = 301 LIMIT 1');
		mysql_query("INSERT INTO `effects` SET `type`= '301',`name`='Уникальный образ, полученный в ходе события «Неделя веселья»',`time`='{$add_time_eff}',`owner`='{$to[id]}', add_info='".$skin_id."'");

		$bet=1;
		$sbet=1;
		$MAGIC_OK=1;
		echo 'Вы использовали помидор.';

        try {
            global $app;
            $User = new \components\models\User($user);
            $Quest = $app->quest
                ->setUser($User)
                ->get();
            $Checker = new \components\Component\Quests\check\CheckerMagic();
            $Checker->magic_id = 300413;
            if(($Item = $Quest->isNeed($Checker)) !== false) {
                $Quest->taskUp($Item);
            }
        } catch (Exception $ex) {
            \components\Helper\FileHelper::writeArray(array(
                'magic' => '5276',
                'error' => $ex->getMessage(),
                'trace' => $ex->getTraceAsString()
            ), 'error_log');
        }

	} else {
		echo 'Персонаж не найден в этой комнате';
	}


/*



	$effect = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$user['id']}'  and `type` = '301' LIMIT 1;")); 
	if (!$effect['id']) {			
		$add_time_eff=time()+($magic['time']*60);

		mysql_query("INSERT INTO `effects` SET `type`= '301',`name`='Первоапрельский образ - Уникальный образ, полученный в ходе события «Неделя веселья»',`time`='{$add_time_eff}',`owner`='{$user[id]}', add_info='".$skin_id."'");

		$bet=1;
		$sbet=1;
		$MAGIC_OK=1;
		echo 'Вы подверглись иллюзии.';
	} else {
		echo 'У вас уже есть карнавальный образ.';
	}
	
*/
?>