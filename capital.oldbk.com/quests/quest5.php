<?
//вход€щие данные масив quests
// вход€щие user
//вход€щие данные масив:
//   labir  ноер карты


function make_qstart($quest,$telo,$labir) // вызываетс€ при создании карты и внем указываем что нам надо сделать 
{
global $map ;
make_qitem($quest,$telo,$labir);
//echo "start QUEST FUNCTION";
}

function make_qitem($quest,$telo,$labir) //создание итема разброс// используетс€ при создании карты
 {
global $map, $mysql ; //  -масив савой карты - глобально дл€ изменени€
 
 $fmap='/www/capitalcity.oldbk.com/labmapsq/'.$labir.'-'.$telo[id].'.qst'; // создаем файлик квестовых итемов - дл€ ид чара в котором хран€тс€ данные о размещении квестовых итемов
 
 $qcounta=mysql_query("select count(id) as qitem , prototype from inventory where owner='{$telo[id]}' and sowner='{$telo[id]}' and prototype in ('{$quest[q_item]}','{$quest[q_item2]}','{$quest[q_item3]}','{$quest[q_item4]}') and setsale=0 GROUP by prototype;");
 
 $k_it=$quest[q_item_count];
 $k_it2=$quest[q_item2_count];
 $k_it3=$quest[q_item3_count];
 $k_it4=$quest[q_item4_count];
 $maxc=count($map);
 
 while ($qcount = mysql_fetch_array($qcounta)) 
		{
		$cc[$qcount[prototype]]=$qcount[qitem]; 
		}
  
 $k_it=$quest[q_item_count]-$cc[$quest[q_item]];
 $k_it2=$quest[q_item2_count]-$cc[$quest[q_item2]];
 $k_it3=$quest[q_item3_count]-$cc[$quest[q_item3]];
 $k_it4=$quest[q_item4_count]-$cc[$quest[q_item4]];
  
 $itemsmap = fopen($fmap,"a+");

 $it=$quest[q_item];
$stopc=0;
  while (($k_it > 0) and ($stopc!=600) )
	{
	$rx=mt_rand(2,$maxc-2);	$ry=mt_rand(2,$maxc-2);
	
	if ($map[$rx][$ry]=='O')
		{
	 	$map[$rx][$ry]='Q'; 
		$k_it--;
		// «аписываем в карту предметов дл€ чара координаты этого квестового итема
		//
		fwrite($itemsmap,$rx.'::'.$ry.'::'.$it."\n");
		}
	$stopc++;		
	}
////////////	
	$it=$quest[q_item2];
	$stopc=0;
	while (($k_it2 > 0) and ($stopc!=600) )
	{
	$rx=mt_rand(2,$maxc-2);
	$ry=mt_rand(2,$maxc-2);
	
	if ($map[$rx][$ry]=='O')
		{
	 	$map[$rx][$ry]='Q'; 
		$k_it2--;
		// «аписываем в карту предметов дл€ чара координаты этого квестового итема
		//
		fwrite($itemsmap,$rx.'::'.$ry.'::'.$it."\n");
		}
	$stopc++;		
	}
////////////	
	$it=$quest[q_item3];
	$stopc=0;
	while (($k_it3 > 0) and ($stopc!=600) )
	{
	$rx=mt_rand(2,$maxc-2);
	$ry=mt_rand(2,$maxc-2);
	
	if ($map[$rx][$ry]=='O')
		{
	 	$map[$rx][$ry]='Q'; 
		$k_it3--;
		// «аписываем в карту предметов дл€ чара координаты этого квестового итема
		//
		fwrite($itemsmap,$rx.'::'.$ry.'::'.$it."\n");
		}
	$stopc++;		
	}
////////////	
	$stopc=0;
	$it=$quest[q_item4];
	while (($k_it4 > 0) and ($stopc!=600) )
	{
	$rx=mt_rand(2,$maxc-2);
	$ry=mt_rand(2,$maxc-2);
	
	if ($map[$rx][$ry]=='O')
		{
	 	$map[$rx][$ry]='Q'; 
		$k_it4--;
		// «аписываем в карту предметов дл€ чара координаты этого квестового итема
		//
		fwrite($itemsmap,$rx.'::'.$ry.'::'.$it."\n");
		}
	$stopc++;		
	}

fclose($itemsmap);
 
 }

//$labpoz - точка x,y
//$Qmap - карта разбросаных итемов
function make_qpoint($quest,$telo,$labir) // создание на карте точки дл€ квестового итема или ботоа
{
global $map ,$labpoz,$Qmap,$mysql ;
mysql_query("INSERT `labirint_items` (`map`,`item`,`x`,`y`,`active`,`count`,`val`,`owner`) values('".$labir."','Q','".$labpoz[x]."','".$labpoz[y]."','1','1','{$Qmap[$labpoz[x]][$labpoz[y]]}','{$telo[id]}' ) ON DUPLICATE KEY UPDATE `active` =1 ;");
mysql_query("INSERT `labirint_items` (`map`,`item`,`x`,`y`,`active`,`val`,`owner`) values('".$labir."','I','".$labpoz[x]."','".$labpoz[y]."','0','{$Qmap[$labpoz[x]][$labpoz[y]]}','{$telo[id]}' ) ON DUPLICATE KEY UPDATE `active` =1 ;");
//тут же можно кинуть системку
}

function get_qitem($quest,$telo,$labir)  // подн€тие с пола квестового итема
{
// функции добавлени€ в коде лабы
global $map ,$labpoz,$Qmap,$mysql,$dress ;
//echo "ѕодн€тие предмета";
addchp ('<font color=red>¬нимание!</font> '.$quest[qsystem].$dress[name],'{[]}'.$telo['login'].'{[]}',$telo['room'],$telo['id_city']);

}

function get_drop_qitem($quest,$telo,$labir) //выпадение из бота
{
}

function get_kill_bot($quest,$telo,$labir) // убивание бота
{

}

function get_qcount($quest,$telo,$labir) //функци€ определени€ сколько собрано чего надо
{
global $map ,$labpoz,$Qmap,$mysql ;
 $qcounta=mysql_query("select count(id) as qitem , prototype from inventory where owner='{$telo[id]}' and sowner='{$telo[id]}' and prototype in ('{$quest[q_item]}','{$quest[q_item2]}','{$quest[q_item3]}','{$quest[q_item4]}') and setsale=0 GROUP by prototype;");
 
 while ($qcount = mysql_fetch_array($qcounta)) 
		{
 		$cc[$qcount[prototype]]=$qcount[qitem]; 
		}
 $cc[$quest[q_item]]=(int)($cc[$quest[q_item]]);
 $cc[$quest[q_item2]]=(int)($cc[$quest[q_item2]]);
 $cc[$quest[q_item3]]=(int)($cc[$quest[q_item3]]);
 $cc[$quest[q_item4]]=(int)($cc[$quest[q_item4]]);  

echo $cc[$quest[q_item]]; echo "/"; echo $quest[q_item_count]; echo " -ќгонь, ";
echo $cc[$quest[q_item2]]; echo "/"; echo $quest[q_item2_count]; echo "-¬оздух, ";
echo $cc[$quest[q_item3]]; echo "/"; echo $quest[q_item3_count]; echo "-√рунт  , ";
echo $cc[$quest[q_item4]]; echo "/"; echo $quest[q_item4_count];echo "-¬ода"; 
echo "<br>";
if 
	( ($cc[$quest[q_item]]>=$quest[q_item_count]) and
	($cc[$quest[q_item2]]>=$quest[q_item2_count]) and
	($cc[$quest[q_item3]]>=$quest[q_item3_count]) and	
	($cc[$quest[q_item4]]>=$quest[q_item4_count]) )
	
	{ return true; } else {return false;}
}

function get_qitem_check($quest,$telo,$labir) // проверка на подходимость квестового предмета если их несколько
{
global $map ,$labpoz,$Qmap,$mysql ;

  return "TRUE";

}

function make_qfin($quest,$telo) // окончание квеста
{
if (get_qcount($quest,$telo,0)==true)
             {
	     return true;             
             }
             else
             {
	     return false;
	     }
}

function make_qfin_del($quest,$telo) // окончание квеста - уделение ненужных вещей после квеста
{
global $mysql ;
mysql_query("delete from inventory where owner='{$telo[id]}' and prototype in ('{$quest[q_item]}','{$quest[q_item2]}','{$quest[q_item3]}','{$quest[q_item4]}') and setsale=0 and sowner='{$telo[id]}'");
}

?>