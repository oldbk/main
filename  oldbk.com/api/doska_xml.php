<?php
//memcach
$rooms = array ("Секретная Комната","Комната для новичков","Комната для новичков 2","Комната для новичков 3","Комната для новичков 4","Зал Воинов 1","Зал Воинов 2","Зал Воинов 3","Торговый зал",
	"Рыцарский зал","Башня рыцарей-магов","Колдовской мир","Этажи духов","Астральные этажи","Огненный мир","Зал Паладинов","Совет Белого Братства","Зал Тьмы","Царство Тьмы","Будуар",
	"Центральная площадь","Страшилкина улица","Магазин","Ремонтная мастерская","Новогодняя елка","Комиссионный магазин","Парковая улица","Почта","Регистратура кланов","Банк","Суд",
	"Башня смерти","Готический замок","Лабиринт хаоса","Цветочный магазин","Магазин 'Березка'","Зал Стихий","Готический замок - приемная","Готический замок - арсенал","Готический замок - внутренний двор",
	"Готический замок - мастерские","Готический замок - комнаты отдыха","Лотерея Сталкеров","Комната Знахаря","Комната №44","Вход в Лабиринт Хаоса","Прокатная лавка","Арендная лавка","Храмовая лавка","Храм Древних","Замковая площадь",
	"Большая скамейка","Средняя скамейка","Маленькая скамейка","Зал Света","Царство Света","Царство Стихий","Зал клановых войн","Комната №58","Комната №59","Арена Богов","Комната №61","Комната №62","Комната №63","Комната №64","Комната №65","66"=>'Торговая улица',
"200"=> "Ристалище","401"=> "Врата Ада");

header ("content-type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"windows-1251\"?>\n";


include "/www/oldbk.com/connect.php";
require_once('/www/oldbk.com/memcache.php');

function CheckOpDay() {
	$q = mysql_query('SELECT * FROM oldbk.variables WHERE var = "opposition_today"');
	if (mysql_num_rows($q) > 0) {
		$v = mysql_fetch_assoc($q);
		if ($v !== FALSE) {
			if (date("d/m/Y",time()+(24*3600*4)) == $v['value']) {
				if (date("H") >= 6) {
					return true;
				}
			}
		}
	}
	return false;
}



function prettyTime($start_timestamp = null, $end_timestamp = null)
{
    $start_datetime = new DateTime();
    if($start_timestamp !== null) {
        $start_datetime->setTimestamp($start_timestamp);
    }

    $end_datetime = new DateTime();
    if($end_timestamp !== null) {
        $end_datetime->setTimestamp($end_timestamp);
    }

    if(($end_datetime->getTimestamp() - $start_datetime->getTimestamp()) <= 60) {
       return 'менее минуты';
    }

    $interval = $end_datetime->diff($start_datetime);

    $time_type = array(
        'm' => '%m мес.',
        'd' => '%d дн.',
        'h' => '%h ч.',
        'i' => '%i мин.',
    );
    $format_arr = array();
    foreach($time_type as $property => $format) {
        if($interval->{$property} != 0) {
            $format_arr[] = $format;
        }
    }

    if(empty($format_arr)) {
        return null;
    }

    return $interval->format(implode(' ', $format_arr));
}

echo "<message refresh=\"".time()."\">";
 
  
   echo '<event name="Лотерея ОлдБк" ';

  // $get_lot=mysql_fetch_array(mysql_query("select * from oldbk.item_loto_ras where status=1 LIMIT 1;"));
	$get_lot=mysql_query_cache("select * from oldbk.item_loto_ras where status=1 LIMIT 1;",false,3600);
	$get_lot=$get_lot[0];

   if ($get_lot[id] >0)
   {
   echo "description=\"Следующий тираж № $get_lot[id] состоится ".date("d-m-Y H:i",$get_lot[lotodate])."\">";
   }
   else
   {
   echo "description=\"Нет данных\"> ";
   }
   echo "</event>\n";

   $data=mysql_query_cache('select * from place_zay order by start limit 5',false,300);
 
	while(list($k,$row) = each($data)) 
   {
      echo "<event name=\"Ближайшие бои на Арене Богов СВЕТ VS TЬМА - CapitalCity\" ";
      echo "description=\"Уровень ".($row[t1min]==$row[t1max]?$row[t1min]:$row[t1min].'-'.$row[t1max]).": ".date('d-m-Y', $row[start])." в ".date('H:i:s', $row[start])."  (".$row[coment].")\">";
      echo "</event>\n";
   }

/*   
$data=mysql_query_cache('select * from avalon.place_zay order by start limit 5',false,300);
   
	while(list($k,$row) = each($data)) 
   {
      echo "<event name=\"Ближайшие бои на Арене Богов СВЕТ VS TЬМА - AvalonCity\" ";
      echo "description=\"Уровень ".($row[t1min]==$row[t1max]?$row[t1min]:$row[t1min].'-'.$row[t1max]).": ".date('d-m-Y', $row[start])." в ".date('H:i:s', $row[start])."  (".$row[coment].")\">";
      echo "</event>\n";
   }
*/

/*      
   //одиночные турниры
   $nt=mysql_query_cache("select * from tur_raspis where tur_type=210 LIMIT 1;",false,120);
   $nt= $nt[0];

        echo "<event name=\"Одиночные сражения (6-10 уровни на Ристалище) - CapitalCity\" ";
	if ($nt[status]==0)
		{
		echo "description=\"".date('d-m-Y', $nt[start_time])." в ".date('H:i:s', $nt[start_time])."\">";
		}
		else
		if ($nt[status]==1)
		{
		echo "description=\"Турнир открыт набор!\">";
		}
		else
		if ($nt[status]==2)
		{
		echo "description=\"Турнир уже идет!\">";
		}

      	echo "</event>\n";
	
   /////////////////////////////////////////////////////
 //одиночные турниры
 $nt=mysql_query_cache("select * from avalon.tur_raspis where tur_type=210 LIMIT 1;",false,120);
   $nt= $nt[0];
   
	
        echo "<event name=\"Одиночные сражения (6-10 уровни на Ристалище) - AvalonCity\" ";
	if ($nt[status]==0)
		{
		echo "description=\"".date('d-m-Y', $nt[start_time])." в ".date('H:i:s', $nt[start_time])."\">";
		}
		else
		if ($nt[status]==1)
		{
		echo "description=\"Турнир открыт набор!\">";
		}
		else
		if ($nt[status]==2)
		{
		echo "description=\"Турнир уже идет!\">";
		}

      	echo "</event>\n";
	
   /////////////////////////////////////////////////////



   //отряды турниры
    $nt=mysql_query_cache("select * from tur_raspis where tur_type=270 LIMIT 1;",false,120);
    $nt= $nt[0];

	    echo "<event name=\"Сражение отрядов (6-10 уровни на Ристалище) - CapitalCity\" ";
	if ($nt[status]==0)
		{
		echo "description=\"".date('d-m-Y', $nt[start_time])." в ".date('H:i:s', $nt[start_time])."\">";
		}
		else
		if ($nt[status]==1)
		{
		echo "description=\"Турнир открыт набор!\">";
		}
		else
		if ($nt[status]==2)
		{
		echo "description=\"Турнир уже идет!\">";
		}

      	echo "</event>\n";
	
   /////////////////////////////////////////////////////
   
 //отряды турниры
 $nt=mysql_query_cache("select * from avalon.tur_raspis where tur_type=270 LIMIT 1;",false,120);
 $nt= $nt[0];
   
	    echo "<event name=\"Сражение отрядов (6-10 уровни на Ристалище) - AvalonCity\" ";
	if ($nt[status]==0)
		{
		echo "description=\"".date('d-m-Y', $nt[start_time])." в ".date('H:i:s', $nt[start_time])."\">";
		}
		else
		if ($nt[status]==1)
		{
		echo "description=\"Турнир открыт набор!\">";
		}
		else
		if ($nt[status]==2)
		{
		echo "description=\"Турнир уже идет!\">";
		}

      	echo "</event>\n";

   /////////////////////////////////////////////////////   


 //группы турниры
//  $nt=mysql_query_cache("select * from tur_raspis where tur_type=240 LIMIT 1;",false,120);
//  $nt= $nt[0];
$nt[status]=1;
$nt[start_time]=time();
 
	  echo "<event name=\"Групповые сражения (7-10 уровни на Ристалище) - CapitalCity\" ";
	if ($nt[status]==0)
		{
		echo "description=\"".date('d-m-Y', $nt[start_time])." в ".date('H:i:s', $nt[start_time])."\">";
		}
		else
		if ($nt[status]==1)
		{
		echo "description=\"Турнир открыт набор!\">";
		}
		else
		if ($nt[status]==2)
		{
		echo "description=\"Турнир уже идет!\">";
		}

	   echo "</event>\n";

   /////////////////////////////////////////////////////

 //группы турниры
//    $nt=mysql_query_cache("select * from avalon.tur_raspis where tur_type=240 LIMIT 1;",false,120);
//   $nt= $nt[0];
$nt[status]=1;
$nt[start_time]=time();
	  echo "<event name=\"Групповые сражения (7-10 уровни на Ристалище) - Avalon.City\" ";
	if ($nt[status]==0)
		{
		echo "description=\"".date('d-m-Y', $nt[start_time])." в ".date('H:i:s', $nt[start_time])."\">";
		}
		else
		if ($nt[status]==1)
		{
		echo "description=\"Турнир открыт набор!\">";
		}
		else
		if ($nt[status]==2)
		{
		echo "description=\"Турнир уже идет!\">";
		}

	   echo "</event>\n";
   /////////////////////////////////////////////////////
*/

	$bs=mysql_query_cache('SELECT * FROM dt_var WHERE var = "nextdt"',false,120);
	while(list($k,$row) = each($bs)) 
	{
		$nextdt=$row;
	}

	$tbs=mysql_query_cache("SELECT * FROM dt_var WHERE var='nextdttype'",false,120);
	while(list($tk,$trow) = each($tbs)) 
	{
		$nextdttype=$trow;
	}
   
	$min_bet = 5;
	$min_up = 3;

	if($nextdttype['valint'] > 0) {
		$min_bet = 10;
		$min_up = 5;
	}
   
   $txt=(($nextdt['valint']<time())?'Турнир уже начался':date("d.m.Y H:i",$nextdt['valint']));
   echo "<event name=\"Начало ближайшей Башни Смерти ".$min_up."-".$min_bet." ур. ".($nextdttype['valint']>0?'(артовая)':'')." - CapitalCity\" ";
   echo "description=\"".$txt."\">";
   echo "</event>\n";      

//////////////////

   /////////////////////////////////////////////////////

/*
	$bs=mysql_query_cache("select * from avalon.`variables` where `var` in ('startbs','bs_type','bs_level')",false,120);
	while(list($k,$row) = each($bs)) 
	{
		if ($row['var']=='startbs') {$bs[value]=$row[value] ; } 
		elseif ($row['var']=='bs_type') {$bs_type[value]=$row[value] ; } 
		elseif ($row['var']=='bs_level') {$bs_level[value]=$row[value] ; } 		
	}

   
   //$cur_bs=mysql_fetch_array(mysql_query('select * from `deztow_turnir` where active=true LIMIT 1'));
   
   $txt=(($bs[value]<time())?'Турнир уже начался':date("d-m-Y в H:i", $bs[value]));
   echo "<event name=\"Начало ближайшей Башни Смерти ".$bs_level[value]."-ур. ".($bs_type[value]==2?'(артовая)':'')." - AvalonCity\" ";
   echo "description=\"".$txt."\">";
   echo "</event>\n";      
*/

   /////////////////////////////////////////////////////
	$op = CheckOpDay();

		if ($op == true) 
				{
				// противостояние
				   echo "<event name=\"День противостояния\" ";
				   echo "description=\"Сегодня\">";
				   echo "</event>\n";      
				} else 
				{
				// пишем когда начнётся наш день
				
				$opd=mysql_query_cache('SELECT * FROM oldbk.variables WHERE var = "opposition_today"',false,120);
				while(list($ok,$orow) = each($opd)) 
				{
				$v=$orow;
				}
				
				$st=str_replace('/', '-', $v['value'])." 06:00";
				$next_op_time = strtotime($st);
					if (date("d/m/Y",time()+(24*3600*4)) == $v['value']) {
						$txt= "через ".prettyTime(null,mktime(6,0,0));	
					} else {
						$txt= "через ".prettyTime(null,$next_op_time);	
					}
				   echo "<event name=\"День противостояния\" ";
				   echo "description=\"".$txt."\">";
				   echo "</event>\n"; 
				}
	
//////////////////

//////////////////





		$t=mysql_query_cache("select * from variables where var='ghost_all_time' ; ",false,60);
		$t=$t[0];

		$freedomt=$t[value];
		echo "<event name=\"Исчадие Хаоса\" ";

		if ($freedomt-time() > 0)
			{
				$Xonline=false;			   
			}
			else
			{
			 $get_bot_next=mysql_query_cache("select id, login, bot_room  from users_clons where id_user=(select value from variables where var='ghost_next_id')",false,300);
       			    $get_bot_next=$get_bot_next[0];
				if ($get_bot_next[login]!='')
				{
				$Xonline=true;
				}
				else
				{
				$Xonline=false;
				}
			
			}
			
		if ($Xonline==false)
		   {
   		    $get_bot_next=mysql_query_cache("select id, login  from users where id=(select value from variables where var='ghost_next_id')",false,300);
   		    $get_bot_next=$get_bot_next[0];
   		    
		     if   ($freedomt-time() > 0)
		     {
		     echo "description=\"".$get_bot_next[login]." - вырвусь на свободу через: ".prettyTime(null,$freedomt)."\" ";
		     }
		     else
		     {
			echo "description=\"".$get_bot_next[login]." - уже в пути.\" ";		     
		     }
		   
		   
		    echo " bot_id=\"$get_bot_next[id]\" >";
		    }
		    else
		    {
		       echo "description=\"".$get_bot_next[login]."- Онлайн\"";
		       echo " bot_id=\"$get_bot_next[id]\" bot_room=\"".$rooms[$get_bot_next['bot_room']]."\" >";
		    }
		    
		echo "</event>\n";       
		

//////////////////

/*
		$t=mysql_query_cache("select * from avalon.variables where var='ghost_all_time' ; ",false,60);
		$t=$t[0];
		
		$freedomt=$t[value];
		echo "<event name=\"Дух Мерлина\" ";
		
		if ($freedomt-time() > 0)
				{
				$Donline=false;		
				}
				else
				{
				    $get_bot_next=mysql_query_cache("select id, login, bot_room  from avalon.users_clons where id_user=(select value from avalon.variables where var='ghost_next_id')",false,300);
		       		    $get_bot_next=$get_bot_next[0];
       		    
		       		    if ($get_bot_next[login]!='')
					{
					$Donline=true;
					}
					else
					{
					$Donline=false;
					}

				}
		
		if ($Donline==false)
		   {
   		    $get_bot_next=mysql_query_cache("select id, login  from avalon.users where id=(select value from avalon.variables where var='ghost_next_id')",false,300);
   		    $get_bot_next=$get_bot_next[0];
   		    
   		 if   ($freedomt-time() > 0)
   		    	{
			    echo "description=\"".$get_bot_next[login]." - вырвусь на свободу через:".floor(($freedomt-time())/60/60)." ч. ".round((($freedomt-time())/60)-(floor(($freedomt-time())/3600)*60))." мин.\" ";
			  }
			  else
			  {
			    echo "description=\"".$get_bot_next[login]." - уже в пути.\"";
			  }
		    echo " bot_id=\"$get_bot_next[id]\" >";
		    }
		    else
		    {
		       echo "description=\"".$get_bot_next[login]."- Онлайн\"";
		       echo " bot_id=\"$get_bot_next[id]\" bot_room=\"".$rooms[$get_bot_next['bot_room']]."\" >";
		    }
		echo "</event>\n";       
*/
//////////////////Драконы 

		$data=mysql_query_cache("select * from variables where var like 'bots_start_time_level_%'",false,60);
		$bots=array();
		while(list($k,$row) = each($data)) 
		   {
		   $in=explode("_",$row['var']);
		   $bots[$in[4]]=$row['value'];
		   }
		
		ksort($bots); 
		
		foreach($bots as $lvl=>$dat)  
			{
		      
				echo "<event name=\"Атака на Драконов ".$lvl."-й уровень\"  level=\"".$lvl."\" outtime=\"".$dat."\"  outdate=\"".date("Y.m.d H:i:s",$dat)."\" ";
				if ($dat <= time())
				   {
				       echo "description=\"Атакуют город\"";
				       echo ">";
				    }
				    else
				    {
				    echo "description=\"через: ".prettyTime(null,$dat)."\" ";
				    echo "  >";				    
				    }
				    
				echo "</event>\n";  
		      			
			}				   
    
//пятницо

		$data=mysql_query_cache("select * from variables where var='friday_time'  ",false,60);
		$bots=array();
		while(list($k,$row) = each($data)) 
		   {
		   $bots['friday']=$row['value'];
		   }
		
		foreach($bots as $n=>$dat)  
			{
		      
				echo "<event name=\"Пятница\"  outtime=\"".$dat."\"  outdate=\"".date("Y.m.d H:i:s",$dat)."\" ";
				if ($dat <= time())
				   {
				       echo "description=\"Онлайн\"";
				       echo ">";
				    }
				    else
				    {
				    echo "description=\"через: ".prettyTime(null,$dat)."\" ";
				    echo "  >";				    
				    }
				    
				echo "</event>\n";  
		      			
			}	
//Деревья
	

		$data=mysql_query_cache("select * from `variables` where `var`='drevos_out_time'  or `var`='drevos_out'",false,60);
		$bots=array();
		while(list($k,$row) = each($data)) 
		   {
		   	if ($row['var']=='drevos_out_time')
		   		{
				   $bots[0]['h']=$row['value'];
				 }
				 else
			if ($row['var']=='drevos_out')
		   		{
				   $bots[0]['out']=$row['value'];
				 }
		   }
		
		foreach($bots as $n=>$dat)  
			{
					
			$out_bot=mktime($dat['h'],0,0,date("n"),date("d"),date("Y"));
			echo "<event name=\"Древоброды\"  outtime=\"".$out_bot."\"  outdate=\"".date("Y.m.d H:i:s",$out_bot)."\" ";					
				if ($dat[out]>0)
					{
					//были
					echo "description=\"уже были выпущены\"";
				        echo ">";					
					}
					else
					{
					//будут
					    echo "description=\"через: ".prettyTime(null,$out_bot)."\" ";
					    echo "  >";				    
					}
				    
				echo "</event>\n";  
		      			
			}	

echo "</message>";
?>
