#!/usr/bin/php
<?php
ini_set('display_errors','On');
$CITY_NAME='capitalcity';
include "/www/".$CITY_NAME.".oldbk.com/cron/init.php";
if( !lockCreate("cron_op_zayav_job") ) {
    exit("Script already running.");
}


//addchp ('<font color=red>Внимание!</font> Start OP ZAYAV','{[]}Bred{[]}');

$time = time();

$nomagic=(date("i")%2); //  через минуту делаем без магии


//(06:00-10:00 и 16:00-18:00 команды 3*3, 4*4 также)
$h=date("H");

if ((($h>=6)and($h<=10)) OR (($h>=16)and($h<=18)) )
	{
	$kkol=4;
	}
	else
	{
	$kkol=5;
	}



$aligns_array=array(3,2,6);//3-темн. 2-нейтрал 6-свет


function search_users($lvel,$aligns,$kol=5)
{
//поиск подходящих тел

				if ($lvel==13)
				{
				//13 и 14 вместе
				$q = mysql_query("select * from zayavka_turn where lvl>=".($lvel)." and lvl<=".($lvel+1)." and align in (".(implode(",",$aligns)).") and zayid=0 order by lvl desc, align , id limit ".$kol);
				echo "select * from zayavka_turn where lvl>=".($lvel)." and lvl<=".($lvel+1)." and align in (".(implode(",",$aligns)).") and zayid=0 order by lvl desc, align , id limit ".$kol ;
				}
				else
				{
				$q = mysql_query("select * from zayavka_turn where lvl=".($lvel)."  and align in (".(implode(",",$aligns)).") and zayid=0 order by lvl desc, align , id limit ".$kol);
				echo "select * from zayavka_turn where lvl=".($lvel)."  and align in (".(implode(",",$aligns)).") and zayid=0 order by lvl desc, align , id limit ".$kol ;

				}
				
				echo "<br>";		

				if (mysql_num_rows($q) == $kol) 
				{
				//есть мужное количество
				$out_array=array();	
					while($row = mysql_fetch_array($q)) 
						{
						$out_array['ids'][]=$row['owner']; //ид людей
						$out_array['aligns'][$row['align']]++; // подсчет склонок
						}
					return $out_array;
				}
return false;				
}


function get_str_align($arr)
{
$str[2]=array('<img src="http://i.oldbk.com/i/align_2.gif">нейтрал', '<img src="http://i.oldbk.com/i/align_2.gif">нейтрала', '<img src="http://i.oldbk.com/i/align_2.gif">нейтралов');
$str[3]=array('<img src="http://i.oldbk.com/i/align_3.gif">темный', '<img src="http://i.oldbk.com/i/align_3.gif">темных', '<img src="http://i.oldbk.com/i/align_3.gif">темных');
$str[6]=array('<img src="http://i.oldbk.com/i/align_6.gif">светлый', '<img src="http://i.oldbk.com/i/align_6.gif">светлых', '<img src="http://i.oldbk.com/i/align_6.gif">светлых');
$out_st='';
foreach($arr as $k=>$v)
	{
	$out_st.=declOfNum($v,$str[$k]).", ";
	}
$out_st=substr($out_st,0,-2);	
return $out_st;
}

for ($Li=9;$Li<=13;$Li++)
{
$team=array();
$fails=0;

		
		if ($Li==13)
			{
			echo "Поиск для заявок ".$Li." и ".($Li+1)."  уровней <br>\n";
			}
			else
			{
			echo "Поиск для заявок ".$Li."  уровней <br>\n";
			}
		
		
		for ($t=1;$t<=2;$t++)		
		{
		echo "Поиск для команды ".$t."<br>\n";		
				foreach($aligns_array as $k=>$v)
					{
						
						if ($t==1)
							{
							//набор для первой команды
							$tmp_users=search_users($Li,array("0"=>$v),$kkol);
							$old_align_key=$k;
							
							if ($tmp_users)
								{
								echo "users for team 1 ok <br>\n";
								$team[$t]=$tmp_users;
								unset($tmp_users);
								break;
								}
								else
								{
								echo "no users for team 1<br>\n";
								}
							}
							else
							{
							$al=$aligns_array;
							unset($al[$old_align_key]);
							$tmp_users=search_users($Li,$al,$kkol);		
							
								if ($tmp_users==false)
								{
								echo "no users for team 2<br>\n";
								unset($tmp_users);
								break;
								}
								else
								{
								echo "есть обе команды!!! <br>\n";
								$team[$t]=$tmp_users;								
								unset($tmp_users);								
										//делаем заявку на 60 сек
										//Ваша заявка: (5 темных) : (3 светлых, 2 нейтрала),
										
										if ($Li==13)
											{
											$tmin=13;
											$tmax=14;
											}
											else
											{
											$tmin=$Li;
											$tmax=$Li;
											}
										
										mysql_query("INSERT INTO `zayavka` SET `coment`='Бой склонностей',`type`=3,`team1`='".implode(";",$team[1]['ids']).";',`team2`='".implode(";",$team[2]['ids']).";',`start`=".(time()+60).",`timeout`=2,`t1min`=".($tmin).",`t1max`=".$tmax.",`t2min`=".($tmin).",`t2max`=".$tmax.",`level`=6,`podan`='".date("H:i")."',`t1c`=".count($team[1]['ids']).",`t2c`=".count($team[2]['ids']).",`stavka`=0,`blood`=0,`fond`=0,`price`=0,`nomagic`='{$nomagic}',`autoblow`=1,`am1`=0,`am2`=0,`ae1`=0,`ae2`=0,`t1hist`='".get_str_align($team[1]['aligns'])."',`t2hist`='".get_str_align($team[2]['aligns'])."',`bcl`=0,`subtype`=0,`zcount`=0,`hz`=0;");
										echo "INSERT INTO `zayavka` SET `coment`='Бой склонностей',`type`=3,`team1`='".implode(";",$team[1]['ids']).";',`team2`='".implode(";",$team[2]['ids']).";',`start`=".(time()+60).",`timeout`=2,`t1min`=".($tmin).",`t1max`=".$tmax.",`t2min`=".($tmin).",`t2max`=".$tmax.",`level`=6,`podan`='".date("H:i")."',`t1c`=".count($team[1]['ids']).",`t2c`=".count($team[2]['ids']).",`stavka`=0,`blood`=0,`fond`=0,`price`=0,`nomagic`='{$nomagic}',`autoblow`=1,`am1`=0,`am2`=0,`ae1`=0,`ae2`=0,`t1hist`='".get_str_align($team[1]['aligns'])."',`t2hist`='".get_str_align($team[2]['aligns'])."',`bcl`=0,`subtype`=0,`zcount`=0,`hz`=0;";	
										
										if (mysql_affected_rows()>0)
											{
												$new_zay_id=mysql_insert_id();
												$all_owners=array_merge($team[1]['ids'], $team[2]['ids']);
												mysql_query("UPDATE zayavka_turn set zayid='{$new_zay_id}' where owner in (".implode(",",$all_owners).") ");
												echo "UPDATE zayavka_turn set zayid='{$new_zay_id}' where owner in (".implode(",",$all_owners).") ";
												//Отправляем групповой чат 
												$txt='<font color=red>Внимание!</font> Ваш поединок склонностей начнется через 60 сек.';
												addch_group($txt,$all_owners);
											}


								break;	
								}
							}
					$fails++;
					}
					
				if ((count($aligns_array)==$fails) and ($t==1))
				{
				//пошлись повсем склонкам и так и не набрали Т1
				//т2 нет смысла искать
				break;				
				}
		}
		unset($old_align_key);
		
		
		
}


lockDestroy("cron_op_zayav_job");
?>