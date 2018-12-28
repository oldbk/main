<?php
// new fsystem -  лонирование
if (!($_SESSION['uid'] >0)) header("Location: index.php");
// актуализируем данные пользовател€
$user = mysql_fetch_array(mysql_query('SELECT * FROM `users` WHERE `id` = \''.$_SESSION['uid'].'\' LIMIT 1;'));
if ($user['battle'] == 0) {	echo "Ёто боева€ маги€..."; }
elseif ($user['room'] == 200) {	echo "¬ турнирных бо€х нельз€ пользоватьс€ магией!"; }
elseif (($user['room'] >=210)AND($user['room'] <299)) {  echo "¬ турнирных бо€х нельз€ пользоватьс€ этой магией!"; }
else {

if ($user[battle] > 0) { $need_dress=' AND dressed=1 '; } else { $need_dress=''; }
$int=101;
//шансы сработки - 


	$clon_count=mysql_fetch_array(mysql_query("select * from battle_vars WHERE battle='".$user['battle']."' and owner='".$user['id']."' ORDER BY `colns_use` DESC LIMIT 1; "));
	
	if ($clon_count[colns_use] < 10) 
              {    
		$rand = rand(1,100);
		if ($rand < $int) 
		{
		//делаем масив дл€ бота шоб не перечитывать из базы
			$bot_data=$user;
			if($user[hidden]>0)
			{
			 // дл€ невидимоск в бою общий счетчик по этому овнер = 0
			$clon_count=mysql_fetch_array(mysql_query("select * from battle_vars WHERE battle='".$user['battle']."' and owner='0' ORDER BY `colns_use` DESC LIMIT 1; "));
			$bot_data[login]="Ќевидимый ƒемон ".($clon_count[colns_use]+1)."";
			$bot_data['level'] = "??";
			$bot_data['align']=0;
			$bot_data['klan']="";
			unset ($bot_data[hidden]); // эта переменна€ не нужна дл€ клонов		
			}
			else
			{
			//$bot_data[login]=$user['login']." (клон ".($clon_count[colns_use]+1).")";
			$bot_data[login]="ƒемон ".($clon_count[colns_use]+1)."";
			}
	
		// create prototype record at users_clons.
		$bots_items=load_mass_items_by_id($user);
		mysql_query("INSERT INTO `users_clons` SET `login`='".$bot_data[login]."',`sex`='{$user['sex']}',
					`level`='{$user['level']}',`align`='{$user['align']}',`klan`='{$user['klan']}',`sila`='{$user['sila']}',
					`lovk`='{$user['lovk']}',`inta`='{$user['inta']}',`vinos`='{$user['vinos']}',
					`intel`='{$user['intel']}',`mudra`='{$user['mudra']}',`duh`='{$user['duh']}',`bojes`='{$user['bojes']}',`noj`='{$user['noj']}',
					`mec`='{$user['mec']}',`topor`='{$user['topor']}',`dubina`='{$user['dubina']}',`maxhp`='{$user['maxhp']}',`hp`='{$user['hp']}',
					`maxmana`='{$user['maxmana']}',`mana`='{$user['mana']}',`sergi`='{$user['sergi']}',`kulon`='{$user['kulon']}',`perchi`='{$user['perchi']}',
					`weap`='{$user['weap']}',`bron`='{$user['bron']}',`r1`='{$user['r1']}',`r2`='{$user['r2']}',`r3`='{$user['r3']}',`helm`='{$user['helm']}',
					`shit`='{$user['shit']}',`boots`='{$user['boots']}',`nakidka`='{$user['nakidka']}',`rubashka`='{$user['rubashka']}',`shadow`='{$user['shadow']}',`battle`='{$user['battle']}',`bot`=1,
					`id_user`='{$user['id']}',`at_cost`='{$bots_items['allsumm']}',`kulak1`=0,`sum_minu`='{$bots_items['min_u']}',
					`sum_maxu`='{$bots_items['max_u']}',`sum_mfkrit`='{$bots_items['krit_mf']}',`sum_mfakrit`='{$bots_items['akrit_mf']}',
					`sum_mfuvorot`='{$bots_items['uvor_mf']}',`sum_mfauvorot`='{$bots_items['auvor_mf']}',`sum_bron1`='{$bots_items['bron1']}',
					`sum_bron2`='{$bots_items['bron2']}',`sum_bron3`='{$bots_items['bron3']}',`sum_bron4`='{$bots_items['bron4']}',`ups`='{$bots_items['ups']}',  `uclass`='{$user['uclass']}' ,
					`injury_possible`=0, `battle_t`='{$user['battle_t']}';");
		$bot_data[id] = mysql_insert_id();

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
		
//		addlog($user['battle'],'<span class=date>'.date("H:i").'</span> '.nick_in_battle($user,$ttt).' выпустил на волю '.nick_in_battle($bot_data,$ttt).'<BR>');
		
				if ($user[hidden]>0 and $user[hiddenlog]=='') 	{ $user[sex]=1;	}
				elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user); $user[sex]=$fuser[sex]; }
				$btext=str_replace(':','^',nick_in_battle($bot_data,$ttt));
		       	       addlog($user['battle'],"!:X:".time().':'.nick_new_in_battle($user).':'.($user[sex]+1030).":".$btext."\n");
		
		

		if ($data_battle[t3]!='')
		{
		mysql_query('UPDATE battle SET to1='.$time.', to2='.$time.', to3='.$time.', t'.$ttt.'=CONCAT(t'.$ttt.',\';'.$bot_data[id].'\') , t'.$ttt.'hist=CONCAT(t'.$ttt.'hist,\','.nick_hist($bot_data).'\')    WHERE id = '.$user['battle'].' ;');		
		}
		else
		{
		mysql_query('UPDATE battle SET to1='.$time.', to2='.$time.', t'.$ttt.'=CONCAT(t'.$ttt.',\';'.$bot_data[id].'\') , t'.$ttt.'hist=CONCAT(t'.$ttt.'hist,\','.nick_hist($bot_data).'\')    WHERE id = '.$user['battle'].' ;');
		}
		
		//mysql_query("INSERT IGNORE INTO battle_dam_exp (battle,owner) VALUES ('{$user['battle']}','{$bot_data[id]}')");

		$bet=1;
		$sbet = 1;
		// добавл€ем в мемори данные
		if ($user[battle_t]==1)
		  {
		  $boec_t1[$bot_data[id]]=$bot_data;
		  // обновл€ем данные	
         	  $data_battle[t1].=";".$bot_data[id];
		  }
		  elseif ($user[battle_t]==2)
		  {
		  $boec_t2[$bot_data[id]]=$bot_data;
  		  // обновл€ем данные	
         	  $data_battle[t2].=";".$bot_data[id];
		  }
		  elseif ($user[battle_t]==3)
		  {
		  $boec_t3[$bot_data[id]]=$bot_data;
  		  // обновл€ем данные	
         	  $data_battle[t3].=";".$bot_data[id];
		  }		
		
		echo " лон создан";
		$MAGIC_OK=1;


	} else {
			echo "—виток рассыпалс€ в ваших руках...";
		
	} //rand
	
	} //clon_count
	else
	{
	echo "¬ этом бою, ¬ы неможете выпустить больше клонов...";
	}
}

?>
