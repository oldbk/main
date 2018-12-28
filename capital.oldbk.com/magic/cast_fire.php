<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
//$targ=($_POST['target']);
$baff_name='Гнев Ареса';
$baff_type=150;//151/152/153/154/155/156/5007152- используется только 150

if ($ABIL>=1)
	{

		if ($ABIL==2)
		{
		$magic = magicinf(5017152);		
		$magic['time']=360;
		}
		else
		{
		$magic = magicinf(5007152);	
		}
	}


if ($user['battle'] > 0) 
{	
	echo "Нельзя использовать в бою..."; 
}
else {
	
//	$jert = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `login` = '{$targ}'   LIMIT 1;"));
	$jert = $user;
	if (($jert['id'] ==$user['id'])  and (($magic['id']==150) OR ($magic['id']==151) OR ($magic['id']==152)) ) // нельзя юзать на себя в этих типах
	//if (1==2)
	{
	err('Нельзя использовать на себя :(');
	}
	else if (($jert['id'] >0) AND  ($jert['id_city']!=$user['id_city']) )
	{
	err('Персонаж в другом городе...');
	}
	else if (($jert['id'] >0) AND  ($jert['room']!=$user['room']) )
	{
	err('Персонаж в другой комнате...');
	}
	else if (($jert['id'] >0) AND  ($jert['level']<8) AND ($jert['id'] !=$user['id']) )
	{
	err('Персонаж ниже 8-го уровня...');
	}	
	else if (($jert['id'] >0) AND  ($jert['mfire']<=0) AND ($jert['id'] !=$user['id']) )
	{
	err('У персонажа '.$jert[login].' отсутствует владение: Стихия огня!');
	}	
	elseif ($jert['id'] >0)
	{
	$get_test_baff= mysql_fetch_array(mysql_query("select * from effects where owner='{$jert['id']}' and (type='{$baff_type}' or type=130 or type=920  or type=930) ; "));
	if (($get_test_baff[id] > 0) )
	{
		err('На персонаже уже есть магия стихий!');
	}
	else
	 {
	$i_can_use_magic=false;	 
	 /*
	 12 - 1-10 за одну умелку у персонажа
	11 - 1-8  за одну умелку у персонажа
	10 - 1-6  за одну умелку у персонажа
	9 - 1-4   за одну умелку у персонажа
	8 - 1-2   за одну умелку у персонажа
	 */
	 $far[8]=10;
	 $far[9]=10;	 
	 $far[10]=10;	 
	 $far[11]=10;	 
	 $far[12]=10;	 
	 
	 $addsqlin="";
	 
	 if ( (($magic['id']==153) OR ($magic['id']==154)OR ($magic['id']==156) OR ($magic['id']==157) OR ($magic['id']==158)  OR ($magic['id']==155) OR ($magic['id']==5007152) OR ($magic['id']==5017152)    ) AND ($jert['id'] == $user['id']) )
	 {
	 
	 $lpow=12;
	 
	 //каст на себя и если у меня мало умелок
	 	if ($user['mfire']<3)
	 	{
	 	$addsqlin=" ,  `lastup`=3  ";
	 	}
	 	
	 
	 }
	 else
	 {
	 $lpow=$user[level];	 
	 }
	 
	 
	 if ($lpow>12) {$lpow=12;}
	 if ($lpow<=7) {$lpow=8;}	 
	 
	 $fire= $far[$lpow];

 	$magictime=time()+($magic['time']*60);
	 
	 			//если тело в бою
	 			if ($jert[battle]>0)
	 			{
	 			//в бою	 			
	 			$nmana[150]=10;
	 			$nmana[151]=20;	 			
	 			$nmana[152]=30;	 			
	 			
	 			$nmana[153]=10;
	 			$nmana[154]=20;	 			
	 			$nmana[155]=30;	 	
	 			$nmana[156]=30;	 	 			
	 			$nmana[157]=30;	 		 			
	 			
	 			$nmana[5007152]=30;	 		 			
	 			$nmana[5017152]=30;	 		 				 					
	 			
	 			$need_mana=$nmana[$magic[id]]; //берем тип свитка
	 				if ($jert[mana]>=$need_mana)
	 				{
	 				//расходуем ману целе
	 				mysql_query("UPDATE users set mana=mana-'{$need_mana}'  where id='{$jert[id]}' and mana>='{$jert[mana]}' LIMIT 1; ");
		 				if (mysql_affected_rows()>0)
						{
			 			$i_can_use_magic=true;	 				
			 			}
	 				}
	 				else
	 				{
	 				//не хватает маны
 					err('У персонажа '.$jert[login].' не хватает маны для этой магии в бою!');
	 				}
	 			
	 			}
	 			else
	 			{
				//не в бою	 			
	 			$i_can_use_magic=true;
	 			}
	 			
	 		
	 		
		 		if ($i_can_use_magic==true)
	 			{
	 				if ($rowm['img']=='')
	 					{
	 					$rowm['img']='scroll_wrath_ares0_2.gif';						
	 					}
	 				$rkm=get_rkm_bonus_by_magic($magic[id]);
	 				mysql_query("INSERT INTO `effects` SET `type`='{$baff_type}',`name`='{$baff_name}', add_info='{$rowm['img']}:{$fire}:{$rkm}'  ,`time`='".$magictime."', `owner`='{$jert[id]}' ".$addsqlin."  ;");//сколько в базе
					if (mysql_affected_rows()>0)
					{
					if ($user[hidden]>0 and $user[hiddenlog]=='') 	{ $user[sex]=1;	}
					elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user); $user[sex]=$fuser[sex]; }
					//текст в ччат
					$mags[150]=$rowm['img'];
					$mags[151]=$rowm['img'];
					$mags[152]=$rowm['img'];
					$mags[153]=$rowm['img'];
					$mags[154]=$rowm['img'];
					
					
					$mags[155]='scroll_wrath_ares0_2.gif';	
					$mags[156]='scroll_wrath_ares3_2.gif';						
					$mags[157]='scroll_wrath_ares2_2.gif';						
					$mags[158]='scroll_wrath_ares1_2.gif';			
					$mags[5007152]='scroll_wrath_ares0_2.gif';						
					$mags[5017152]='scroll_wrath_ares0_2.gif';						

					$magstxt[150]='Гнев Ареса I';
					$magstxt[151]='Гнев Ареса II';
					$magstxt[152]='Гнев Ареса III';
					$magstxt[153]='Гнев Ареса I';
					$magstxt[154]='Гнев Ареса II';
					
					$magstxt[155]='Малый свиток «Гнев Ареса»';	
					$magstxt[156]='Совершенный свиток «Гнев Ареса»';	
					$magstxt[157]='Большой свиток «Гнев Ареса»';						
					$magstxt[158]='Средний свиток «Гнев Ареса»';				
							
					$magstxt[5007152]='Гнев Ареса';						
					$magstxt[5017152]='Гнев Ареса';						
					
					
					$mag_gif='<img src=i/magic/'.$mags[$magic[id]].'>';

					 if(($user['hidden'] > 0) and ($user['hiddenlog'] ==''))
					 {
					 $fuser['login']='<i>Невидимка</i>';
					$fuser['id']=$user['hidden'];					 
					 $sexi='использовал';
					 }
					 else
					 {
					 $fuser=load_perevopl($user); 
					 if ($fuser['sex'] == 1) {$sexi='использовал';  }	else { $sexi='использовала';}
					 }
					 
					 if(($jert['hidden'] > 0) and ($jert['hiddenlog'] ==''))
					 {
					 $fjert['login']='<i>Невидимка</i>';
					$fjert['id']=$jert['hidden'];					 					 					 
					 }
					 else
					 {
					 $fjert=load_perevopl($jert); 
					 }
					 
					 

					if ($fjert['id']==$fuser['id'])
					{
					$mag_gif='<img src=http://i.oldbk.com/i/sh/'.$rowm['img'].'>';
					addch($mag_gif." ".link_for_user($fuser)." использовал магию &quot;".link_for_magic($rowm['img'],$magstxt[$magic[id]])."&quot;, на себя.",$user['room'],$user['id_city']);					
					}
					else
					{
					$mag_gif='<img src=http://i.oldbk.com/i/sh/'.$rowm['img'].'>';
					addch($mag_gif." ".link_for_user($fuser)." использовал магию &quot;".link_for_magic($rowm['img'],$magstxt[$magic[id]])."&quot;, на персонажа ".link_for_user($fjert).".",$user['room'],$user['id_city']);
					}
										
		
					$bet=1;
					$sbet = 1;
					echo "Все прошло удачно!";
					$MAGIC_OK=1;

						try {
							global $app;

							$UserObj = new \components\models\User($user);
							$Quest = $app->quest->setUser($UserObj)->get();

							$Checker = new \components\Component\Quests\check\CheckerMagic();
							$Checker->magic_id = $baff_type;
							if (($Item = $Quest->isNeed($Checker)) !== false) {
								$Quest->taskUp($Item);
							}

							unset($UserObj);
							unset($Quest);
						} catch (Exception $ex) {
							\components\Helper\FileHelper::writeException($ex, 'cast_mearth');
						}

				 	}
				}
	   }
	
	}
	else
	     {
	     err('Персонаж  "'.$targ.'"  не найден!');
	     }


	} 
	



?>
