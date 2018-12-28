<?php
$RUN_FROM_LAB=0;
//получаем разрешение выпускать или нет
 $is_time_exit=mysql_fetch_array(mysql_query("SELECT * FROM `variables`  where `var`='bots_exit_lab' ;"));	 
 if ($is_time_exit['value'] >= time() ) {$RUN_FROM_LAB=0;} 
if ($RUN_FROM_LAB==1)
{
include "mobs_config.php";
include "fsystem.php";
$max_c=2;$ccc=0;
foreach($v_mobs as $keymob=>$valmob)
	{
	
	//работаем только с ботом для нужной лабы
       	if ( ($v_mobs[$keymob]['lab']==$LAB) and ($v_mobs[$keymob]['master_bot'] > 0) )
                       {

			//запросим статус мастер бота из базы клонов
			// может он уже в онлайне
			// у мастерботов bot_online = 2 у остальных больше 2-х
			$bbot=mysql_query("select * from users_clons where id_user=".$v_mobs[$keymob]['master_bot']." and bot_online = 2;");
						if (mysql_num_rows($bbot) >0)					        
						{
						//echo " есть запись значит мастер бот онлайн ничего не делаем <br>";
						}
						else
						{
			                       $ccc++;
						//echo " нету бота в онлайне <br>";
						//echo "пора его завести в онлайн<br>";	
						//ставим для этого мастер бота флаг что мы его завели в онлайн
						mysql_query("INSERT `variables` (`var`,`value`) values('bots_".$v_mobs[$keymob]['master_bot']."_is_run', '1' ) ON DUPLICATE KEY UPDATE `value` =1;");
						$BOT= mysql_fetch_array(mysql_query("select * from users where id=".$v_mobs[$keymob]['master_bot']." ;"));
						//доступные боту комнаты
						$br=$v_mobs[$keymob]['room'];
						$rnd_room=mt_rand(0,count($br)-1);
						$botroom=$br[$rnd_room];
						//echo " комната где бот появится $botroom <br> ";
						
						//системка что бот появился тут в этой комнате
						 $fr=mt_rand(0,count($bot_mess)-1);
						 addchp($bot_mess[$fr],$BOT['login']." (клон 1)",$botroom,CITY_ID);
						
						$BOT[protid]=$BOT[id];
							$BOT_items=load_mass_items_by_id($BOT);
							mysql_query("INSERT INTO `users_clons` SET `login`='".$BOT['login']." (клон 1)',`sex`='{$BOT['sex']}',
							`level`='{$BOT['level']}',`align`='{$BOT['align']}',`klan`='{$BOT['klan']}',`sila`='{$BOT['sila']}',
							`lovk`='{$BOT['lovk']}',`inta`='{$BOT['inta']}',`vinos`='{$BOT['vinos']}',
							`intel`='{$BOT['intel']}',`mudra`='{$BOT['mudra']}',`duh`='{$BOT['duh']}',`bojes`='{$BOT['bojes']}',`noj`='{$BOT['noj']}',
							`mec`='{$BOT['mec']}',`topor`='{$BOT['topor']}',`dubina`='{$BOT['dubina']}',`maxhp`='{$BOT['maxhp']}',`hp`='{$BOT['maxhp']}',
							`maxmana`='{$BOT['maxmana']}',`mana`='{$BOT['mana']}',`sergi`='{$BOT['sergi']}',`kulon`='{$BOT['kulon']}',`perchi`='{$BOT['perchi']}',
							`weap`='{$BOT['weap']}',`bron`='{$BOT['bron']}',`r1`='{$BOT['r1']}',`r2`='{$BOT['r2']}',`r3`='{$BOT['r3']}',`helm`='{$BOT['helm']}',
							`shit`='{$BOT['shit']}',`boots`='{$BOT['boots']}',`nakidka`='{$BOT['nakidka']}',`rubashka`='{$BOT['rubashka']}',`shadow`='{$BOT['shadow']}',`battle`=0,`bot`=2,
							`id_user`='{$BOT['id']}',`at_cost`='{$BOT_items['allsumm']}',`kulak1`=0,`sum_minu`='{$BOT_items['min_u']}',
							`sum_maxu`='{$BOT_items['max_u']}',`sum_mfkrit`='{$BOT_items['krit_mf']}',`sum_mfakrit`='{$BOT_items['akrit_mf']}',
							`sum_mfuvorot`='{$BOT_items['uvor_mf']}',`sum_mfauvorot`='{$BOT_items['auvor_mf']}',`sum_bron1`='{$BOT_items['bron1']}',
							`sum_bron2`='{$BOT_items['bron2']}',`sum_bron3`='{$BOT_items['bron3']}',`sum_bron4`='{$BOT_items['bron4']}',`ups`='{$BOT_items['ups']}',
							`injury_possible`=0, `battle_t`=0 , bot_online = 2, bot_room='{$botroom}'   ;");
							//echo mysql_error();
							$BOT['id'] = mysql_insert_id();
						}
	
				} // мастер бот
	
	if ($max_c <=$ccc) { break; }
	
                       } //гл.цикл
}
?>