<?php
if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
include "/www/capitalcity.oldbk.com/fsystem.php";
$ituse=(int)$_GET['use'];

$myeff = getalleff($user['id']);

if ($user['battle'] > 0) 
{	
echo "Можно использовать вне боя..."; 
}
elseif ($user['hidden'] > 0) 
{	
echo "Вы находитесь под магией иллюзии..."; 
}
elseif (isset($myeff[830])) 
{
echo "Вы находитесь под медитацией!";
}
elseif ($myeff['owntravma']>=1)
{
err('С вашей травмой нельзя драться!');
		
}
elseif ($user['hp']<($user['maxhp']/3))
{
err('Вы слишком ослаблены для боя!');
}
elseif ($user['room'] !=300) 
{	
echo "Можно использовать только в локации «Бои с пойманными монстрами»"; 
}
else {

$cb=1;
$cb_stop=1;
	
		//ищем какого бота выпускать
		$get_drop_scroll=mysql_query("select * from get_lock_bots where item_id = '{$rowm['id']}'  "); // ищем
		if (mysql_num_rows($get_drop_scroll) > 0)
			{
								$dscroll=mysql_fetch_assoc($get_drop_scroll);
								
								$bot_data=mysql_fetch_array(mysql_query("select * from users where id='{$dscroll['proto_bot']}' ;"));
								$bot_data[login]=$dscroll['name_bot'];
								
								// create prototype record at users_clons.
								$bots_items=load_mass_items_by_id($bot_data);
								//$bots_items['allsumm']=$bots_items['allsumm']*0.4;//занижаем стоимость шмоток
						
						
								//типы боев 
									//311 - бои с ХИ
									//312 бои с  Драконами
									//313 бои  Древобороды
									//314 бои с лабовскими ботами
										
									if ( ($dscroll['proto_bot']>=101) and ($dscroll['proto_bot']<=110)) 
										{
										//бос с пойманым ИХ	
										$bt_type=311;
										}
									elseif ( ($dscroll['proto_bot']>=42) and ($dscroll['proto_bot']<=65)) 
										{
										//бос с пойманым  Дараконами
										$bt_type=312;
										}
									elseif ( ($dscroll['proto_bot']>=86) and ($dscroll['proto_bot']<=89))
										{
										//Деревья
										$bt_type=313;
										} 
									else
										{
										//отсальные боты из лабы
										$bt_type=314;
										//для ботов из лабы грузим их из гонфига лабы для соотвествия уровню
										include "/www/capitalcity.oldbk.com/labconfig_4.php";
									
										$userlevel=$user['level'];										
										if ($userlevel<8) $userlevel=8;
										if ($userlevel>15) $userlevel=15;
	
										//под уровень перса , правим МФ бота
										$bot_data['level']=$bot_setup[$userlevel][$bot_data['id']]['level'];
										$bot_data['maxhp']=$bot_setup[$userlevel][$bot_data['id']]['maxhp'];
	
										$bots_items['min_u']=$bot_setup[$userlevel][$bot_data['id']]['sum_minu'];
										$bots_items['max_u']=$bot_setup[$userlevel][$bot_data['id']]['sum_maxu'];
										$bots_items['krit_mf']=$bot_setup[$userlevel][$bot_data['id']]['sum_mfkrit'];
										$bots_items['akrit_mf']=$bot_setup[$userlevel][$bot_data['id']]['sum_mfakrit'];
										$bots_items['uvor_mf']=$bot_setup[$userlevel][$bot_data['id']]['sum_mfuvorot'];
										$bots_items['auvor_mf']=$bot_setup[$userlevel][$bot_data['id']]['sum_mfauvorot'];
										$bots_items['bron1']=$bot_setup[$userlevel][$bot_data['id']]['sum_bron1'];
										$bots_items['bron2']=$bot_setup[$userlevel][$bot_data['id']]['sum_bron2'];
										$bots_items['bron3']=$bot_setup[$userlevel][$bot_data['id']]['sum_bron3'];
										$bots_items['bron4']=$bot_setup[$userlevel][$bot_data['id']]['sum_bron4'];
										$bots_items['allsumm']=$bot_setup[$userlevel][$bot_data['id']]['at_cost'];
										$bots_items['ups']=11;
										}
						
								mysql_query("INSERT INTO `users_clons` SET `login`='".$bot_data[login]."',`sex`='{$bot_data['sex']}',
											`level`='{$bot_data['level']}',`align`='{$bot_data['align']}',`klan`='{$bot_data['klan']}',`sila`='{$bot_data['sila']}',
											`lovk`='{$bot_data['lovk']}',`inta`='{$bot_data['inta']}',`vinos`='{$bot_data['vinos']}',
											`intel`='{$bot_data['intel']}',`mudra`='{$bot_data['mudra']}',`duh`='{$bot_data['duh']}',`bojes`='{$bot_data['bojes']}',`noj`='{$bot_data['noj']}',
											`mec`='{$bot_data['mec']}',`topor`='{$bot_data['topor']}',`dubina`='{$bot_data['dubina']}',`maxhp`='{$bot_data['maxhp']}',`hp`='{$bot_data['maxhp']}',
											`maxmana`='{$bot_data['maxmana']}',`mana`='{$bot_data['mana']}',`sergi`='{$bot_data['sergi']}',`kulon`='{$bot_data['kulon']}',`perchi`='{$bot_data['perchi']}',
											`weap`='{$bot_data['weap']}',`bron`='{$bot_data['bron']}',`r1`='{$bot_data['r1']}',`r2`='{$bot_data['r2']}',`r3`='{$bot_data['r3']}',`helm`='{$bot_data['helm']}',
											`shit`='{$bot_data['shit']}',`boots`='{$bot_data['boots']}',`nakidka`='{$bot_data['nakidka']}',`rubashka`='{$bot_data['rubashka']}',`shadow`='{$bot_data['shadow']}',`battle`='{$user['battle']}',`bot`=1,`bot_online`=-2,
											`id_user`='{$bot_data['id']}',`at_cost`='{$bots_items['allsumm']}',`kulak1`=0,`sum_minu`='{$bots_items['min_u']}',
											`sum_maxu`='{$bots_items['max_u']}',`sum_mfkrit`='{$bots_items['krit_mf']}',`sum_mfakrit`='{$bots_items['akrit_mf']}',
											`sum_mfuvorot`='{$bots_items['uvor_mf']}',`sum_mfauvorot`='{$bots_items['auvor_mf']}',`sum_bron1`='{$bots_items['bron1']}',
											`sum_bron2`='{$bots_items['bron2']}',`sum_bron3`='{$bots_items['bron3']}',`sum_bron4`='{$bots_items['bron4']}',`ups`='{$bots_items['ups']}',
											`injury_possible`=0, `battle_t`='2', `mklevel`='{$user[level]}' ;");
											
								$bot_data['id'] = mysql_insert_id();
								
								
							
								
									
									
													
								mysql_query("INSERT INTO `battle`
										(
											`id`,`coment`,`teams`,`timeout`,`type`,`status`,`t1`,`t2`,`to1`,`to2`,`t1hist`,`t2hist`,`blood`,`status_flag`,`CHAOS`
										)
										VALUES
										(
											NULL,'<b>Бой с пойманным монстром</b>','20000','10','{$bt_type}','0','".$user['id']."','".$bot_data['id']."','".time()."','".time()."','".BNewHist($user)."','".BNewHist($bot_data)."','1','0','0'
										)");
								$id = mysql_insert_id();	
												
								//ставим ботам бой
								mysql_query("UPDATE `users_clons` SET `battle` = '{$id}' WHERE `id` = {$bot_data['id']} ;");
								//ставим телу бой
								mysql_query("UPDATE users SET `battle` = '{$id}' ,`zayavka`=0 , `battle_t`='1' WHERE `id`= ".$user['id']);
								
								addlog($id,"!:S:".time().":".BNewHist($user).":".BNewHist($bot_data)."\n");
							
								//чистим 
								mysql_query("DELETE FROM `get_lock_bots` WHERE `id`='{$dscroll['id']}'  ");
								
								//таблик логов локации

								mysql_query("INSERT INTO `battle_hist_rist300` SET `battle_id`='{$id}',`owner`='{$user['id']}' ,`bot_id`='{$dscroll['proto_bot']}', `owner_hist` = '".BNewHist($user)."' ,`bot_hist`='".BNewHist($bot_data)."',`win`=3;");

								$bet=1;
								$sbet = 1;
								echo "Удачно использован свиток  \"{$rowm[name]}\"  ";
								$MAGIC_OK=1;

								addch('<font color=red>Внимание!</font> Ваш бой начался! <BR>\'; top.frames[\'main\'].location=\'fbattle.php\'; var z = \'   ', $user['room'],CITY_ID);
								//системка
								$rrc="<b>".$user['login']."</b> и <b>".$bot_data['login']."</b>"; //для текста в чат
								addch ("<a href=logs.php?log=".$id." target=_blank>Поединок</a> между <B>".$rrc."</B> начался.   ",$user['room'],CITY_ID);									
				}
	
	
		



	} 
	
	


?>
