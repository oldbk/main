<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
//$targ=($_POST['target']);
$baff_name='Обман Химеры';
$baff_type=920;// 5007154 - абилка /920,921,922,923,924,925,926  используется только 920

if ($ABIL>=1)
	{
	
		if ($ABIL==2)
		{
		$magic = magicinf(5017154);
		$magic['time']=360;
		}
		else
		{
		$magic = magicinf(5007154);
		}
	}


if ($user['battle'] > 0) 
{	
	echo "Нельзя использовать в бою..."; 
}
else {
	
//	$jert = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `login` = '{$targ}'   LIMIT 1;"));
	$jert = $user;
	if (($jert['id'] ==$user['id'])  and (($magic['id']==920) OR ($magic['id']==921) OR ($magic['id']==922)) ) //  тут потом допилить если кредовые ограничение
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
	else if (($jert['id'] >0) AND  ($jert['mearth']<=0) AND ($jert['id'] !=$user['id']) )
	{
	err('У персонажа '.$jert[login].' отсутствует владение: Стихия земли!');
	}	
	elseif ($jert['id'] >0)
	{
	$get_test_baff= mysql_fetch_array(mysql_query("select * from effects where owner='{$jert['id']}' and (type='{$baff_type}' or type=150 or type=130 or type=930) ; "));
	if (($get_test_baff[id] > 0) )
	{
		err('На персонаже уже есть магия стихий!');
	}
	else
	 {
	$i_can_use_magic=false;	 
	 /*
		1   за одну умелку у персонажа
	 */
	 $far[8]=1;
	 $far[9]=1;	 
	 $far[10]=1;	 
	 $far[11]=1;	 
	 $far[12]=1;	 
	 
	 $addsqlin="";
	 
	 if ( (($magic['id']==923) OR ($magic['id']==924) OR ($magic['id']==925)  OR ($magic['id']==926)  OR ($magic['id']==927)   OR ($magic['id']==928)  OR ($magic['id']==5007154) or ($magic['id']==5017154)  ) AND ($jert['id'] == $user['id']) )
	 {
	 
	 $lpow=12;
	 
	 //каст на себя и если у меня мало умелок
	 	if ($user['mearth']<1)
	 	{
	 	$addsqlin=" ,  `lastup`=1  ";
	 	}
	 	
	 
	 }
	 else
	 {
	 $lpow=$user[level];	 
	 }
	 
	 
	 if ($lpow>12) {$lpow=12;}
	 if ($lpow<=7) {$lpow=8;}	 
	 
	 $earth= $far[$lpow];

 	$magictime=time()+($magic['time']*60);
	 
	 			//если тело в бою
	 			if ($jert[battle]>0)
	 			{
	 			//в бою	 
		
	 			$nmana[920]=10;
	 			$nmana[921]=20;	 			
	 			$nmana[922]=30;	 			

	 			
	 			$nmana[923]=10;
	 			$nmana[924]=20;	 			
	 			$nmana[925]=30;	 	

	 			$nmana[926]=30;	 		 			
	 			$nmana[927]=30;	 
	 			$nmana[928]=30;	 	 				 			
	 			
	 			$nmana[5007154]=30;	 		 
	 			$nmana[5017154]=30;	 		 	 			
	 						
	 					
	 			
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
	 					$rowm['img']='scroll_wrath_ground0_2.gif';
	 					}
	 				$rkm=get_rkm_bonus_by_magic($magic[id]);
	 				mysql_query("INSERT INTO `effects` SET `type`='{$baff_type}',`name`='{$baff_name}', add_info='{$rowm['img']}:{$earth}:{$rkm}'  ,`time`='".$magictime."', `owner`='{$jert[id]}' ".$addsqlin."  ;");//сколько в базе
					if (mysql_affected_rows()>0)
					{
					if ($user[hidden]>0 and $user[hiddenlog]=='') 	{ $user[sex]=1;	}
					elseif ($user[hidden]>0 and $user[hiddenlog]!='') {  $fuser=load_perevopl($user); $user[sex]=$fuser[sex]; }
					//текст в ччат
					/* для кредовых если будут */
					$mags[920]=$rowm['img'];
					$mags[921]=$rowm['img'];
					$mags[922]=$rowm['img'];
					$mags[923]=$rowm['img'];
					$mags[924]=$rowm['img'];
					
					
					$mags[925]='scroll_wrath_ground0_2.gif';	
					$mags[926]='scroll_wrath_ground3_2.gif';					
					$mags[927]='scroll_wrath_ground2_2.gif';					
					$mags[928]='scroll_wrath_ground1_2.gif';															
					
					$mags[5007154]='scroll_wrath_ground0_2.gif';						
					$mags[5017154]='scroll_wrath_ground0_2.gif';											
					

					$magstxt[920]='Обман Химеры I';
					$magstxt[921]='Обман Химеры II';
					$magstxt[922]='Обман Химеры III';
					$magstxt[923]='Обман Химеры I';
					$magstxt[924]='Обман Химеры II';
					
					
					$magstxt[925]='Малый свиток «Обман Химеры»';	
					$magstxt[926]='Совершенный свиток «Обман Химеры»';						
					$magstxt[927]='Большой свиток «Обман Химеры»';						
					$magstxt[928]='Средний свиток «Обман Химеры»';																
					
					
					$magstxt[5007154]='Обман Химеры';
					$magstxt[5017154]='Обман Химеры';
					
					
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
