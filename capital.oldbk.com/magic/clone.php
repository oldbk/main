<?php
// new fsystem - Клонирование
if (!($_SESSION['uid'] >0)) header("Location: index.php");
// актуализируем данные пользователя
$user = mysql_fetch_array(mysql_query('SELECT * FROM `users` WHERE `id` = \''.$_SESSION['uid'].'\' LIMIT 1;'));
if ($user['battle'] == 0) {	echo "Это боевая магия..."; }
elseif ( ($data_battle['coment']=='<b>Бой с Пятницо</b>') OR ($data_battle['coment']=='<b>Бой защитников Кэпитал-сити</b>') ) { echo 'В этом бою нельзя использовать эту магию!'; }
elseif ($user['room'] == 200) {	echo "В турнирных боях нельзя пользоваться магией!"; }
elseif (($user['room'] >=210)AND($user['room'] <299)) {  echo "В турнирных боях нельзя пользоваться этой магией!"; }
else {

	if ($USE_HELPER>0) 
	{
	$int=102;
	}
	else
	{
		if ($user[battle] > 0) { $need_dress=' AND dressed=1 '; } else { $need_dress=''; }
		//шансы сработки - 
		$rowmm = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `owner` = '{$_SESSION['uid']}' AND `id` = '{$_GET['use']}' ".$need_dress."  ;"));
		if((int)$rowmm['magic'] > 0)
		{
		$magic = magicinf($rowmm['magic']);
		}
		else
		{
		$magic = magicinf($rowmm['includemagic']);
		}
	
		if ($user['intel'] >= $rowmm['nintel']) 
		{
		$int = $magic['chanse'] + ($user['intel'] - $rowmm['nintel']) * 3;
			if ($int > 98){ $int = 99; }
		}
		else 
		{
		$int = 0;
		}
	}

	$ckol=1;
	
	if ($user[id]==10) { $ckol=10; }
	

	for ($cb=1;$cb<=$ckol;$cb++)
	{
	$clon_count=mysql_fetch_array(mysql_query("select * from battle_vars WHERE battle='".$user['battle']."' and owner='".$user['id']."' ORDER BY `colns_use` DESC LIMIT 1; "));
	
	if (($clon_count[colns_use] < 10) or $user[id]==190672 || $user[id]==395467  || $user[id]==9|| $user[id]==10000 || $user[id]==10  || $user[id]==12 || $user[id]==3 || $user[id]==4 )
              {    
		$rand = rand(1,100);
		if ($rand < $int) 
		{
		//делаем масив для бота шоб не перечитывать из базы
			$bot_data=$user;
			if (($user[hidden]>0) and ($user[hiddenlog]==''))
			{
			 // для невидимоск в бою общий счетчик по этому овнер = 0
			$clon_count=mysql_fetch_array(mysql_query("select * from battle_vars WHERE battle='".$user['battle']."' and owner='0' ORDER BY `colns_use` DESC LIMIT 1; "));
			$bot_data['login']="Невидимка (клон ".($clon_count[colns_use]+1).")";
			$bot_data['level'] = "??";
			$bot_data['align']=0;
			$bot_data['klan']="";
			unset ($bot_data[hidden]); // эта переменная не нужна для клонов		
			}
			else
			{
			
			$bot_data=load_perevopl($user);
			
			unset($bot_data[hiddenlog]);//fix
			unset($bot_data[hidden]);//fix			
			
			$bot_data['login']=$bot_data['login']." (клон ".($clon_count[colns_use]+1).")";
			}
	
		// create prototype record at users_clons.
		$bots_items=load_mass_items_by_id($user);
		

		if ( ($user['in_tower']==0) AND ($user['lab']==0) and (!(($user['room']>=211 and $user['room']<240) or ($user['room']>240 and $user['room']<270) or ($user['room']>270 and $user['room']<290)))   )
			{
			//если не в локах и не лабе класс=юзеру
			$user_uclass=$user['uclass'];
			}
			else
			{
			//нет класса у клона юзера
			$user_uclass=0;
			}
		
		
		mysql_query("INSERT INTO `users_clons` SET `login`='".$bot_data['login']."',`sex`='{$bot_data['sex']}',
					`level`='{$user['level']}',`align`='{$bot_data['align']}',`klan`='{$bot_data['klan']}',`sila`='{$user['sila']}',
					`lovk`='{$user['lovk']}',`inta`='{$user['inta']}',`vinos`='{$user['vinos']}',
					`intel`='{$user['intel']}',`mudra`='{$user['mudra']}',`duh`='{$user['duh']}',`bojes`='{$user['bojes']}',`noj`='{$user['noj']}',
					`mec`='{$user['mec']}',`topor`='{$user['topor']}',`dubina`='{$user['dubina']}',`maxhp`='{$user['maxhp']}',`hp`='{$user['hp']}',
					`maxmana`='{$user['maxmana']}',`mana`='{$user['mana']}',`sergi`='{$user['sergi']}',`kulon`='{$user['kulon']}',`perchi`='{$user['perchi']}',
					`weap`='{$user['weap']}',`bron`='{$user['bron']}',`r1`='{$user['r1']}',`r2`='{$user['r2']}',`r3`='{$user['r3']}',`helm`='{$user['helm']}',
					`shit`='{$user['shit']}',`boots`='{$user['boots']}',`nakidka`='{$user['nakidka']}',`rubashka`='{$user['rubashka']}',`shadow`='{$user['shadow']}',`battle`='{$user['battle']}',`bot`=1,
					`id_user`='{$user['id']}',`at_cost`='{$bots_items['allsumm']}',`kulak1`=0,`sum_minu`='{$bots_items['min_u']}',
					`sum_maxu`='{$bots_items['max_u']}',`sum_mfkrit`='{$bots_items['krit_mf']}',`sum_mfakrit`='{$bots_items['akrit_mf']}',
					`sum_mfuvorot`='{$bots_items['uvor_mf']}',`sum_mfauvorot`='{$bots_items['auvor_mf']}',`sum_bron1`='{$bots_items['bron1']}',
					`sum_bron2`='{$bots_items['bron2']}',`sum_bron3`='{$bots_items['bron3']}',`sum_bron4`='{$bots_items['bron4']}',`ups`='{$bots_items['ups']}',  `hiddenlog`='{$user[hiddenlog]}' ,  `uclass`='{$user_uclass}',  
					`injury_possible`=0, `battle_t`='{$user['battle_t']}' ;");
		$bot_data[id] = mysql_insert_id();

		$time = time();
		$ttt=$user[battle_t];
		
		if ( ($user[hidden] > 0) and ($user[hiddenlog] ==''))
		{
		//добавив в счетчик овнер 0 у невидимы
		//mysql_query("update battle_vars SET `colns_use`=`colns_use`+1 WHERE battle='".$user['battle']."' and (owner='0' OR owner='".$user[id]."')");
		mysql_query("INSERT `battle_vars` (`battle`,`owner`,`update_time`,`colns_use`) values('".$user['battle']."', '".$user['id']."', '".time()."' , '1' ) ON DUPLICATE KEY UPDATE `colns_use` =`colns_use`+1;");
		mysql_query("INSERT `battle_vars` (`battle`,`owner`,`update_time`,`colns_use`) values('".$user['battle']."', '0', '".time()."' , '1' ) ON DUPLICATE KEY UPDATE `colns_use` =`colns_use`+1;");
		}
		else
		{
		//mysql_query("update battle_vars SET `colns_use`=`colns_use`+1 WHERE battle='".$user['battle']."' and owner='".$user[id]."' ");
		mysql_query("INSERT `battle_vars` (`battle`,`owner`,`update_time`,`colns_use`) values('".$user['battle']."', '".$user['id']."', '".time()."' , '1' ) ON DUPLICATE KEY UPDATE `colns_use` =`colns_use`+1;");
		}
		
		if ($user[hidden]>0 and $user[hiddenlog]=='') 	{ $user[sex]=1;	}
		elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user); $user[sex]=$fuser[sex]; }
		$btext=str_replace(':','^',nick_in_battle($bot_data,$ttt));
       	       addlog($user['battle'],"!:X:".time().':'.nick_new_in_battle($user).':'.($user[sex]+300).":".$btext."\n");



		if ($data_battle[t3]!='')
		{
		mysql_query('UPDATE battle SET to1='.$time.', to2='.$time.', to3='.$time.' , t'.$ttt.'=CONCAT(t'.$ttt.',\';'.$bot_data[id].'\') , t'.$ttt.'hist=CONCAT(t'.$ttt.'hist,\''.BNewHist($bot_data).'\')    WHERE id = '.$user['battle'].' ;');
		}
		else
		{
		mysql_query('UPDATE battle SET to1='.$time.', to2='.$time.', t'.$ttt.'=CONCAT(t'.$ttt.',\';'.$bot_data[id].'\') , t'.$ttt.'hist=CONCAT(t'.$ttt.'hist,\''.BNewHist($bot_data).'\')    WHERE id = '.$user['battle'].' ;');		
		}
		


		$bet=1;
		$sbet = 1;
		// добавляем в мемори данные
		if ($user[battle_t]==1)
		  {
		  $boec_t1[$bot_data[id]]=$bot_data;
		  // обновляем данные	
         	  $data_battle[t1].=";".$bot_data[id];
		  }
		  elseif ($user[battle_t]==2)
		  {
		  $boec_t2[$bot_data[id]]=$bot_data;
  		  // обновляем данные	
         	  $data_battle[t2].=";".$bot_data[id];
		  }
		  elseif ($user[battle_t]==3)
		  {
		  $boec_t3[$bot_data[id]]=$bot_data;
  		  // обновляем данные	
         	  $data_battle[t3].=";".$bot_data[id];
		  }		  
		
		echo "Клон создан";
		$MAGIC_OK=1;


	} else {
			echo "Свиток рассыпался в ваших руках...";
		
	} //rand
	
	} //clon_count
	else
	{
	echo "В этом бою, Вы не можете выпустить больше клонов...";
	}
	
	}
	
}

?>
