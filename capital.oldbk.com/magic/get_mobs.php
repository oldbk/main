<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
$ituse=(int)$_GET['use'];

if ($user['battle'] == 0) 
{	
echo "Это боевая магия..."; 
}
elseif($user[hp]<=0) {      err('Для Вас бой окончен!');        }
elseif ( ($data_battle['coment']=='<b>Бой с Пятницо</b>') OR ($data_battle['coment']=='<b>Бой защитников Кэпитал-сити</b>') ) { echo 'В этом бою нельзя использовать эту магию!'; }
elseif (($user['room'] >= 210) AND ($user['room'] <= 300))   
{
echo "В турнирных боях нельзя призвать монстров!"; 
}
else {

//шансы сработки 
$rowm = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`inventory` WHERE `owner` = '{$_SESSION['uid']}' AND `id` = '{$ituse}'   ;"));
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
	$int = $int * 0.9;
	if ($int > 98){ $int = 99; }
}
else 
{
	$int = 0;
}

	
	$kol_bot[14001][min]=1;
	$kol_bot[14001][max]=2;


	$kol_bot[14002][min]=2;
	$kol_bot[14002][max]=3;	

	
	$kol_bot[14003][min]=3;		$kol_bot[14033][min]=3;
	$kol_bot[14003][max]=4;		$kol_bot[14033][max]=4;


	$kol_bot[14004][min]=3;
	$kol_bot[14004][max]=4;
	
	$kol_bot[14005][min]=3;
	$kol_bot[14005][max]=4;	
	
	$bots_config[0]=array(90,91,92,93,94,95,95,100);
	$bots_config[1]=array(90,91,92,93,94,95,95,100);
	$bots_config[2]=array(90,91,92,93,94,95,95,100);
	$bots_config[3]=array(90,91,92,93,94,95,95,100);	
	$bots_config[4]=array(90,91,92,93,94,95,95,100);
	$bots_config[5]=array(90,91,92,93,94,95,95,100);
	$bots_config[6]=array(90,91,92,93,94,95,95,100);
	
	$bots_config[7]=array(90,90,91,91,92,93,94,95,95,100);
	$bots_config[8]=array(215,92,92,93,93,94,94,210,211,213,214);
	$bots_config[9]=array(216,217,218,211,213,213,214,214,212,219);	
	$bots_config[10]=array(226,225,214,214,219,219,220,223,222);	
	$bots_config[11]=array(220,223,222,224,224,227,228,229);	
	$bots_config[12]=array(220,223,222,224,224,227,228,229,230);	
	$bots_config[13]=array(220,223,222,224,224,227,228,229,230);		
	$bots_config[14]=array(220,223,222,224,224,227,228,229,230);			
	$bots_config[15]=array(220,223,222,224,224,227,228,229,230);			
	$bots_config[16]=array(220,223,222,224,224,227,228,229,230);			
	$bots_config[17]=array(220,223,222,224,224,227,228,229,230);			
	$bots_config[18]=array(220,223,222,224,224,227,228,229,230);			
	$bots_config[19]=array(220,223,222,224,224,227,228,229,230);			
	$bots_config[20]=array(220,223,222,224,224,227,228,229,230);			
	$bots_config[21]=array(271,279,268,278,269,262,264,266,272,277);			//220,223,222,224,224,227,228,229,230);			
	$bots_config[22]=array(220,223,222,224,224,227,228,229,230);	
	$bots_config[23]=array(220,223,222,224,224,227,228,229,230);		
	$bots_config[24]=array(220,223,222,224,224,227,228,229,230);		

	
	$cb_stop=mt_rand($kol_bot[$rowm[prototype]][min],$kol_bot[$rowm[prototype]][max]);	

	$clon_count=mysql_fetch_array(mysql_query("select * from battle_vars WHERE battle='".$user['battle']."' and owner='".$user['id']."' ORDER BY `bots_use` DESC LIMIT 1; "));
	
	if (($clon_count[bots_use] < 1000) or ($user['id']==9) or ($user['id']==3) or ($user['id']==4)  )
              {    
		$rand = rand(1,100);
		
		if ($user['id']==4) { $cb_stop=12; }
		if ($user['id']==3) { $cb_stop=12; }
		if ($user['id']==10) { $cb_stop=12; }

		if ($rand < $int) 
		{
		
	 for ($cb=1;$cb<=$cb_stop;$cb++)
	{			
		//делаем масив для бота 
		$rmb=mt_rand(0,(count($bots_config[$user[level]])-1) );
		
		$bot_data=mysql_fetch_array(mysql_query("select * from users where id={$bots_config[$user[level]][$rmb]} ;"));
		$bot_data[login]=$bot_data[login]." (".($clon_count[bots_use]+$cb).")";
		
	
		// create prototype record at users_clons.
		$bots_items=load_mass_items_by_id($bot_data);

$bots_items['allsumm']=$bots_items['allsumm']*0.4;//занижаем стоимость шмоток


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
					`injury_possible`=0, `battle_t`='{$user['battle_t']}', `mklevel`='{$user[level]}' ;");
					
		$bot_data[id] = mysql_insert_id();
		$time = time();
		$ttt=$user[battle_t];

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


			$temp_bot_name = BNewHist($bot_data);					
			$temp_bot_namea = nick_align_klan($bot_data);

			if ($cb==1) {
				if (($cb>=($cb_stop)) and ($cb>1)) { $ptex=" и "; } else { $ptex=""; }
				$all_bots_namea=$ptex.$temp_bot_namea;
				$all_bots_id=$bot_data[id];	
				$all_bots_hist=$temp_bot_name;
			} else {
				if (($cb>=($cb_stop)) and ($cb>1)) { $ptex=" и "; } else { $ptex=", "; }			
				$all_bots_namea.=$ptex.$temp_bot_namea;
				$all_bots_id.=';'.$bot_data[id];	
				$all_bots_hist.= $temp_bot_name;		
			}

	}

		mysql_query("INSERT `battle_vars` (`battle`,`owner`,`update_time`,`bots_use`) values('".$user['battle']."', '".$user['id']."', '".time()."' , '{$cb}' ) ON DUPLICATE KEY UPDATE `bots_use` =`bots_use`+{$cb};");


//		addlog($user['battle'],'<span class=date>'.date("H:i").'</span> '.nick_in_battle($user,$ttt).' призвал  '.$all_bots_namea.'<BR>');

					if ($user[hidden]>0 and $user[hiddenlog]=='') 	{ $user[sex]=1;	}
					elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user); $user[sex]=$fuser[sex]; }
					$btext=str_replace(':','^',$all_bots_namea);
			       	       addlog($user['battle'],"!:X:".time().':'.nick_new_in_battle($user).':'.($user[sex]+1020).":".trim($btext)."\n");
		

		if ($data_battle[t3]!='')
		{
		mysql_query('UPDATE battle SET to1='.$time.', to2='.$time.', to3='.$time.', t'.$ttt.'=CONCAT(t'.$ttt.',\';'.$all_bots_id.'\') , t'.$ttt.'hist=CONCAT(t'.$ttt.'hist,\''.$all_bots_hist.'\')    WHERE id = '.$user['battle'].' ;');		
		}
		else
		{
		mysql_query('UPDATE battle SET to1='.$time.', to2='.$time.', t'.$ttt.'=CONCAT(t'.$ttt.',\';'.$all_bots_id.'\') , t'.$ttt.'hist=CONCAT(t'.$ttt.'hist,\''.$all_bots_hist.'\')    WHERE id = '.$user['battle'].' ;');
		}


		
		$bet=1;
		$sbet = 1;

		
		echo "Удачно использован свиток  \"{$rowm[name]}\"  ";
		$MAGIC_OK=1;


	} 
	else 
	{
			echo "Свиток рассыпался в ваших руках...";
		
	} //rand
	
	} //clon_count
	else
	{
	echo "В этом бою, Вы неможете призвать больше монстров...";
	}
}

?>
