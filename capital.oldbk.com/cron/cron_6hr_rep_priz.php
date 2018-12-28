#!/usr/bin/php
<?php
ini_set('display_errors','On');
$CITY_NAME='capitalcity';
include "/www/".$CITY_NAME.".oldbk.com/cron/init.php";
if( !lockCreate("cron_6hr_job") ) {
    exit("Script already running.");
}
echo date("d.m.y H:i:s").'\r\n';
$gotime=time(); //время запуска

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

					$nagrada_rep[246]=1200; $nagrada_check[246]=3207; $nagrada_exp[246]=6000;
					$nagrada_rep[247]=1400; $nagrada_check[247]=3207; $nagrada_exp[247]=7000;
					$nagrada_rep[248]=1600; $nagrada_check[248]=3204; $nagrada_exp[248]=8000;
					$nagrada_rep[249]=1800;	$nagrada_check[249]=3206; $nagrada_exp[249]=9000;
					$nagrada_rep[250]=2000;	$nagrada_check[250]=3205; $nagrada_exp[250]=10000;
					$nagrada_rep[251]=2200;	$nagrada_check[251]=3205; $nagrada_exp[251]=12000;					
					$nagrada_rep[252]=2400;	$nagrada_check[252]=3205; $nagrada_exp[252]=14000;										
					$nagrada_rep[253]=2600;	$nagrada_check[253]=3205; $nagrada_exp[253]=16000;															

				//чеки
					$item_ch[3204]=50;
					$item_ch[3205]=100;
					$item_ch[3206]=80;
					$item_ch[3207]=30;
///////////////////////////////////////////////////////////////////////////////////////////////////
//грузим всех в масив
$otryad=array();
$otwin=array();
$get_all_ot=mysql_query("select *, UNIX_TIMESTAMP(btime) as sbtime  from tur_stat where start=2 ORDER BY demag DESC ;");
while($orow=mysql_fetch_array($get_all_ot))
{
//запоминаем победителей
if (!($otwin[$orow[lvl]])) { $otwin[$orow[lvl]]=$orow;  }
//запоминаем по уровням всех учасников
$otryad[$orow[lvl]][$orow[id]]=$orow;
}

//и сразу дел - всех
mysql_query("delete from tur_stat where start=2;");



//while($wrow=mysql_fetch_array($get_all_wins))
//парсим победителей
foreach ($otwin as $k =>$wrow)
	{
					$Blaha=240+$wrow[lvl];
					$repa=$nagrada_rep[$Blaha];
					$expa=$nagrada_exp[$Blaha];
					$check=$nagrada_check[$Blaha];
			
			if (($repa > 0)and ($expa >0 ))
					{
					//если есть настройки идем дальше
					//ищем людей
					$winners=mysql_query("select * from oldbk.users where (id='{$wrow[u1]}' or id='{$wrow[u2]}' or id='{$wrow[u3]}')   ; ");
					$ku=mysql_num_rows($winners);
				 	if (($ku>0)and($ku<4))
					{
					while($winrow=mysql_fetch_array($winners))
						{
						$dress=array();
						$rec=array();
							if ($winrow[id_city]==0) { $bci='oldbk.';  }
							if ($winrow[id_city]==1) { $bci='avalon.';  }											
							if ($winrow[id_city]==2) { $bci='angels.';  }											
	
						$realwin=mysql_fetch_array(mysql_query("select * from ".$bci."users where id='{$winrow[id]}' ; "));
							
						//загруз бонуса
						$eff = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$winrow['id']}'  and `type` = '9101' ;")); 	
						if ($eff['id']>0)
							{
							$add_bonus=$eff['add_info'];
							}
							else
							{
							$add_bonus=0;
							}
									
						if ($realwin[prem]>0)
							{
							$addrepa=$repa+$repa*(0.1+$add_bonus);
							}
						else
							{
							$addrepa=$repa+($repa*$add_bonus);
							}
												
				        mysql_query("UPDATE ".$bci."`users` SET `exp`=`exp`+".$expa." ,`rep`=`rep`+'".$addrepa."', `repmoney` = `repmoney` + '".$addrepa."' WHERE `id`='".$winrow['id']."' LIMIT 1; ");
				        
					mysql_query("INSERT INTO `oldbk`.`users_rep_log` SET `onwer`={$realwin[id]},`lvl`={$realwin[level]},`sdate`=NOW(),`rep_rist240`=`rep_rist240`+{$addrepa} ON DUPLICATE KEY UPDATE `rep_rist240`=`rep_rist240`+{$addrepa};");

                                                						$rec['owner']=$realwin[id];
												$rec['owner_login']=$realwin[login];
												$rec['owner_balans_do']=$realwin[money];
												$rec['owner_balans_posle']=$realwin[money];
												$rec['owner_rep_do']= $realwin[repmoney];
												$rec['owner_rep_posle']=$realwin[repmoney]+$addrepa;
												$rec['target']=0;
												$rec['target_login']="Победа в турнире";
												$rec['type']=183;//репа за квест
												$rec['sum_kr']=0;
												$rec['sum_ekr']=0;
												$rec['sum_rep']=$addrepa;
												$rec['sum_kom']=0;
												add_to_new_delo($rec);
	        		addchp ('<font color=red>Поздравляем!</font> Вы получили <b>'.$addrepa.'</b> репутации и  <b>'.$expa.'</b> опыта за победу в турнире!','{[]}'.$realwin['login'].'{[]}',$realwin['room'],$realwin['id_city']);
			
			 if ($item_ch[$check])
			 	{
	        		mysql_query("INSERT INTO oldbk.`inventory`
								        		(`name`,`duration`,`maxdur`,`cost`,`owner`,`nlevel`,`nsila`,`nlovk`,`ninta`,`nvinos`,`nintel`,`nmudra`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nalign`,`minu`,`maxu`,`gsila`,
								        		`glovk`,`ginta`,`gintel`,`ghp`,`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`img`,`text`,`dressed`,
								        		`bron1`,`bron2`,`bron3`,`bron4`,`dategoden`,`magic`,`type`,`present`,`sharped`,`massa`,`goden`,`needident`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`letter`,`isrep`,`update`,`setsale`,`prototype`,`otdel`,`bs`,`gmp`,`includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,
								        		`includemagicuses`,`includemagiccost`,`includemagicekrcost`,`gmeshok`,`tradesale`,`karman`,`stbonus`,`upfree`,`ups`,`mfbonus`,`mffree`,`type3_updated`,`bs_owner`,`nsex`,`present_text`,`add_time`,`labonly`,`labflag`,`prokat_idp`,`prokat_do`,`arsenal_klan`,`repcost`,`up_level`,`ecost`,`group`,`ekr_up`,`unik`,`add_pick`,`pick_time`,`sowner`)
								        		VALUES
								        		('Чек на предъявителя ".$item_ch[$check]."кр',0,1,'{$item_ch[$check]}','{$winrow[id]}',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'lab2_".$item_ch[$check]."kr.gif',
								        		'',0,0,0,0,0,0,0,50,'Ристалище',0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'',0,NOW(),0,'{$check}','52',0,0,0,0,0,'',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL,0,0,0,0,NULL,'',0,0,0,1,NULL,0,NULL,NULL,0);");
					if (mysql_affected_rows()>0)
					{
                                                $dress[id]=mysql_insert_id();
                                                
                                                $dress[idcity]=$winrow[id_city];
                                                $dress['type']=52;
                                                $dress['name']='Чек на предъявителя '.$item_ch[$check].'кр';
                                                $dressid=get_item_fid($dress);
						                                                $rec=array();
												$rec['owner']=$realwin[id];
												$rec['owner_login']=$realwin[login];
												$rec['owner_balans_do']=$realwin[money];
												$rec['owner_balans_posle']=$realwin[money];
												$rec['target']=0;
												$rec['target_login']="Победа в турнире";
												$rec['type']=1184;//подарок за квест
												$rec['sum_kr']=0;
												$rec['sum_ekr']=0;
												$rec['sum_kom']=0;
												$rec['item_id']=$dressid;
												$rec['item_name']=$dress['name'];
												$rec['item_count']=1;
												$rec['item_type']=$dress['type'];
												$rec['item_cost']=$item_ch[$check];
												$rec['item_dur']=0;
												$rec['item_maxdur']=1;
												$rec['item_ups']=0;
												$rec['item_unic']=0;
												$rec['item_incmagic']='';
												$rec['item_incmagic_count']='';
												$rec['item_arsenal']='';
												add_to_new_delo($rec);
					 }
					 else
					 {
	 				echo mysql_error();
					 }
				  }
                                                

								        	mysql_query("INSERT INTO oldbk.`inventory` (`name`,`duration`,`maxdur`,`cost`,`owner`,`nlevel`,`nsila`,`nlovk`,`ninta`,`nvinos`,`nintel`,`nmudra`,`nnoj`,`ntopor`,`ndubina`,`nmech`,
								        		`nalign`,`minu`,`maxu`,`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`img`,`text`,`dressed`,`bron1`,
								        		`bron2`,`bron3`,`bron4`,`dategoden`,`magic`,`type`,`present`,`sharped`,`massa`,`goden`,`needident`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,`gfire`,`gwater`,
								        		`gair`,`gearth`,`glight`,`ggray`,`gdark`,`letter`,`isrep`,`update`,`setsale`,`prototype`,`otdel`,`bs`,`gmp`,`includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,
								        		`includemagicuses`,`includemagiccost`,`includemagicekrcost`,`gmeshok`,`tradesale`,`karman`,`stbonus`,`upfree`,`ups`,`mfbonus`,`mffree`,`type3_updated`,`bs_owner`,`nsex`,
								        		`present_text`,`add_time`,`labonly`,`labflag`,`prokat_idp`,`prokat_do`,`arsenal_klan`,`repcost`,`up_level`,`ecost`,`group`,`ekr_up`,`unik`,`add_pick`,`pick_time`,`sowner`)
								        		VALUES ('Сердце Рыцаря',0,1,1,'{$winrow[id]}',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'heart_of_hero".mt_rand(1,7).".gif','',0,0,0,0,0,0,0,200,'',0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
								        		'',0,NOW(),0,1011001,'72',0,0,0,0,0,'',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL,0,0,0,0,NULL,'',0,0,0,1,NULL,0,NULL,NULL,0);");
				if (mysql_affected_rows()>0)
					{
                                                $dress[id]=mysql_insert_id();
                                                $dress[idcity]=$winrow[id_city];
                                                $dress['type']=200;
                                                $dress['name']='Сердце Рыцаря';
                                                $dressid=get_item_fid($dress);
						                                                $rec=array();
												$rec['owner']=$realwin[id];
												$rec['owner_login']=$realwin[login];
												$rec['owner_balans_do']=$realwin[money];
												$rec['owner_balans_posle']=$realwin[money];
												$rec['target']=0;
												$rec['target_login']="Победа в турнире";
												$rec['type']=1184;//подарок за квест
												$rec['sum_kr']=0;
												$rec['sum_ekr']=0;
												$rec['sum_kom']=0;
												$rec['item_id']=$dressid;
												$rec['item_name']=$dress['name'];
												$rec['item_count']=1;
												$rec['item_type']=$dress['type'];
												$rec['item_cost']=$item_ch[$check];
												$rec['item_dur']=0;
												$rec['item_maxdur']=1;
												$rec['item_ups']=0;
												$rec['item_unic']=0;
												$rec['item_incmagic']='';
												$rec['item_incmagic_count']='';
												$rec['item_arsenal']='';
												
												add_to_new_delo($rec);

						addchp ('<font color=red>Поздравляем!</font> Вы получили <b>\"Чек на предъявителя '.$item_ch[$check].'кр\"</b> и Сувенир:<b>\"Сердце Рыцаря\"</b> !','{[]}'.$winrow['login'].'{[]}',$winrow['room'],$winrow['id_city']);
						}
						else
						{
		 				echo mysql_error();
						}
						
						
						
						}
				 	}
					else
					  {
					  addchp ('<font color=red>Внимание!</font> SQL Ошибка выдачи награды команды - нет людей или больше 3-х'.$wrow[id],'{[]}Bred{[]}');
					  }					
					
					
					}else
				        {
					        addchp ('<font color=red>Внимание!</font> Ошибка выдачи награды команды - нет настройки награды'.$wrow[id],'{[]}Bred{[]}');
				        }
				        
	//пишем в лог победителей
	//21.01.13 06:00 - Победитель [10]:Отряд: «ADs» ( [AD] Vagon [10]Инф. о Vagon, [AD] The Groove [10]Инф. о The Groove, [MiB] Rusich [10]Инф. о Rusich) Начало боя 25.01.13 23:29 продолжительность 2 ч. 18 мин. лог боя »»			        
	$get_start_battle=mysql_fetch_array(mysql_query("select UNIX_TIMESTAMP(`date`) as std from battle where id='{$wrow[battle]}' "));
	
	
	$nn=0;
	$add_to_tlog='';
	foreach ($otryad[$wrow[lvl]] as $oid=>$vr)
	{
	$nn++;
	$add_to_tlog.="<b>{$nn}</b> ".$vr[group]." нанесено урона:<b>{$vr[demag]} HP</b> <a href=logs.php?log=$vr[battle]>лог боя  »»</a><br>";
	}
	
	mysql_query("INSERT INTO `tur_logs` SET gotime='{$gotime}', winer='".mysql_real_escape_string($wrow[group])."', active=0 ,start_time='{$get_start_battle[std]}' , end_time='{$wrow[sbtime]}' , `logs`='".mysql_real_escape_string($add_to_tlog)."' ,  `type`='$Blaha' ;");	
	echo "INSERT INTO `tur_logs` SET gotime='{$gotime}', winer='".mysql_real_escape_string($wrow[group])."', active=0 ,start_time='{$get_start_battle[std]}' , end_time='{$wrow[sbtime]}' , `logs`='".mysql_real_escape_string($add_to_tlog)."' ,  `type`='$Blaha' ;" ;	
	echo mysql_error();
	}

lockDestroy("cron_6hr_job");
?>