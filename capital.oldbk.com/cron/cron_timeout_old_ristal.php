#!/usr/bin/php
<?php
ini_set('display_errors','On');
$CITY_NAME='capitalcity';
include "/www/".$CITY_NAME.".oldbk.com/cron/init.php";
include "/www/".$CITY_NAME.".oldbk.com/fsystem.php";
if( !lockCreate("cron_timeout_job") ) {
    exit("Script already running.");
}


//addchp ('<font color=red>Внимание!</font> Start Time out','{[]}Bred{[]}');


if ($CITY_NAME=='capitalcity')  { $cnis='cap'; }   else { $cnis='ava'; }

// техническая ничья через 15 минут


$time = time();


$q = mysql_query("SELECT * FROM battle WHERE type not in (10,60,61,276,277,278,279,280,281,282,216,217,218,219,220,221,222)  AND status_flag='0'  AND win='3' AND ( (     (60*15+`to1`) < {$time}   )     OR    (    (60*15+`to2`)   <   {$time}  )   )");
while($bd = mysql_fetch_array($q)) 
	{
	mysql_query("UPDATE battle SET t1_dead='finlog' WHERE id='{$bd['id']}' and  t1_dead='' ;");
	if (mysql_affected_rows()>0)
		{
		finish_battle(0, $bd, $bd[blood], $bd[type] , $bd[fond]);
		addlog($bd['id'],"<span class=date>".date("H:i")."</span> Бой закончен по таймауту. Техническая ничья по бездействию в течении 15 мин.<BR>");
		addlog($bd['id'],get_text_broken($bd));
		}
	}
////////////////////////////////////////////////////////////////



function load_mass_items_by_id_c($telo)
{

//загружаем шмотки все кроме магий и подарков и всякой херни надо добавить
// загруженный масив буит нужен для расчетов и отображения
//$query_telo_dess = mysql_query("SELECT * FROM inventory WHERE dressed = 1 AND `type`!=12 AND owner ={$telo[id]} ");
$query_telo_dess =mysql_query_cache("SELECT * FROM oldbk.inventory WHERE dressed = 1 AND `type`!=12 AND owner ={$telo[id]} ",false,24*3600);

	$telo_magicIds   = array();
	$telo_magicIds[] = 0;
	$telo_wearItems  = array();

////////////////////////////////
	$totsumm=0;
$telo_wearItems[krit_mf]=0;
$telo_wearItems[akrit_mf]=0;
$telo_wearItems[uvor_mf]=0;
$telo_wearItems[auvor_mf]=0;
$telo_wearItems[bron1]=0;
$telo_wearItems[bron2]=0;
$telo_wearItems[bron3]=0;
$telo_wearItems[bron4]=0;
$telo_wearItems[min_u]=0;
$telo_wearItems[max_u]=0;
$telo_wearItems[allsumm]=0;
$telo_wearItems[ups]=0;
$telo_wep[mast]=0;
$telo_wearItems[сhem]='';

//// тут потом можно загружать еще чето что надо
/// а пока тольк огрузим ниже мастерство для профильного оружия
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	//while($row = mysql_fetch_assoc($query_telo_dess)) {
	while(list($k,$row) = each($query_telo_dess)) {	
	    $telo_wearItems[$row['id']] = $row;
	        $totsumm+=$row['cost'];
	$telo_wearItems[krit_mf]+=$row[mfkrit];
	$telo_wearItems[akrit_mf]+=$row[mfakrit];
	$telo_wearItems[uvor_mf]+=$row[mfuvorot];
	$telo_wearItems[auvor_mf]+=$row[mfauvorot];
		$telo_wearItems[bron1]+=$row[bron1];
		$telo_wearItems[bron2]+=$row[bron2];
		$telo_wearItems[bron3]+=$row[bron3];
		$telo_wearItems[bron4]+=$row[bron4];
	$telo_wearItems[min_u]+=$row[minu];
	$telo_wearItems[max_u]+=$row[maxu];
		$telo_wearItems[ups]+=$row[ups];

		if($row['includemagic'] > 0) {
	        $telo_magicIds[] = $row['includemagic'];
		}
		// шо за пушка
		if ($row[id]==$telo[weap])
		 	{
			$telo_wep=load_wep($row,$telo);
		 	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	} // end of while
//////////////////////////////////////////////////////////////////////////////////////////////////////
	$telo_wearItems[allsumm]=$totsumm; // запомним общую стоимость своий шмоток
	//fix если кулак и оружие не было просчитано
		if (($telo[weap]==0) and (!$telo_wep))
		 	{
		 	$kulak[otdel]=0;
		 	$telo_wep=load_wep($kulak,$telo);
		 	}
//////////////////////////////////////////////////////////////////////////////////////////////////////
	// формула вычисления мин - макс урон для меня
	$telo_wearItems[min_u] = round((floor($telo['sila']/3) + 1) + $telo['level'] + $telo_wearItems[min_u] * (1 + 0.07 * $telo_wep[mast]));
	$telo_wearItems[max_u] = round((floor($telo['sila']/3) + 4) + $telo['level'] + $telo_wearItems[max_u] * (1 + 0.07 * $telo_wep[mast]));
//////////////////////////////////////////////////////////////////////////////////////////////////////
// текстовый указатель на оружие
 	$telo_wearItems[сhem]=$telo_wep[chem];
 	$telo_wearItems[mast]=$telo_wep[mast];
///  fix из старой боевки для младших уровней по урону //////////////////////////////////
	if($telo_wearItems[сhem] == 'kulak' && (int)$telo['level'] < 4)
				{
					$telo_wearItems[min_u] += 3;
					$telo_wearItems[max_u] += 6;
				}
////////// бонус в кулачке нейтарлам мастерство + иего уровень	////////////////////////
	if($telo_wearItems[сhem] == 'kulak' && (int)$telo['align'] ==2)
				{
					$telo_wearItems[min_u] += $telo[level];
					$telo_wearItems[max_u] += $telo[level];
				}
///////////////////////////////////////////////////////////////////////////////////
///закрузка названий для магии встроеной
//	$query_telo_mag = mysql_query("SELECT * FROM magic WHERE id IN (" . implode(", ", $telo_magicIds) . ")");
//	while($row = mysql_fetch_assoc($query_telo_mag)) {
//	    $telo_magicItems[$row['id']] = $row;
//	}
//////////////////////////////////////////////////////////////////////////////////
	$telo_wearItems[incmagic]=$telo_magicItems;
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

return $telo_wearItems;
}

///


function exit_dress2($telo,$goto)
{

///////////////////////////////////////////////////////////////////////////////
		   if ($goto>0)
		     {
		     ///загружаем параметры prof=0 для выхода
		     $telo_real=mysql_fetch_array(mysql_query("SELECT * FROM `users_profile` WHERE  prof=0 and  `owner` = '{$telo[id]}' LIMIT 1;"));
		     if ($telo_real[bpbonushp] >0)
		     {
		     //если был боныс хп - проверяем незакончился ли он
		     $hp_bonus=mysql_fetch_array(mysql_query("select * from effects where owner='{$telo[id]}' and (type=1001 or  type=1002 or type=1003) "));
		     if ($hp_bonus[id]>0)
		       {
		       //все ок эфект еще висит

		       }
		       else
		       {
		       //эфекта такого уже нет!
		       //снимаем его ручками, т.к. в кроене он не снялся
		       $telo_real[maxhp]=$telo_real[maxhp]-$telo_real[bpbonushp];
       		       $telo_real[bpbonushp]=0;
			       if ($telo_real[hp]>$telo_real[maxhp])
			       		{
			       		$telo_real[hp]=$telo_real[maxhp];
			       		}
		       }
		     }
		     //идем дальше
		     //обновляем инвентарь
		     //1. удаляем шаблонные вещи
		     mysql_query("delete from inventory  where owner='{$telo[id]}' and bs_owner=3 and type!=12");
		     //2.устанавливаем родные шмотки
		     mysql_query("update inventory  set dressed=1 where id in ({$telo_real[sergi]},{$telo_real[kulon]},{$telo_real[perchi]},{$telo_real[weap]},{$telo_real[bron]},{$telo_real[r1]},{$telo_real[r2]},{$telo_real[r3]},{$telo_real[helm]},{$telo_real[shit]},{$telo_real[boots]},{$telo_real[nakidka]},{$telo_real[rubashka]}) AND owner='{$telo[id]}' and dressed=0 ");
		     //3. обновляем чарчика
		     $sk_row=" `sila`='{$telo_real[sila]}',`lovk`='{$telo_real[lovk]}',`inta`='{$telo_real[inta]}',`vinos`='{$telo_real[vinos]}',`intel`='{$telo_real[intel]}',
		`mudra`='{$telo_real[mudra]}',`duh`='{$telo_real[duh]}',`bojes`='{$telo_real[bojes]}',`noj`='{$telo_real[noj]}',`mec`='{$telo_real[mec]}',`topor`='{$telo_real[topor]}',`dubina`='{$telo_real[dubina]}',
		`maxhp`='{$telo_real[maxhp]}',`hp`='{$telo_real[hp]}',`maxmana`='{$telo_real[maxmana]}',`mana`='{$telo_real[mana]}',`sergi`='{$telo_real[sergi]}',`kulon`='{$telo_real[kulon]}',`perchi`='{$telo_real[perchi]}',
		`weap`='{$telo_real[weap]}',`bron`='{$telo_real[bron]}',`r1`='{$telo_real[r1]}',`r2`='{$telo_real[r2]}',`r3`='{$telo_real[r3]}',`helm`='{$telo_real[helm]}',`shit`='{$telo_real[shit]}',`boots`='{$telo_real[boots]}',
		`stats`='{$telo_real[stats]}',`master`='{$telo_real[master]}',`nakidka`='{$telo_real[nakidka]}',`rubashka`='{$telo_real[rubashka]}',`mfire`='{$telo_real[mfire]}',`mwater`='{$telo_real[mwater]}',`mair`='{$telo_real[mair]}',`mearth`='{$telo_real[mearth]}',
		`mlight`='{$telo_real[mlight]}',`mgray`='{$telo_real[mgray]}',`mdark`='{$telo_real[mdark]}', `bpbonushp`='{$telo_real[bpbonushp]}'  ";
		      mysql_query("UPDATE `users` SET ".$sk_row." , `users`.`id_grup` = '0' ,  `users`.`room` = '{$goto}' WHERE  `users`.`id` = '{$telo[id]}' ;");


		     }
		     else
		     {
		     echo "Ошибка направления...";
		     }
/////////////////////////////////////////////////////////////////////////////
}

//авто окончания одиночных турниров

function get_close_and_next($type)
{
$next=time()+21600;//6 часов

//second fix
$next=mktime(date("H",$next),date("i",$next), 0, date("m",$next), date("d",$next), date("Y",$next));

mysql_query("UPDATE tur_raspis SET status=0, sendmsg=0 ,start_time=".$next." where tur_type={$type} and (status=2 OR status=22);");

}

function get_look_tur($ptype)
{
 $look=mysql_query("SELECT * from users where room='{$ptype}' ;");
 $rfL=mysql_affected_rows();

			if ( $rfL > 0)
			{
			$ret=true;
			}
		else
			{
			//если никого нет значит турнир окончен
			$addl="<span class=date2>".date("d.m.y H:i")."</span> <b>Турнир:окончен - <i>нет победителя</i></b><BR>";
		 	mysql_query("UPDATE `tur_logs` SET active=0, end_time='".time()."' , `logs`= CONCAT(`logs`,'{$addl}') WHERE   `type`='{$ptype}'  and active=1;");
	   		$ret=false;
			}
return $ret;
}


function get_look_tur_group($ptype)
{
 $look=mysql_query("SELECT * from tur_grup where type='{$ptype}' ;");
 $rfL=mysql_affected_rows();

			if ( $rfL > 0)
			{
			$ret=true;
			}
		else
			{
			//если никого нет значит турнир окончен
			$addl="<span class=date2>".date("d.m.y H:i")."</span> <b>Турнир:окончен - <i>нет победителя</i></b><BR>";
		 	mysql_query("UPDATE `tur_logs` SET active=0, end_time='".time()."' , `logs`= CONCAT(`logs`,'{$addl}') WHERE   `type`='{$ptype}'  and active=1;");
	   		$ret=false;
			}
return $ret;
}




	//пока одиночные
	$otn_tur=mysql_fetch_array(mysql_query("SELECT * FROM `tur_raspis` WHERE  tur_type=210 LIMIT 1;"));

	//system message
	if ( ($otn_tur[start_time]-1800<=time()) AND ($otn_tur[status]==0) AND ($otn_tur[sendmsg]==0) )
	{
		mysql_query("UPDATE tur_raspis SET sendmsg=1 where tur_type=210 and status=0;");
		$TEXT='Начало турнира для 6-11 уровней в Одиночных сражениях на Ристалище:'.date("d.m.y H:i",$otn_tur['start_time']);
		addch2all($TEXT,$bot_city);

	}
	if (($otn_tur[start_time]<=time())AND($otn_tur[status]==0))
	{
	//echo "run...";
	//пришло время стартануть т.к. статус 0
	mysql_query("UPDATE tur_raspis SET status=1 where tur_type=210 and status=0;");
	   	  //тут если операция ок делаем лог
		   	$arf1=mysql_affected_rows();
			//echo "R".$rf;
			if ( $arf1 > 0)
			   {
			   //если прокатил апдейт то пишем влог об открытии турнира
			   mysql_query("INSERT INTO `tur_logs` (`type`,`start_time`,`logs`,`active`)
			   			 VALUES
 			   			 (216,'{$otn_tur['start_time']}','<span class=date2>".date("d.m.y H:i")."</span> <b>Турнир: открыт</b><BR>',1),
			   			 (217,'{$otn_tur['start_time']}','<span class=date2>".date("d.m.y H:i")."</span> <b>Турнир: открыт</b><BR>',1),
			   			 (218,'{$otn_tur['start_time']}','<span class=date2>".date("d.m.y H:i")."</span> <b>Турнир: открыт</b><BR>',1),
			   			 (219,'{$otn_tur['start_time']}','<span class=date2>".date("d.m.y H:i")."</span> <b>Турнир: открыт</b><BR>',1),
			   			 (220,'{$otn_tur['start_time']}','<span class=date2>".date("d.m.y H:i")."</span> <b>Турнир: открыт</b><BR>',1),
			   			 (221,'{$otn_tur['start_time']}','<span class=date2>".date("d.m.y H:i")."</span> <b>Турнир: открыт</b><BR>',1) ;");
			   // чистим старые группы если есть такие
			    //mysql_query("delete from tur_grup where type in (216,217,218,219,220);");
			   }
	}
	else if ((($otn_tur[start_time]+300)<=time())AND($otn_tur[status]==1))
	{
	//echo "close...";
		mysql_query("UPDATE tur_raspis SET status=22 where tur_type=210 and status=1;"); //!!!обязательно старый лок
 		   	   	  //тут если операция ок делаем лог
 		   	   	$arf2=mysql_affected_rows();
				if ( $arf2 > 0)
				 {
				 //апдейт прошел
				 $closeis= $otn_tur['start_time']+300; //5 мин
				 $gotime=time()+1800; //30 min
				 mysql_query("UPDATE `tur_logs` SET gotime='{$gotime}',`logs`= CONCAT(`logs`,'<span class=date2>".date("d.m.y H:i",$closeis)."</span> <b>Турнир: набор окончен.</b><BR>') WHERE  `type` in (216,217,218,219,220,221) and `start_time`='{$otn_tur['start_time']}' and active=1;");
				 }

	// не порали закрывать ?
	//ищем тех кто уже ждет турнира и не имеет фамильного герба
	$chiters=mysql_query("select * from users u where ( (room>210 and room<239) and 5000 not in (select prototype from inventory where owner=u.id and setsale=0)) and id_city='{$bot_city}' ");
		if (mysql_affected_rows() > 0 )
		{
			//есть такие сабаки
			while($gorow=mysql_fetch_array($chiters))
				{
				//вы брасываем такого гада
				// ставим профиль
	        		addchp ('<font color=red>Внимание!</font><b> Вы выбыли из турнира! У Вас неоказалось фамильного герба!</b>','{[]}'.$gorow['login'].'{[]}',$gorow[room],$gorow[id_city]);
				exit_dress2($gorow,210);
				}
		}

	///////////////////////////////////////
	//проверяем количество оставшихся и если оно меньше 5 - то отменяем турнир - проверка по комнатам
	$getcounts=mysql_query("select count(id) as c, room from users where room in (216,217,218,219,220,221) group by room");
		if (mysql_affected_rows() > 0 )
		{

			while($crow=mysql_fetch_array($getcounts))
				{
				if ($crow[c]<9)
					{
					//турнир не состоялся
					//выгоняем людей из этой комнаты
					////////////////
					$chiters=mysql_query("select * from users where `room` = '{$crow[room]}' and id_city='{$bot_city}'   ");
					if (mysql_affected_rows() > 0 )
					{
					while($gorow=mysql_fetch_array($chiters))
						{
						//вы брасываем такого гада
						// ставим профиль
			        		addchp ("<b>Турнир:".$rooms[$crow[room]]."</b>, не может начаться по причине:<i>мало участников</i> ",'{[]}'.$gorow['login'].'{[]}',$gorow['room'],$gorow['id_city']);
						exit_dress2($gorow,210);
						}
					}
					/////////////////
					mysql_query("UPDATE `tur_logs` SET `logs`= CONCAT(`logs`,'<span class=date2>".date("d.m.y H:i",$closeis)."</span> <b>Турнир, не может начаться по причине:<i>мало участников</i></b><BR>') WHERE  `type`='{$crow[room]}' and active=1;");
					}
					else
					{
					$good_210=true;
					//типа есть люди у данного типа
					// надо удалить у всех людией в текущей комнате герб
					// запрашиваем людей
						$get_users_in=mysql_query("select * from users where room='{$crow[room]}' and id_city='{$bot_city}'  ");
						while($urow=mysql_fetch_array($get_users_in))
						{
//						mysql_query("delete from inventory where owner='{$urow[id]}' and prototype=5000 and present='DRUP_HUP' and setsale=0 LIMIT 1;");
						mysql_query("delete from inventory where owner='{$urow[id]}' and prototype=5000 and setsale=0 LIMIT 1;");
			        		addchp ('<font color=red>Внимание!</font><b>Вы отдали <i>«Фамильный Герб»</i> за участие в турнире!</b>','{[]}'.$urow['login'].'{[]}',$urow['room'],$urow['id_city']);
						}
					}

				}

		}

		if ($good_210)
		{
		mysql_query("UPDATE tur_raspis SET status=2 where tur_type=210 and status=22;"); 
		}


	}
	else if	(($otn_tur[status]==2) OR ($otn_tur[status]==22) )
	{

			$nowtr = mysql_query("SELECT * FROM `tur_logs` WHERE type in (216,217,218,219,220,221) and active=1 ; ");
			$rf1=mysql_affected_rows();
			if ( $rf1 > 0)
			{
			$finc=0;
				while($row=mysql_fetch_array($nowtr))
				{
				if (get_look_tur($row[type])!=true)
					{
					//считаем законченые только что турниры
					//echo "close".$row[type];
					//echo "<br>";
					$finc++;
					}
				///////////////////
				//обработка тайма по не нападению
					if ($row[gotime] > 0)
						{
						//echo "gotime -- is<br>";
						///есть
						///проверяем бои вдруг есть
						$count_battle=mysql_fetch_array(mysql_query("SELECT count(*) FROM `battle` WHERE win=3 and type='{$row[type]}';"));
						if ($count_battle[0] >0 )
							{
							//echo "gotime -- have battle<br>";
							//есть бои надо обнулить таймер
							mysql_query("UPDATE `tur_logs` set gotime=0 where id='{$row[id]}' ; ");
							}
							else
							{
							//echo "gotime -- no battle<br>";
							//нету боев этого типа
							//смотрим врмя
							if ($row[gotime] <=time())
								{
								//echo "gotime -- istime<br>";
								//нихто не нападал пришло время всех выгоняем из турнира нахер
								//выбираем всех кто есть в турнире

					////////////////
					$chiters=mysql_query("select * from users where `room` = '{$row[type]}' and id_city='{$bot_city}'  ");
					if (mysql_affected_rows() > 0 )
					{
					while($gorow=mysql_fetch_array($chiters))
						{
						//вы брасываем такого гада
						// ставим профиль
			        		addchp ("<b>Турнир:".$rooms[$crow[room]]."</b>, окончен <i>отказ в нападении</i> ",'{[]}'.$gorow['login'].'{[]}',$gorow['room'],$gorow['id_cty']);
						exit_dress2($gorow,210);
						}
					}
					/////////////////

								// нет победителя
								 mysql_query("UPDATE `tur_logs` SET `logs`= CONCAT(`logs`,'<span class=date2>".date("d.m.y H:i")."</span> <b> Все живые участники покидают турнир - <i>отказ в нападении</i></b><BR>') WHERE  `type`='{$row[type]}' and active=1;");

								}
								else
								{
								//echo "gotime -- no time<br>";
								}

							}

						}
						else
						{
						//echo "gotime --0<br>";
						//нет таймера
						///проверяем бои есть?
						$count_battle=mysql_fetch_array(mysql_query("SELECT count(*) FROM `battle` WHERE win=3 and type='{$row[type]}';"));
							if ($count_battle[0] >0 )
							{
							//все окк
							}
							else
							{
							//нету боев надо выставить таймер
							$gotime=time()+1800; //30 min
							mysql_query("UPDATE `tur_logs` set gotime='{$gotime}' where id='{$row[id]}' ; ");
							}

						}

				}
			if ($rf1==$finc)
				{
				// надо закрыть и поставить новое время
				//ставим следующее время турнира
				//echo "закрыли по счету";
				get_close_and_next(210);

				}
			}
			else
			{
			//активных нету турниров
			// надо закрыть турнир сам и поставить новое время
			//echo "Нет активных...закрываем турнир";
			get_close_and_next(210);
			}
	}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// турниры отрядов
	$otn_tur=mysql_fetch_array(mysql_query("SELECT * FROM `tur_raspis` WHERE  tur_type=270 LIMIT 1;"));

	//system message
	if ( ($otn_tur[start_time]-1800<=time()) AND ($otn_tur[status]==0) AND ($otn_tur[sendmsg]==0) )
	{
		mysql_query("UPDATE tur_raspis SET sendmsg=1 where tur_type=270 and status=0;");
		$TEXT='Начало турнира для 6-11 уровней в Сражениях отрядов на Ристалище:'.date("d.m.y H:i",$otn_tur['start_time']);
		addch2all($TEXT,$bot_city);

	}

	if (($otn_tur[start_time]<=time())AND($otn_tur[status]==0))
	{
	//echo "run...";
	//пришло время стартануть т.к. статус 0
	mysql_query("UPDATE tur_raspis SET status=1 where tur_type=270 and status=0;");
	   	  //тут если операция ок делаем лог
		   	$arf1=mysql_affected_rows();
			if ( $arf1 > 0)
			   {
			   //если прокатил апдейт то пишем влог об открытии турнира
			   mysql_query("INSERT INTO `tur_logs` (`type`,`start_time`,`logs`,`active`)
			   			 VALUES
 			   			 (276,'{$otn_tur['start_time']}','<span class=date2>".date("d.m.y H:i")."</span> <b>Турнир: открыт</b><BR>',1),
			   			 (277,'{$otn_tur['start_time']}','<span class=date2>".date("d.m.y H:i")."</span> <b>Турнир: открыт</b><BR>',1),
			   			 (278,'{$otn_tur['start_time']}','<span class=date2>".date("d.m.y H:i")."</span> <b>Турнир: открыт</b><BR>',1),
			   			 (279,'{$otn_tur['start_time']}','<span class=date2>".date("d.m.y H:i")."</span> <b>Турнир: открыт</b><BR>',1),
			   			 (280,'{$otn_tur['start_time']}','<span class=date2>".date("d.m.y H:i")."</span> <b>Турнир: открыт</b><BR>',1),
			   			 (281,'{$otn_tur['start_time']}','<span class=date2>".date("d.m.y H:i")."</span> <b>Турнир: открыт</b><BR>',1) ;");
			   // чистим старые группы если есть такие
			    mysql_query("delete from tur_grup where type in (276,277,278,279,280,281);");
			   }
	}
	else if ((($otn_tur[start_time]+300)<=time())AND($otn_tur[status]==1))
	{

	//echo "close...";
	//print_r($otn_tur);
	// не порали закрывать ?
	mysql_query("UPDATE tur_raspis SET status=22 where tur_type=270 and status=1;"); //!!!обязательно старый лок
 		   	   	  //тут если операция ок делаем лог
 		   	   	$arf2=mysql_affected_rows();
				if ( $arf2 > 0)
				 {
				 //апдейт прошел
				 $closeis= $otn_tur['start_time']+300; //5 мин
				 $gotime=time()+1800; //30 min
				 mysql_query("UPDATE `tur_logs` SET gotime='{$gotime}',`logs`= CONCAT(`logs`,'<span class=date2>".date("d.m.y H:i",$closeis)."</span> <b>Турнир: набор окончен.</b><BR>') WHERE  `type` in (276,277,278,279,280,281) and `start_time`='{$otn_tur['start_time']}' and active=1;");
				 }
	////////////////////////////////////////////////////////////
	//1.1
	//выгоняем тех кто не имеет герба
	$chiters=mysql_query("select * from users u where ((room>270 and room<299) and 5000 not in (select prototype from inventory where owner=u.id and setsale=0)) and id_city='{$bot_city}'  ");
		if (mysql_affected_rows() > 0 )
		{
			//есть такие сабаки
			while($gorow=mysql_fetch_array($chiters))
				{
				//выбрасываем такого гада
				if ($gorow[id_grup] > 0)
				 {
				 //того кого выгнали без герба был в отряде - удаляем весь отряд из-за этого падонка
				 //удаляем отряд
				  mysql_query("DELETE FROM tur_grup where id='{$gorow[id_grup]}'  ;");
				 // обновляем людей
 				 exit_dress2($gorow,270);
 	        		 addchp ('<font color=red>Внимание!</font><b> Из-за Вас отряд выбывает из турнира! У Вас неоказалось фамильного герба!</b>','{[]}'.$gorow['login'].'{[]}',$gorow['room'],$gorow['id_city']);
				 }
				 else
				 {
				 //тот кто оказался без герба не в группы
 				 exit_dress2($gorow,270);
	        		 addchp ('<font color=red>Внимание!</font><b> Вы выбыли из турнира! У Вас неоказалось фамильного герба!</b>','{[]}'.$gorow['login'].'{[]}',$gorow['room'],$gorow['id_city']);
				 }
				}
		}
	///////////////////////////////////////
	//заявка закрывается
	//1.2.выгоняем всех кто не в отряде
	$exit1=mysql_query("SELECT * from  users  WHERE  `id_grup` = 0 AND  `users`.`room`>270 AND  `room`<299 ;");
         while($gorow=mysql_fetch_array($exit1))
	  {
	  exit_dress2($gorow,270);
	   }


    //2. выгоняем всех кто не набрал 3-х человек
    //   a. выбираем всех не набраных команд нужного типа и активных
	$go_out=mysql_query("select * from users where (id_grup>0 and room in (276,277,278,279,280,281) and id_grup in (select id  from tur_grup where (owner1=0 or owner2=0 OR owner3=0) and `type` in (276,277,278,279,280,281) and active=1 )) and id_city='{$bot_city}'  ");
      	if ( mysql_affected_rows() > 0)
				{
				//1. грохаем такую ггруппу
				while($orow=mysql_fetch_array($go_out))
					{
					mysql_query("DELETE FROM tur_grup where id='{$orow[id_grup]}'  ;");
					//2. обновляем людей
				  	exit_dress2($orow,270);
				  	addchp ('<font color=red>Внимание!</font><b> Вы выбыли из турнира! Неполный отряд!</b>','{[]}'.$orow[login].'{[]}',$orow[room],$orow[id_city]);
					}
				}
	///////////////////////////////////////

	//проверяем количество оставшихся и если оно меньше 3+3+3 - то отменяем турнир - проверка по комнатам
	$getcounts=mysql_query("select count(id) as c, room from users where room in (276,277,278,279,280,281) group by room");
		if (mysql_affected_rows() > 0 )
		{

			while($crow=mysql_fetch_array($getcounts))
				{
				if ($crow[c]<9)
					{
					//турнир не состоялся
					//выгоняем людей из этой комнаты
					$exit2=mysql_query("SELECT * from  users  WHERE `room` = '{$crow[room]}'  ;");
				         while($gorow=mysql_fetch_array($exit2))
					  {
					  exit_dress2($gorow,270);
					   }

					addch ("<b>Турнир:".$rooms[$crow[room]]."</b>, не может начаться по причине:<i>мало участников</i> ",$crow[room],$bot_city);
					mysql_query("UPDATE `tur_logs` SET `logs`= CONCAT(`logs`,'<span class=date2>".date("d.m.y H:i",$closeis)."</span> <b>Турнир, не может начаться по причине:<i>мало участников</i></b><BR>') WHERE  `type`='{$crow[room]}' and active=1;");
					//удаляем группу
					mysql_query("DELETE FROM tur_grup where type='{$crow[room]}'  ;");
					}
					else
					{
					$good_270=true;
					//типа есть люди у данного типа
					// надо удалить у всех людией в текущей комнате герб
					// запрашиваем людей
						$get_users_in=mysql_query("select * from users where room='{$crow[room]}' and id_city='{$bot_city}'  ");
						while($urow=mysql_fetch_array($get_users_in))
						{
						mysql_query("delete from inventory where owner='{$urow[id]}' and prototype=5000 and setsale=0 LIMIT 1;");
			        		addchp ('<font color=red>Внимание!</font><b>Вы отдали <i>«Фамильный Герб»</i> за участие в турнире!</b>','{[]}'.$urow['login'].'{[]}',$urow['room'],$urow['id_city']);
						}
					}

				}

		}

		if ($good_270)
		{
		mysql_query("UPDATE tur_raspis SET status=2 where tur_type=270 and status=22;"); 
		}






	}
	else if	(($otn_tur[status]==2) OR ($otn_tur[status]==22) )
	{

			$nowtr = mysql_query("SELECT * FROM `tur_logs` WHERE type in (276,277,278,279,280,281) and active=1 ; ");
			$rf1=mysql_affected_rows();
			if ( $rf1 > 0)
			{
			$finc=0;
				while($row=mysql_fetch_array($nowtr))
				{
				if (get_look_tur($row[type])!=true)
					{
					//считаем законченые только что турниры
					//echo "close".$row[type];
					//echo "<br>";
					$finc++;
					}
				///////////////////
				//обработка тайма по не нападению
					if ($row[gotime] > 0)
						{
						//echo "gotime -- is<br>";
						///есть
						///проверяем бои вдруг есть
						$count_battle=mysql_fetch_array(mysql_query("SELECT count(*) FROM `battle` WHERE win=3 and type='{$row[type]}';"));
						if ($count_battle[0] >0 )
							{
							//echo "gotime -- have battle<br>";
							//есть бои надо обнулить таймер
							mysql_query("UPDATE `tur_logs` set gotime=0 where id='{$row[id]}' ; ");
							}
							else
							{
							//echo "gotime -- no battle<br>";
							//нету боев этого типа
							//смотрим врмя
							if ($row[gotime] <=time())
								{
								//echo "gotime -- istime<br>";
								//нихто не нападал пришло время всех выгоняем из турнира нахер
								//выбираем всех кто есть в турнире
								//удаляем все группы этого типа
								mysql_query("DELETE FROM tur_grup where id='{$row[type]}' and active=1  ;");
								//2. обновляем людей
							  	$exit3=mysql_query("SELECT * from  users  WHERE `room`='{$row[type]}'  ;");
							         while($gorow=mysql_fetch_array($exit3))
								  {
								  exit_dress2($gorow,270);
								   }

								// нет победителя
								mysql_query("UPDATE `tur_logs` SET `logs`= CONCAT(`logs`,'<span class=date2>".date("d.m.y H:i")."</span> <b> Все живые участники покидают турнир - <i>отказ в нападении</i></b><BR>') WHERE  `type`='{$row[type]}' and active=1;");

								}
								else
								{
								//echo "gotime -- no time<br>";
								}

							}

						}
						else
						{
						//echo "gotime --0<br>";
						//нет таймера
						///проверяем бои есть?
						$count_battle=mysql_fetch_array(mysql_query("SELECT count(*) FROM `battle` WHERE win=3 and type='{$row[type]}';"));
							if ($count_battle[0] >0 )
							{
							//все окк
							}
							else
							{
							//нету боев надо выставить таймер
							$gotime=time()+1800; //30 min
							mysql_query("UPDATE `tur_logs` set gotime='{$gotime}' where id='{$row[id]}' ; ");
							}

						}

				}
			if ($rf1==$finc)
				{
				// надо закрыть и поставить новое время
				//ставим следующее время турнира
				//echo "закрыли по счету";
				get_close_and_next(270);

				}
			}
			else
			{
			//активных нету турниров
			// надо закрыть турнир сам и поставить новое время
			//echo "Нет активных...закрываем турнир";
			get_close_and_next(270);
			}
	}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// турниры групп
function get_max_demag($ttype)
{
$get_dem=mysql_fetch_array(mysql_query("SELECT * FROM tur_grup WHERE (demag=(SELECT MAX(demag) FROM tur_grup where type={$ttype})) and type={$ttype} "));
return $get_dem;
}


	$otn_tur=mysql_fetch_array(mysql_query("SELECT * FROM `tur_raspis` WHERE  tur_type=240 LIMIT 1;"));

	//system message
	if ( ($otn_tur[start_time]-1800<=time()) AND ($otn_tur[status]==0) AND ($otn_tur[sendmsg]==0) )
	{
		mysql_query("UPDATE tur_raspis SET sendmsg=1 where tur_type=240 and status=0;");
		$TEXT='Начало турнира для 7-11 уровней в Групповых сражениях на Ристалище:'.date("d.m.y H:i",$otn_tur['start_time']);
		addch2all($TEXT,$bot_city);

	}

	if (($otn_tur[start_time]<=time())AND($otn_tur[status]==0))
	{

	//echo "run...";
	//пришло время стартануть т.к. статус 0
	mysql_query("UPDATE tur_raspis SET status=1 where tur_type=240 and status=0;");
	   	  //тут если операция ок делаем лог
		   	$arf1=mysql_affected_rows();
			if ( $arf1 > 0)
			   {
			   //если прокатил апдейт то пишем влог об открытии турнира
			   mysql_query("INSERT INTO `tur_logs` (`type`,`start_time`,`logs`,`active`)
			   			 VALUES
			   			 (247,'{$otn_tur['start_time']}','<span class=date2>".date("d.m.y H:i")."</span> <b>Турнир: открыт</b><BR>',1),
			   			 (248,'{$otn_tur['start_time']}','<span class=date2>".date("d.m.y H:i")."</span> <b>Турнир: открыт</b><BR>',1),
			   			 (249,'{$otn_tur['start_time']}','<span class=date2>".date("d.m.y H:i")."</span> <b>Турнир: открыт</b><BR>',1),
			   			 (250,'{$otn_tur['start_time']}','<span class=date2>".date("d.m.y H:i")."</span> <b>Турнир: открыт</b><BR>',1),
			   			 (251,'{$otn_tur['start_time']}','<span class=date2>".date("d.m.y H:i")."</span> <b>Турнир: открыт</b><BR>',1) ;");
			   // чистим старые группы если есть такие
			    mysql_query("delete from tur_grup where type in (247,248,249,250,251);");
			   }
	}
	else if ((($otn_tur[start_time]+300)<=time())AND($otn_tur[status]==1))
	{
	//echo "close...";

	////////////////////////////////////////////////////////////
	mysql_query("UPDATE tur_raspis SET status=2 where tur_type=240 and status=1;"); //!!!обязательно старый лок
 		   	   	  //тут если операция ок делаем лог
 		   	   	$arf2=mysql_affected_rows();
				if ( $arf2 > 0)
				 {
				 //апдейт прошел
				 $closeis= $otn_tur['start_time']+300; //5 мин
				 $gotime=time()+1800; //30 min
				 mysql_query("UPDATE `tur_logs` SET gotime='{$gotime}',`logs`= CONCAT(`logs`,'<span class=date2>".date("d.m.y H:i",$closeis)."</span> <b>Турнир: набор окончен.</b><BR>') WHERE  `type` in (247,248,249,250,251) and `start_time`='{$otn_tur['start_time']}' and active=1;");
				 $otn_tur[status]=2;
				 }

	////////////////////////////////////////////////////////////
	//1.1
	//выгоняем тех кто не имеет герба
	$chiters=mysql_query("select * from users u where ((room>240 and room<269) and 5000 not in (select prototype from inventory where owner=u.id and setsale=0)) and id_city='{$bot_city}' ");
		if (mysql_affected_rows() > 0 )
		{
			//есть такие сабаки
			while($gorow=mysql_fetch_array($chiters))
				{
				//выбрасываем такого гада
				if ($gorow[id_grup] > 0)
				 {
				 //того кого выгнали без герба был в отряде - удаляем весь отряд из-за этого падонка
				 //удаляем отряд
				  mysql_query("DELETE FROM tur_grup where id='{$gorow[id_grup]}'  ;");
				 // обновляем людей
			  	 mysql_query("UPDATE `users` SET `users`.`id_grup` = '0' , `users`.`room` = '240' WHERE  `users`.`id_grup` = '{$gorow[id_grup]}' AND `users`.`room`>240 AND `users`.`room`<269 ;");
 	        		 addchp ('<font color=red>Внимание!</font><b> Из-за Вас отряд выбывает из турнира! У Вас неоказалось фамильного герба!</b>','{[]}'.$gorow['login'].'{[]}',$gorow['room'],$gorow['id_city']);
				 }
				 else
				 {
				 //тот кто оказался без герба не в группе
				 mysql_query("UPDATE `users`  SET  `users`.`id_grup` = 0 , `users`.`room` = '240' WHERE  `users`.`id` = '{$gorow[id]}' and (room>240 and room<269)  ;");
	        		 addchp ('<font color=red>Внимание!</font><b> Вы выбыли из турнира! У Вас неоказалось фамильного герба!</b>','{[]}'.$gorow['login'].'{[]}',$gorow['room'],$gorow['id_city']);
				 }
				}
		}
	///////////////////////////////////////

	 //1.выгоняем всех кто не в отряде
	  mysql_query("UPDATE `users` SET `users`.`room` = '240'  WHERE  `users`.`id_grup` = 0 AND `users`.`room`>240 AND `users`.`room`<269 ;");

 		   //2. выгоняем всех кто не набрал 3-х человек
 		   //   a. выбираем всех не набраных команд нужного типа и активных
	 		$go_out=mysql_query("select * from tur_grup where (owner1=0 or owner2=0 OR owner3=0) and `type` in (247,248,249,250,251) and active=1 ");
 		     	if ( mysql_affected_rows() > 0)
				{
				//1. грохаем такую ггруппу
				while($orow=mysql_fetch_array($go_out))
					{
					mysql_query("DELETE FROM tur_grup where id='{$orow[id]}'  ;");
					//2. обновляем людей
				  	mysql_query("UPDATE `users`  SET `users`.`id_grup` = '0' , `users`.`room` = '240'  WHERE  `users`.`id_grup` = '{$orow[id]}' AND `users`.`room`>240 AND `users`.`room`<269 ;");
					}
				}

	//проверяем количество оставшихся и если оно меньше 3+3+3 - то отменяем турнир - проверка по комнатам
	$getcounts=mysql_query("select count(id) as c, room from users where room in (247,248,249,250,251) group by room");
		if (mysql_affected_rows() > 0 )
		{

			while($crow=mysql_fetch_array($getcounts))
				{
				if ($crow[c]<9)
					{
					//турнир не состоялся
					//выгоняем людей из этой комнаты
					mysql_query("UPDATE `users`  SET `users`.`id_grup` = '0' , `users`.`room` = '240'  WHERE `users`.`room` = '{$crow[room]}' and (room>240 and room<269) ;");
					addch ("<b>Турнир:".$rooms[$crow[room]]."</b>, не может начаться по причине:<i>мало участников</i> ",$crow[room],$bot_city);
					mysql_query("UPDATE `tur_logs` SET `logs`= CONCAT(`logs`,'<span class=date2>".date("d.m.y H:i",$closeis)."</span> <b>Турнир, не может начаться по причине:<i>мало участников</i></b><BR>') WHERE  `type`='{$crow[room]}' and active=1;");
					//удаляем группу
					mysql_query("DELETE FROM tur_grup where type='{$crow[room]}'  ;");
					}
					else
					{
					//типа есть люди у данного типа
					// надо удалить у всех людией в текущей комнате герб
					// запрашиваем людей
						$get_users_in=mysql_query("select * from users where room='{$crow[room]}' and id_city='{$bot_city}'  ");
						while($urow=mysql_fetch_array($get_users_in))
						{
						mysql_query("delete from inventory where owner='{$urow[id]}' and prototype=5000 and setsale=0 LIMIT 1;");
//						mysql_query("delete from inventory where owner='{$urow[id]}' and prototype=5000 and present='DRUP_HUP' and setsale=0 LIMIT 1;");
			        		addchp ('<font color=red>Внимание!</font><b>Вы отдали <i>«Фамильный Герб»</i> за участие в турнире!</b>','{[]}'.$urow['login'].'{[]}',$urow['room'],$urow['id_city']);
						}
					}

				}

		}


	}

	if ($otn_tur[status]==2)
	{

			$nowtr = mysql_query("SELECT * FROM `tur_logs` WHERE `type` in (247,248,249,250,251) and active=1 ; ");
			$rf1=mysql_affected_rows();
			if ( $rf1 > 0)
			{
			$finc=0;
				while($row=mysql_fetch_array($nowtr))
				{
				if (get_look_tur_group($row[type])!=true)
					{
					//считаем законченые только что турниры
					//echo "close".$row[type];
					//echo "<br>";
					$finc++;
					}
					else
					{
					echo "Active turnirs ".$row[type]."<br>";
					//этот турнир идет
					//1. выбираем все группы которые этого типа
					   $all_grups=mysql_query("SELECT * FROM `tur_grup` WHERE  type={$row[type]} ; ");
						while($AGrow=mysql_fetch_array($all_grups))
						{
						//echo "Grup :".$AGrow[type]."  grup id:".$AGrow[id]."<br>";
						if ($AGrow[active]==1)
							{
							//echo "is active <br>";
							//живая тима
							if ($AGrow[battle]==0)
								{
								//для тех кто не в бою - создаем бой
								$tima1=$AGrow[owner1].";";
								$tima1_data="Отряд: <b>«".$AGrow[nazva]."»</b> (".$AGrow[owner_data1].",";
								if ($AGrow[owner2]>0) { $tima1.=$AGrow[owner2].";"; $tima1_data.=$AGrow[owner_data2].","; }
								if ($AGrow[owner3]>0) { $tima1.=$AGrow[owner3].";"; $tima1_data.=$AGrow[owner_data3].","; }
							        $tima1 = substr($tima1, 0, -1); $tima1_data = substr($tima1_data, 0, -1); $tima1_data.=")";

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$c=0;
$id_bot=array();
$bot_team_sql=''; 
$bots_names=''; 
$bots_names_chat='';
$bot_team='';

echo "start batt...";
include "/www/".$CITY_NAME.".oldbk.com/config240.php";
//готовим ботов
$mobot=$row[type];
$moboa=$monstro[$mobot][1];

foreach ($moboa as $k=>$v)
			{

			for ($l=0;$l<$v;$l++)
				{
				$c++;
				//$BOT=mysql_fetch_array(mysql_query("SELECT * from `users` where `id`='".$k."' ;"));
				$BOT=mysql_query_cache("SELECT * from `users` where `id`='".$k."' ;",false,24*3600);
				$BOT = $BOT[0];					
				$BOT['login'].=" (kлoн ".$c.")";
				$BNAME=BNewHist($BOT);
				$BNAME_chat=nick_hist($BOT);
				$BOT_items=load_mass_items_by_id_c($BOT);
				
				mysql_query("INSERT INTO `users_clons` SET `login`='".$BOT['login']."',`sex`='{$BOT['sex']}',
					`level`='{$BOT['level']}',`align`='{$BOT['align']}',`klan`='{$BOT['klan']}',`sila`='{$BOT['sila']}',
					`lovk`='{$BOT['lovk']}',`inta`='{$BOT['inta']}',`vinos`='{$BOT['vinos']}',
					`intel`='{$BOT['intel']}',`mudra`='{$BOT['mudra']}',`duh`='{$BOT['duh']}',`bojes`='{$BOT['bojes']}',`noj`='{$BOT['noj']}',
					`mec`='{$BOT['mec']}',`topor`='{$BOT['topor']}',`dubina`='{$BOT['dubina']}',`maxhp`='{$BOT['maxhp']}',`hp`='{$BOT['maxhp']}',
					`maxmana`='{$BOT['maxmana']}',`mana`='{$BOT['mana']}',`sergi`='{$BOT['sergi']}',`kulon`='{$BOT['kulon']}',`perchi`='{$BOT['perchi']}',
					`weap`='{$BOT['weap']}',`bron`='{$BOT['bron']}',`r1`='{$BOT['r1']}',`r2`='{$BOT['r2']}',`r3`='{$BOT['r3']}',`helm`='{$BOT['helm']}',
					`shit`='{$BOT['shit']}',`boots`='{$BOT['boots']}',`nakidka`='{$BOT['nakidka']}',`rubashka`='{$BOT['rubashka']}',`shadow`='{$BOT['shadow']}',`battle`=1,`bot`=1,
					`id_user`='{$BOT['id']}',`at_cost`='{$BOT_items['allsumm']}',`kulak1`=0,`sum_minu`='{$BOT_items['min_u']}',
					`sum_maxu`='{$BOT_items['max_u']}',`sum_mfkrit`='{$BOT_items['krit_mf']}',`sum_mfakrit`='{$BOT_items['akrit_mf']}',
					`sum_mfuvorot`='{$BOT_items['uvor_mf']}',`sum_mfauvorot`='{$BOT_items['auvor_mf']}',`sum_bron1`='{$BOT_items['bron1']}',
					`sum_bron2`='{$BOT_items['bron2']}',`sum_bron3`='{$BOT_items['bron3']}',`sum_bron4`='{$BOT_items['bron4']}',`ups`='{$BOT_items['ups']}',
					`injury_possible`=0, `battle_t`=2;");
				$id_bot[$c]=mysql_insert_id();
				
				if ($bot_team!='') {
							$bots_names.=$BNAME;
							$bots_names_chat.=", ".$BNAME_chat;
							$bot_team.=";".$id_bot[$c];
							$bot_team_sql.=",".$id_bot[$c];
							}
							else
							{
							$bots_names=$BNAME;
							$bots_names_chat=$BNAME_chat;							
							$bot_team=$id_bot[$c];
							$bot_team_sql=$id_bot[$c];
							}			
				}
				///работает!!
			}

mysql_query("INSERT INTO `battle` (`id`,`coment`,`teams`,`timeout`,`type`,`status`,`t1`,`t2`,`to1`,`to2`,`win`,`t1hist`,`t2hist`)
						VALUES
						(NULL,'Групповой турнир','','3','{$row[type]}','0','{$tima1}','".$bot_team."','".time()."','".time()."',3,'".$tima1_data."','{$bots_names}')");
				$id_battl=mysql_insert_id();

// апдейтим ботов
mysql_query("UPDATE `users_clons` SET `battle` = {$id_battl} WHERE `id` in (".$bot_team_sql.") ");


// создаем лог
				$rr = "<b>".$tima1_data."</b> и <b>".$bots_names_chat."</b>";
				addch ("<a href=logs.php?log=".$id_battl." target=_blank>Бой</a> между <B><b>".$tima1_data."</b> и <b>".$bots_names_chat."</b> начался.  ",$row[type],$bot_city);
				addlog($id_battl,"Часы показывали <span class=date>".date("Y.m.d H.i")."</span>, когда ".$rr." бросили вызов друг другу. <BR>");
				//обновляем чарам бой 
				mysql_query("UPDATE users SET `battle` ={$id_battl},`zayavka`=0, `battle_t`=1 WHERE  id='{$AGrow[owner1]}' or id='{$AGrow[owner2]}' or id='{$AGrow[owner3]}'   ;");

//апдейтим группу ставим ей номербоя и волну 1 и счетчик клонов
				mysql_query("UPDATE tur_grup SET `battle` ={$id_battl},`volna`=1 , `c`={$c}  WHERE `id`= '".$AGrow[id]."';");

//
				mysql_query("UPDATE `tur_logs` SET  gotime='0', `logs`= CONCAT(`logs`,'<span class=date>".date("d.m.y H:i")."</span> ".$tima1_data." начал бой против монстров <a href=logs.php?log=".$id_battl." target=_blank>»»</a><BR>') WHERE  `type`='{$row[type]}'  and active=1;");

								}
								else
								{

								//для тех кто в бою поверяем волну еслинадо добавляем монстров
								$count=mysql_fetch_array(mysql_query("select sum(hp) from users_clons where battle='{$AGrow[battle]}' ;"));
								if ($count[0]<=6000)
									{
									//пора вводить новых ботов
							$c=$AGrow[c];
							//echo "goto in batt...";
							include "/www/".$CITY_NAME.".oldbk.com/config240.php";
							//готовим ботов
							$bots_names='';
							$bots_names_chat='';
							$bot_team_sql='';
							$mobot=$row[type];
							$voln=$AGrow[volna]+1;
							$moboa=$monstro[$mobot][$voln];
			foreach ($moboa as $k=>$v)
			{

			for ($l=0;$l<$v;$l++)
				{
				$c++;
				//$BOT=mysql_fetch_array(mysql_query("SELECT * from `users` where `id`='".$k."' ;"));
				$BOT=mysql_query_cache("SELECT * from `users` where `id`='".$k."' ;",false,24*3600);
				$BOT = $BOT[0];					
				$BOT['login'].=" (kлoн ".$c.")";
				$BNAME=BNewHist($BOT);
				$BNAME_chat=nick_hist($BOT);
				$BOT_items=load_mass_items_by_id_c($BOT);
				mysql_query("INSERT INTO `users_clons` SET `login`='".$BOT['login']."',`sex`='{$BOT['sex']}',
					`level`='{$BOT['level']}',`align`='{$BOT['align']}',`klan`='{$BOT['klan']}',`sila`='{$BOT['sila']}',
					`lovk`='{$BOT['lovk']}',`inta`='{$BOT['inta']}',`vinos`='{$BOT['vinos']}',
					`intel`='{$BOT['intel']}',`mudra`='{$BOT['mudra']}',`duh`='{$BOT['duh']}',`bojes`='{$BOT['bojes']}',`noj`='{$BOT['noj']}',
					`mec`='{$BOT['mec']}',`topor`='{$BOT['topor']}',`dubina`='{$BOT['dubina']}',`maxhp`='{$BOT['maxhp']}',`hp`='{$BOT['maxhp']}',
					`maxmana`='{$BOT['maxmana']}',`mana`='{$BOT['mana']}',`sergi`='{$BOT['sergi']}',`kulon`='{$BOT['kulon']}',`perchi`='{$BOT['perchi']}',
					`weap`='{$BOT['weap']}',`bron`='{$BOT['bron']}',`r1`='{$BOT['r1']}',`r2`='{$BOT['r2']}',`r3`='{$BOT['r3']}',`helm`='{$BOT['helm']}',
					`shit`='{$BOT['shit']}',`boots`='{$BOT['boots']}',`nakidka`='{$BOT['nakidka']}',`rubashka`='{$BOT['rubashka']}',`shadow`='{$BOT['shadow']}',`battle`={$AGrow[battle]},`bot`=1,
					`id_user`='{$BOT['id']}',`at_cost`='{$BOT_items['allsumm']}',`kulak1`=0,`sum_minu`='{$BOT_items['min_u']}',
					`sum_maxu`='{$BOT_items['max_u']}',`sum_mfkrit`='{$BOT_items['krit_mf']}',`sum_mfakrit`='{$BOT_items['akrit_mf']}',
					`sum_mfuvorot`='{$BOT_items['uvor_mf']}',`sum_mfauvorot`='{$BOT_items['auvor_mf']}',`sum_bron1`='{$BOT_items['bron1']}',
					`sum_bron2`='{$BOT_items['bron2']}',`sum_bron3`='{$BOT_items['bron3']}',`sum_bron4`='{$BOT_items['bron4']}',`ups`='{$BOT_items['ups']}',
					`injury_possible`=0, `battle_t`=2;");
				$id_bot[$c]=mysql_insert_id();

				if ($bot_team!='') {
							$bots_names.=$BNAME;
							$bots_names_chat.=", ".$BNAME_chat;
							$bot_team.=";".$id_bot[$c];
							$bot_team_sql.=",".$id_bot[$c];
							}
							else
							{
							$bots_names=$BNAME;
							$bots_names_chat=$BNAME_chat;							
							$bot_team=$id_bot[$c];
							$bot_team_sql=$id_bot[$c];
							}
				}
				///работает!!
			}

		//Апдейтим бой
		$time = time();
		mysql_query('UPDATE `battle` SET to1='.$time.', to2='.$time.', `t2`=CONCAT(`t2`,\';'.$bot_team.'\'), `t2hist`=CONCAT(`t2hist`,\''.$bots_names.'\')  WHERE `id` = '.$AGrow[battle].' ;');

		//Апдейтим группу
		mysql_query("UPDATE tur_grup SET `volna`={$voln} , `c`={$c}  WHERE `id`= '".$AGrow[id]."';");

		//пишем  влог
		addlog($AGrow[battle],'<span class=date>'.date("H:i").'</span> '.$bots_names_chat.' вмешались в поединок!<BR>');

								$tima1=$AGrow[owner1].";";
								$tima1_data="Отряд: <b>«".$AGrow[nazva]."»</b> (".$AGrow[owner_data1].",";
								if ($AGrow[owner2]>0) { $tima1.=$AGrow[owner2].";"; $tima1_data.=$AGrow[owner_data2].","; }
								if ($AGrow[owner3]>0) { $tima1.=$AGrow[owner3].";"; $tima1_data.=$AGrow[owner_data3].","; }
							        $tima1 = substr($tima1, 0, -1); $tima1_data = substr($tima1_data, 0, -1); $tima1_data.=")";

		mysql_query("UPDATE `tur_logs` SET  gotime='0', `logs`= CONCAT(`logs`,'<span class=date>".date("d.m.y H:i")."</span> ".$tima1_data." встретил <b>".$voln."-ю</b> волну монстров <a href=logs.php?log=".$AGrow[battle]." target=_blank>»»</a><BR>') WHERE  `type`='{$row[type]}'  and active=1;");










									}


								}
							}
							else
							{
							echo "no active...<br>";
							/// для тех кто погиб - проверяем не пора ли им выбывать
							$GMAXDEM=get_max_demag($AGrow[type]);
							if ($AGrow[demag]< $GMAXDEM[demag])
								{
								//echo "DEM too small<br>";
								// группа набила меньше чем максимальное
								// выгоняем ее
								$tima1_data="Отряд: <b>«".$AGrow[nazva]."»</b> (".$AGrow[owner_data1].",";
								if ($AGrow[owner2]>0) { $tima1.=$AGrow[owner2].";"; $tima1_data.=$AGrow[owner_data2].","; }
								if ($AGrow[owner3]>0) { $tima1.=$AGrow[owner3].";"; $tima1_data.=$AGrow[owner_data3].","; }
							        $tima1_data = substr($tima1_data, 0, -1); $tima1_data.=")";

								//выбрасываем тех кто проиграл и пишем лог
								mysql_query("UPDATE `tur_logs` SET `logs`= CONCAT(`logs`,'<span class=date>".date("d.m.y H:i")."</span> <b>".$tima1_data." выбывают из турнира - нанесено урона (".$AGrow[demag]."HP ) </b><BR>') WHERE  `type`='{$AGrow[type]}' and active=1;");
								//удаляем проигравшую сторону
								//по ид боя где овнер1 не равен победившей стороне
								mysql_query("DELETE from tur_grup where id='{$AGrow[id]}' ; ");
								//выводим проигравшую сторону
								mysql_query("UPDATE `users`  SET `users`.`id_grup` = '0', `users`.`room` = '240'  WHERE  `users`.`id_grup` = '{$AGrow[id]}' AND `users`.`room`>240 AND `users`.`room`<269 ;");
								
								// удалим старый эфект если он был
								mysql_query("DELETE FROM `effects` where type=8240 and (owner='{$AGrow[owner1]}' or owner='{$AGrow[owner2]}' or owner='{$AGrow[owner1]}') ");
								
								//добавляем эфект 8240
								mysql_query("INSERT INTO `effects` SET `type`=8240,`name`='Следующее посещение Групповые сражения',`time`=".(time()+19800).",`owner`='{$AGrow[owner1]}';");
								mysql_query("INSERT INTO `effects` SET `type`=8240,`name`='Следующее посещение Групповые сражения',`time`=".(time()+19800).",`owner`='{$AGrow[owner2]}';");
								mysql_query("INSERT INTO `effects` SET `type`=8240,`name`='Следующее посещение Групповые сражения',`time`=".(time()+19800).",`owner`='{$AGrow[owner3]}';");																
								}
								else
								{
								//echo "DEM MAX<br>";
								// у этой группы максимальные данные
								 $get_winner=mysql_fetch_array(mysql_query("select count(*) from tur_grup where type='{$AGrow[type]}'"));
								 	if ($get_winner[0]==1)
								 	{
									// проверяем общее кол. групп
									  // если группа одна
									  // то она победила
									$tima1_data="Отряд: <b>«".$AGrow[nazva]."»</b> (".$AGrow[owner_data1].",";
									if ($AGrow[owner2]>0) { $tima1.=$AGrow[owner2].";"; $tima1_data.=$AGrow[owner_data2].","; }
									if ($AGrow[owner3]>0) { $tima1.=$AGrow[owner3].";"; $tima1_data.=$AGrow[owner_data3].","; }
								        $tima1_data = substr($tima1_data, 0, -1); $tima1_data.=")";
									// награждаем - выпускаем - пишем
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

					$nagrada_rep[246]=1200; $nagrada_check[246]=3207; $nagrada_exp[246]=6000;
					$nagrada_rep[247]=1400; $nagrada_check[247]=3207; $nagrada_exp[247]=7000;
					$nagrada_rep[248]=1600; $nagrada_check[248]=3204; $nagrada_exp[248]=8000;
					$nagrada_rep[249]=1800;	$nagrada_check[249]=3206; $nagrada_exp[249]=9000;
					$nagrada_rep[250]=2000;	$nagrada_check[250]=3205; $nagrada_exp[250]=10000;
					$nagrada_rep[251]=2200;	$nagrada_check[251]=3205; $nagrada_exp[251]=12000;					

					$Blaha=$AGrow[type];
					$repa=$nagrada_rep[$Blaha];
					$expa=$nagrada_exp[$Blaha];
					$check=$nagrada_check[$Blaha];

					if (($repa > 0)and ($expa >0 ))
					{
					$item_ch[3204]=50;
					$item_ch[3205]=100;
					$item_ch[3206]=80;
					$item_ch[3207]=30;
					//запрашиваем всех 3-х победителей

										$winners=mysql_query("select * from users where (id='{$AGrow[owner1]}' or id='{$AGrow[owner2]}' or id='{$AGrow[owner3]}')   ; ");
										$ku=mysql_affected_rows();
									 	if (($ku>0)and($ku<4))
									 	{
											while($winrow=mysql_fetch_array($winners))
											{
											if ($winrow[id_city]==0) { $bci='oldbk.';  }
											if ($winrow[id_city]==1) { $bci='avalon.';  }											
											
											$realwin=mysql_fetch_array(mysql_query("select * from ".$bci."users where id='{$winrow[id]}' ; "));
											
												if ($realwin[prem]>0)
												{
													$addrepa=$repa+$repa*0.1;
												}
												else
												{
													$addrepa=$repa;
												}
												
										        mysql_query("UPDATE ".$bci."`users` SET `exp`=`exp`+".$expa." ,`rep`=`rep`+'".$addrepa."', `repmoney` = `repmoney` + '".$addrepa."' WHERE `id`='".$winrow[id]."' LIMIT 1; ");
                                                						$rec['owner']=$realwin[id];
												$rec['owner_login']=$realwin[login];
												$rec['owner_balans_do']=$realwin[money];
												//$user['money'] += $q_data['kr'];
												$rec['owner_balans_posle']=$realwin[money];
												$rec['owner_rep_do']= $realwin['repmoney'];
												$winrow['repmoney']+=$addrepa;
												$rec['owner_rep_posle']=$realwin['repmoney'];
												$rec['target']=0;
												$rec['target_login']="Победа в турнире";
												$rec['type']=183;//репа за квест
												$rec['sum_kr']=0;
												$rec['sum_ekr']=0;
												$rec['sum_rep']=$addrepa;
												$rec['sum_kom']=0;
												add_to_new_delo($rec);
								        		addchp ('<font color=red>Поздравляем!</font> Вы получили <b>'.$addrepa.'</b> репутации и  <b>'.$expa.'</b> опыта за победу в турнире!','{[]}'.$realwin['login'].'{[]}',$realwin['room'],$realwin['id_city']);
			
											mysql_query("INSERT INTO `effects` SET `type`=8240,`name`='Следующее посещение Групповые сражения',`time`=".(time()+19800).",`owner`='{$realwin[id]}';");
			
								        		mysql_query("INSERT INTO `inventory`
								        		(`name`,`duration`,`maxdur`,`cost`,`owner`,`nlevel`,`nsila`,`nlovk`,`ninta`,`nvinos`,`nintel`,`nmudra`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nalign`,`minu`,`maxu`,`gsila`,
								        		`glovk`,`ginta`,`gintel`,`ghp`,`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`img`,`text`,`dressed`,
								        		`bron1`,`bron2`,`bron3`,`bron4`,`dategoden`,`magic`,`type`,`present`,`sharped`,`massa`,`goden`,`needident`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`letter`,`isrep`,`update`,`setsale`,`prototype`,`otdel`,`bs`,`gmp`,`includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,
								        		`includemagicuses`,`includemagiccost`,`includemagicekrcost`,`gmeshok`,`tradesale`,`karman`,`stbonus`,`upfree`,`ups`,`mfbonus`,`mffree`,`type3_updated`,`bs_owner`,`nsex`,`present_text`,`add_time`,`labonly`,`labflag`,`prokat_idp`,`prokat_do`,`arsenal_klan`,`repcost`,`up_level`,`ecost`,`group`,`ekr_up`,`unik`,`add_pick`,`pick_time`,`sowner`)
								        		VALUES
								        		('Чек на предъявителя ".$item_ch[$check]."кр',0,1,'{$item_ch[$check]}','{$winrow[id]}',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'lab2_".$item_ch[$check]."kr.gif',
								        		'',0,0,0,0,0,0,0,50,'Ристалище',0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'',0,'',0,'{$check}','52',0,0,0,0,0,'',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL,0,0,0,0,NULL,'',0,0,0,1,NULL,0,NULL,NULL,0);");


                                                $dress[id]=mysql_insert_id();
                                                $dress[idcity]=$winrow[id_city];
                                                $dress['type']=52;
                                                $dress['name']='Чек на предъявителя '.$item_ch[$check].'кр';
                                                $dressid=get_item_fid($dress);
												$rec['owner']=$realwin[id];
												$rec['owner_login']=$realwin[login];
												$rec['owner_balans_do']=$realwin[money];
												//$user['money'] -= $_POST['count']*$dress['cost'];
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

                                                if(olddelo==1)
                                                {
                                                	$itm1_id="Чек на предъявителя ".$item_ch[$check]."кр ".$cnis."".$dress[id]."(x1)";
                                                }


								        		mysql_query("INSERT INTO `inventory` (`name`,`duration`,`maxdur`,`cost`,`owner`,`nlevel`,`nsila`,`nlovk`,`ninta`,`nvinos`,`nintel`,`nmudra`,`nnoj`,`ntopor`,`ndubina`,`nmech`,
								        		`nalign`,`minu`,`maxu`,`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`img`,`text`,`dressed`,`bron1`,
								        		`bron2`,`bron3`,`bron4`,`dategoden`,`magic`,`type`,`present`,`sharped`,`massa`,`goden`,`needident`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,`gfire`,`gwater`,
								        		`gair`,`gearth`,`glight`,`ggray`,`gdark`,`letter`,`isrep`,`update`,`setsale`,`prototype`,`otdel`,`bs`,`gmp`,`includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,
								        		`includemagicuses`,`includemagiccost`,`includemagicekrcost`,`gmeshok`,`tradesale`,`karman`,`stbonus`,`upfree`,`ups`,`mfbonus`,`mffree`,`type3_updated`,`bs_owner`,`nsex`,
								        		`present_text`,`add_time`,`labonly`,`labflag`,`prokat_idp`,`prokat_do`,`arsenal_klan`,`repcost`,`up_level`,`ecost`,`group`,`ekr_up`,`unik`,`add_pick`,`pick_time`,`sowner`)
								        		VALUES ('Сердце Рыцаря',0,1,1,'{$winrow[id]}',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'heart_of_hero.gif','',0,0,0,0,0,0,0,200,'',0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
								        		'',0,'',0,1011001,'72',0,0,0,0,0,'',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL,0,0,0,0,NULL,'',0,0,0,1,NULL,0,NULL,NULL,0);");

                                                $dress[id]=mysql_insert_id();
                                                $dress[idcity]=$winrow[id_city];
                                                $dress['type']=200;
                                                $dress['name']='Сердце Рыцаря';
                                                $dressid=get_item_fid($dress);
												$rec['owner']=$realwin[id];
												$rec['owner_login']=$realwin[login];
												$rec['owner_balans_do']=$realwin[money];
												//$user['money'] -= $_POST['count']*$dress['cost'];
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

                                                if(olddelo==1)
												{
													$itm2_id="Сердце Рыцаря".$cnis."".$dress[id]."(x1)";

													mysql_query("INSERT INTO oldbk.`delo` (`id` , `author` ,`pers`, `text`, `type`, `date`)
													VALUES ('','0','{$winrow[id]}','\"".$winrow['login']."\" выиграл в групповых сражениях: ".$itm1_id.", ".$itm2_id."',1,'".time()."');");
                                                }
												addchp ('<font color=red>Поздравляем!</font> Вы получили <b>\"Чек на предъявителя '.$item_ch[$check].'кр\"</b> и Сувенир:<b>\"Сердце Рыцаря\"</b> !','{[]}'.$winrow['login'].'{[]}',$winrow['room'],$winrow['id_city']);
												//addchp ('<font color=red>Внимание!</font> Награда получена награды команды'.$AGrow[id],'{[]}Bred{[]}');
											}
				 						}
										  else
										  {
										  addchp ('<font color=red>Внимание!</font> SQL Ошибка выдачи награды команды'.$AGrow[id],'{[]}Bred{[]}');
										  }
									        }else
									        {
									        addchp ('<font color=red>Внимание!</font> Ошибка выдачи награды команды'.$AGrow[id],'{[]}Bred{[]}');
									        }
///////////////////////////////////////////////////////////////////////////////////////////////




 									mysql_query("UPDATE `tur_logs` SET winer='{$tima1_data}', active=0 , end_time='".time()."' , `logs`= CONCAT(`logs`,'<span class=date>".date("d.m.y H:i")."</span> <b>".$tima1_data." Победил в турнире! - Нанесено урона (".$AGrow[demag]."HP ) </b><BR>') WHERE  `type`='{$AGrow[type]}' and active=1;");
									//по ид боя где овнер1 не равен победившей стороне
									mysql_query("DELETE from tur_grup where id='{$AGrow[id]}' ; ");
									//выводим  сторону
									mysql_query("UPDATE `users`  SET `users`.`id_grup` = '0', `users`.`room` = '240'  WHERE  `users`.`id_grup` = '{$AGrow[id]}' AND `users`.`room`>240 AND `users`.`room`<269 ;");
								 	}

								}
							}
						}


					}
				///////////////////

				}
			if ($rf1==$finc)
				{
				// надо закрыть и поставить новое время
				//ставим следующее время турнира
				//echo "закрыли по счету";
				get_close_and_next(240);

				}
			}
			else
			{
			//активных нету турниров
			// надо закрыть турнир сам и поставить новое время
			//echo "Нет активных...закрываем турнир";
			get_close_and_next(240);
			}
	}

///
$get_star=mysql_fetch_array(mysql_query("select * from variables where var='starevshik' ; "));
print_r($get_star);
if ( ($get_star[value]>0) AND ($get_star[value]<=time()))
	{
	$TEXT1="<font color=red>[Старьевщик] Одумайтесь люди! что вы творите!...";
	//addchp($TEXT1,'Старьевщик',12);
	addch2all($TEXT1,$bot_city);
	$TEXT2="<font color=red>[Старьевщик] Род человеческий, своей алчностью и жаждой наживы вы прогневали духов подземелья!...";
	addch2all($TEXT2,$bot_city);
	//addchp($TEXT2,'Старьевщик',12);
	$TEXT3="<font color=red>[Старьевщик] Грядет Кара богов за ваши деяния!!!";
	addch2all($TEXT3,$bot_city);
	//addchp($TEXT3,'Старьевщик',12);
	$TEXT4="<font color=red>[Старьевщик] Недра города содрогаются от лязга оружия и воздух пропитан злобой...";
	//addchp($TEXT4,'Старьевщик',12);
	addch2all($TEXT4,$bot_city);
	$TEXT5="<font color=red>[Старьевщик] Берите жен и детей и бегите прочь из города! Времени осталось совсем мало...";
	addch2all($TEXT5,$bot_city);
	//addchp($TEXT5,'Старьевщик',12);
	mysql_query("UPDATE variables set value=".(time()+3600)." where var='starevshik' ; ");
	}

//удаление мертвых ботов из ристалки
mysql_query("delete from users_clons where hp=0 and battle in (select id from battle where win=3 and type in (247,248,249,250,251))");


//чистка заявок физов и новичков
$get_zayav=mysql_query("select * from zayavka where `start` < UNIX_TIMESTAMP()-21600 and `level` in (1,2)");
while($zd=mysql_fetch_array($get_zayav))
{
	mysql_query("delete from zayavka where id='{$zd[id]}' ");
	if (mysql_affected_rows()>0)
	{
	mysql_query("UPDATE users SET zayavka=0, battle_t=0 WHERE   zayavka='{$zd[id]}' and battle=0; ");
	}
}

lockDestroy("cron_timeout_job");
?>