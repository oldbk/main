<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
$ituse=(int)$_GET['use'];

if ($user['battle'] == 0) 
{	
echo "Это боевая магия..."; 
}
elseif($user[hp]<=0) {      err('Для Вас бой окончен!');        }
elseif (($user['room'] >= 210) AND ($user['room'] <= 300))   
{
echo "В турнирных боях нельзя захватывать монстров!"; 
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

	
	$kol_bot[15001][min]=1;
	$kol_bot[15001][max]=1;

	$kol_bot[15002][min]=1;
	$kol_bot[15002][max]=2;	
	
	$kol_bot[15003][min]=2;	
	$kol_bot[15003][max]=3;	
	
	$kol_bot[15000][min]=2;
	$kol_bot[15000][max]=3;	

	$kol_bot[15004][min]=2;
	$kol_bot[15004][max]=3;

	$kol_bot[15005][min]=2;	
	$kol_bot[15005][max]=3;		
			

	
	$cb_lim=mt_rand($kol_bot[$rowm[prototype]][min],$kol_bot[$rowm[prototype]][max]);	

//	if ($user[battle_t]==1)  { $en_team_n=2; } else {  $en_team_n=1; }
		
	$my_team_n=$user[battle_t];
	$bmmaxlvl=$user[level]+1;

	$bots_data=mysql_query("select * from users_clons where battle='{$user[battle]}' and battle_t!='{$user[battle_t]}' and hp>0 and mklevel>0 and id_user NOT IN (85,249,250,251,252,253,254,255,554,555,556,557,558,559,560,561,562,563,564,565,566,567,568,569,570) and mklevel<={$bmmaxlvl} ORDER BY RAND() LIMIT {$cb_lim} ");

	
	if (mysql_num_rows($bots_data)>0) 
              {    
		if ($rand < $int) 
		{
		$time = time();
		$all_bots_namea='';	
		$cc=0;			
		while ($bot = mysql_fetch_array($bots_data))
		{			
		$all_bots_namea.=($cc==0?"":", ").nick_align_klan($bot);
		$en_team_n=$bot[battle_t];
		
		mysql_query("UPDATE `users_clons` SET `battle_t` =".$user[battle_t]."  WHERE `id` = ".$bot['id']);
		
		if ($data_battle[t3]!='')
		{
		mysql_query("UPDATE battle SET to1=".$time.", to2=".$time.", to3=".$time.", t".$my_team_n."=CONCAT(t".$my_team_n.",';".$bot[id]."') , t".$my_team_n."hist=CONCAT(t".$my_team_n."hist,'".BNewHist($bot)."') ,  t".$en_team_n."=REPLACE(t".$en_team_n.",';".$bot[id]."',''), t".$en_team_n."hist=REPLACE(t".$en_team_n."hist,'".BNewHist($bot)."','') WHERE id = ".$user['battle']." ;");
		}
		else
		{
		mysql_query("UPDATE battle SET to1=".$time.", to2=".$time.", t".$my_team_n."=CONCAT(t".$my_team_n.",';".$bot[id]."') , t".$my_team_n."hist=CONCAT(t".$my_team_n."hist,'".BNewHist($bot)."') ,  t".$en_team_n."=REPLACE(t".$en_team_n.",';".$bot[id]."',''), t".$en_team_n."hist=REPLACE(t".$en_team_n."hist,'".BNewHist($bot)."','') WHERE id = ".$user['battle']." ;");
		}

		if ($bot[battle_t]==1)
		     { unset($boec_t1[$bot[id]]); 
		     		if ($user[battle_t]==2) { $boec_t2[$bot[id]]=$bot; $data_battle[t2].=';'.$bot[id] ; }
			elseif ($user[battle_t]==3) { $boec_t3[$bot[id]]=$bot; $data_battle[t3].=';'.$bot[id] ; }
		     } 
			elseif ($bot[battle_t]==2) 
			{ unset($boec_t2[$bot[id]]); 
					if ($user[battle_t]==1) { $boec_t1[$bot[id]]=$bot; $data_battle[t1].=';'.$bot[id] ;   }
				elseif ($user[battle_t]==3) { $boec_t3[$bot[id]]=$bot; $data_battle[t3].=';'.$bot[id] ; }					
			}
			elseif ($bot[battle_t]==3) 
			{ 
				unset($boec_t2[$bot[id]]); 
				if ($user[battle_t]==1) { $boec_t1[$bot[id]]=$bot; $data_battle[t1].=';'.$bot[id] ;   }
		     		elseif ($user[battle_t]==2) { $boec_t2[$bot[id]]=$bot; $data_battle[t2].=';'.$bot[id] ; }				
			}
			
		$cc++;
		}
		
//		if (($user['sex'] == 1)OR($user[hidden]>0 and $user[hiddenlog]=='')) { $action = ""; }
//		elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user);  if ($fuser[sex]==0) {$action="а"; } else {$action="";}}
//		else { $action="а"; }
//		addlog($user['battle'],'<span class=date>'.date("H:i").'</span> '.nick_in_battle($user,$user[battle_t]).' захватил'.$action.'  '.$all_bots_namea.'<BR>');
		
		if ($user[hidden]>0 and $user[hiddenlog]=='') 	{ $user[sex]=1;	}
		elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user); $user[sex]=$fuser[sex]; }
		
		$btext=str_replace(':','^',$all_bots_namea);
       	       addlog($user['battle'],"!:X:".time().':'.nick_new_in_battle($user).':'.($user[sex]+1010).":".$btext."\n");
	
	
	
	
		$bet=1;
		$sbet = 1;
		echo "Удачно использован свиток  \"{$rowm[name]}\"  ";
		$MAGIC_OK=1;
		} 
		else 
		{
			echo "Свиток рассыпался в ваших руках...";
		
		} //rand
	
	} 
	else
	{
	echo "Нет монстров доступных для захвата.";
	}
}

?>
