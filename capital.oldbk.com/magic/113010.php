<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }


if ($user['battle'] == 0) 
{	
echo "Это боевая магия..."; 
}
elseif($user[hp]<=0) {      err('Для Вас бой окончен!');        }
elseif ($user['in_tower'] !=0 )
{
echo "Тут нельзя призвать монстров!"; 
}
elseif ($user['room'] ==90 )
{
echo "Тут нельзя призвать этого монстра!"; 
}
elseif (($user['room'] >= 210) AND ($user['room'] <= 300))   
{
echo "В турнирных боях нельзя призвать монстров!"; 
}
else {
	
	$bots_config=233;// id  бота


	$clon_count=mysql_fetch_array(mysql_query("select * from battle_vars WHERE battle='".$user['battle']."' and owner='".$user['id']."' ORDER BY `bots_use` DESC LIMIT 1; "));
	
	if (($clon_count[bots_use] < 1000) or ($user['id']==9))
        {
	$cb=1;
	{
		//делаем масив для бота 
		$bot_data=mysql_fetch_array(mysql_query("select * from users where id={$bots_config} ;"));
		$bot_data[login]=$bot_data[login]." (".($clon_count[bots_use]+$cb).")";
		/*$bot_data[login]=$bot_data[login];*/
		
	
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
		
		echo "Удачно призван <b>\"Морозный дух\"</b> ";
		$MAGIC_OK=1;

	} //clon_count
	else
	{
	echo "В этом бою, Вы неможете призвать больше монстров...";
	}
	
}

?>
