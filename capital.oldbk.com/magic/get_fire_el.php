<?php
// new fsystem - Клонирование
if (!($_SESSION['uid'] >0)) header("Location: index.php");
// актуализируем данные пользователя
$user = mysql_fetch_array(mysql_query('SELECT * FROM `users` WHERE `id` = \''.$_SESSION['uid'].'\' LIMIT 1;'));
if ($user['battle'] == 0) {	echo "Это боевая магия..."; }
elseif ( ($data_battle['coment']=='<b>Бой с Пятницо</b>') OR ($data_battle['coment']=='<b>Бой защитников Кэпитал-сити</b>') ) { echo 'В этом бою нельзя использовать эту магию!'; }
elseif (($user['room'] >= 210) AND ($user['room'] <= 300))   {	echo "В турнирных боях нельзя призвать элементаль!"; }
else {

if ($user[battle] > 0) { $need_dress=' AND dressed=1 '; } else { $need_dress=''; }
//шансы сработки - 
$rowm = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `owner` = '{$_SESSION['uid']}' AND `id` = '{$_GET['use']}' ".$need_dress."  ;"));
if((int)$rowm['magic'] > 0)
{
	$magic = magicinf($rowm['magic']);
}
else
{
	$magic = magicinf($rowm['includemagic']);
}

if ($user['intel'] >= $rowm['nintel']) 
{
	$int = $magic['chanse'] + ($user['intel'] - $rowm['nintel']) * 3;
	if ($int > 98){ $int = 99; }
}
else 
{
	$int = 0;
}


	$clon_count=mysql_fetch_array(mysql_query("select * from battle_vars WHERE battle='".$user['battle']."' and owner='".$user['id']."' ORDER BY `colns_use` DESC LIMIT 1; "));
	
	if ($clon_count[colns_use] < 1000) 
              {    
		$rand = rand(1,100);
		if ($rand < $int) 
		{
		//делаем масив для бота 
		
			$bot_data=mysql_fetch_array(mysql_query("select * from users where id=85"));
			$bot_data[login]=$bot_data[login]." (Проекция ".($clon_count[colns_use]+1).")";

	
		// create prototype record at users_clons.
		$bots_items=load_mass_items_by_id($bot_data);
		//перебиваем значения нужные нам ставим - похер эти значения
		$bots_items['min_u']=0;
		$bots_items['max_u']=0;
		$bots_items['krit_mf']=0;
		$bots_items['akrit_mf']=0;
		$bots_items['uvor_mf']=0;
		$bots_items['auvor_mf']=0;
		
		$bots_items['bron1']=40;
		$bots_items['bron2']=40;
		$bots_items['bron3']=40;
		$bots_items['bron4']=40;
		
		$bots_items['ups']=5;
		
		$bots_items['allsumm']=3500; // сумма шмоток 
		
		// шмотка с которой снимать параметры
		$bot_data['r1']=15; // ид в базе
		
		
		mysql_query("INSERT INTO `users_clons` SET `login`='".$bot_data[login]."',`sex`='{$bot_data['sex']}',
					`level`='{$bot_data['level']}',`align`='{$bot_data['align']}',`klan`='{$bot_data['klan']}',`sila`='{$bot_data['sila']}',
					`lovk`='{$bot_data['lovk']}',`inta`='{$bot_data['inta']}',`vinos`='{$bot_data['vinos']}',
					`intel`='{$bot_data['intel']}',`mudra`='{$bot_data['mudra']}',`duh`='{$bot_data['duh']}',`bojes`='{$bot_data['bojes']}',`noj`='{$bot_data['noj']}',
					`mec`='{$bot_data['mec']}',`topor`='{$bot_data['topor']}',`dubina`='{$bot_data['dubina']}',`maxhp`='{$bot_data['maxhp']}',`hp`='{$bot_data['hp']}',
					`maxmana`='{$bot_data['maxmana']}',`mana`='{$bot_data['mana']}',`sergi`='{$bot_data['sergi']}',`kulon`='{$bot_data['kulon']}',`perchi`='{$bot_data['perchi']}',
					`weap`='{$bot_data['weap']}',`bron`='{$bot_data['bron']}',`r1`='{$bot_data['r1']}',`r2`='{$bot_data['r2']}',`r3`='{$bot_data['r3']}',`helm`='{$bot_data['helm']}',
					`shit`='{$bot_data['shit']}',`boots`='{$bot_data['boots']}',`nakidka`='{$bot_data['nakidka']}',`rubashka`='{$bot_data['rubashka']}',`shadow`='{$bot_data['shadow']}',`battle`='{$user['battle']}',`bot`=1,
					`id_user`='{$bot_data['id']}',`at_cost`='{$bots_items['allsumm']}',`kulak1`=0,`sum_minu`='{$bots_items['min_u']}',
					`sum_maxu`='{$bots_items['max_u']}',`sum_mfkrit`='{$bots_items['krit_mf']}',`sum_mfakrit`='{$bots_items['akrit_mf']}',
					`sum_mfuvorot`='{$bots_items['uvor_mf']}',`sum_mfauvorot`='{$bots_items['auvor_mf']}',`sum_bron1`='{$bots_items['bron1']}',
					`sum_bron2`='{$bots_items['bron2']}',`sum_bron3`='{$bots_items['bron3']}',`sum_bron4`='{$bots_items['bron4']}',`ups`='{$bots_items['ups']}',
					`injury_possible`=0, `battle_t`='{$user['battle_t']}' , `mklevel`='{$user[level]}' ;");
		$bot_data[id] = mysql_insert_id();
		$bot_data[battle]=$user['battle'];//шоб были данные

		$time = time();
		$ttt=$user[battle_t];
		
		if($user[hidden] > 0)
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
		
		//addlog($user['battle'],'<span class=date>'.date("H:i").'</span> '.nick_in_battle($user,$ttt).' призвал  '.nick_in_battle($bot_data,$ttt).'<BR>');

					if ($user[hidden]>0 and $user[hiddenlog]=='') 	{ $user[sex]=1;	}
					elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user); $user[sex]=$fuser[sex]; }
					$btext=str_replace(':','^',nick_in_battle($bot_data,$ttt));
			       	       addlog($user['battle'],"!:X:".time().':'.nick_new_in_battle($user).':'.($user[sex]+1020).":".$btext."\n");

		
		if ($data_battle[t3]!='')
		{
		mysql_query('UPDATE battle SET to1='.$time.', to2='.$time.', to3='.$time.', t'.$ttt.'=CONCAT(t'.$ttt.',\';'.$bot_data[id].'\') , t'.$ttt.'hist=CONCAT(t'.$ttt.'hist,\''.BNewHist($bot_data).'\')    WHERE id = '.$user['battle'].' ;');
		}
		else
		{
		mysql_query('UPDATE battle SET to1='.$time.', to2='.$time.', t'.$ttt.'=CONCAT(t'.$ttt.',\';'.$bot_data[id].'\') , t'.$ttt.'hist=CONCAT(t'.$ttt.'hist,\''.BNewHist($bot_data).'\')    WHERE id = '.$user['battle'].' ;');
		}
		
		//mysql_query("INSERT IGNORE INTO battle_dam_exp (battle,owner) VALUES ('{$user['battle']}','{$bot_data[id]}')");

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
		
		echo "Элементаль призвана";
		$MAGIC_OK=1;


	} else {
			echo "Свиток рассыпался в ваших руках...";
		
	} //rand
	
	} //clon_count
	else
	{
	echo "В этом бою, Вы неможете призвать больше элементалей...";
	}
}

?>
