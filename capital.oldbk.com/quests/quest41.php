<?
//входящие данные масив quests
// входящие user
//входящие данные масив:
//   labir  ноер карты


function make_qstart($quest,$telo,$labir) // вызывается при создании карты и внем указываем что нам надо сделать 
{
global $map ;
make_qitem($quest,$telo,$labir);
//echo "start QUEST FUNCTION";
}

function make_qitem($quest,$telo,$labir) //создание итема разброс// используется при создании карты
 {
global $map, $mysql ; //  -масив савой карты - глобально для изменения
 
 $fmap='/www/capitalcity.oldbk.com/labmapsq/'.$labir.'-'.$telo[id].'.qst'; // создаем файлик квестовых итемов - для ид чара в котором хранятся данные о размещении квестовых итемов
 
 $qcount=mysql_fetch_array(mysql_query("select val as qitem from labirint_var  where owner='{$telo[id]}' and var='qlab_count_bot' ")); 
  
 $itemsmap = fopen($fmap,"a+");
 
 if ($qcount[qitem] < $quest[q_bot_count])
  {
  $k_it=1;
  }
  else
  {
   $k_it=0;
  }
 

 $it=1020000; // квест_поинт
 $maxc=count($map);


  $stopc=0;
  while (($k_it > 0) and ($stopc!=600) )
	{

	$rx=mt_rand(5,$maxc-2); // разбрасываем квест поинты - не всамом начале лабы
	$ry=mt_rand(5,$maxc-2);

	
	if ($map[$rx][$ry]=='R') // ставим метки только там где монстры
		{
	 	$map[$rx][$ry]='Q'; 
		$k_it--;
		// Записываем в карту предметов для чара координаты этого квестового итема
		//
		fwrite($itemsmap,$rx.'::'.$ry.'::'.$it."\n");
		}
	$stopc++;			
	}

fclose($itemsmap);
 
 }

//$labpoz - точка x,y
//$Qmap - карта разбросаных итемов
function make_qpoint($quest,$telo,$labir) // создание на карте точки для квестового итема или ботоа
{
global $mob,$rmt ;
//тут же можно кинуть системку
	////////////////////////////////////////id => кол.
	$Q_MOB=array( $quest[q_bot] => 5);
	
	$mob[$rmt]=$Q_MOB; // добавляем монстра к существующей патти
}

function get_qitem($quest,$telo,$labir)  // поднятие с пола квестового итема
{
// функции добавления в коде лабы
//global $map ,$labpoz,$Qmap,$mysql ;
//echo "Поднятие предмета";
//addchp ('<font color=red>Внимание!</font> '.$quest[qsystem],'{[]}'.$telo['login'].'{[]}');

}

function get_drop_qitem($quest,$telo,$labir) //выпадение из бота
{



}

function get_kill_bot($quest,$telo,$labir) // убивание бота
{
global $labpoz,$mysql ;
//инсертим не активный - квест итем - активируется сам  если бой будет победой
//mysql_query("INSERT `labirint_items` (`map`,`item`,`x`,`y`,`active`,`count`,`val`,`owner`) values('".$labir."','I','".$labpoz[x]."','".$labpoz[y]."','0','-1','{$_SESSION['questdata'][q_item]}','{$telo[id]}' ) ON DUPLICATE KEY UPDATE `active` =1 ;");

   mysql_query("INSERT `labirint_var` (`owner`,`var`,`val`) values('".$telo[id]."', 'qlab_count_bot', '1' ) ON DUPLICATE KEY UPDATE `val` =`val`+1;");
addchp ('<font color=red>Внимание!</font> '.$quest[qsystem],'{[]}'.$telo['login'].'{[]}');
}

function get_qcount($quest,$telo,$labir) //функция определения сколько собрано чего надо
{
global $map ,$labpoz,$Qmap,$mysql ;

$qcount=mysql_fetch_array(mysql_query("select val as qitem from labirint_var  where owner='{$telo[id]}' and var='qlab_count_bot' ")); 
echo $qcount[qitem];
echo "/";
echo $quest[q_bot_count];
echo "<br>";
if ($qcount[qitem]>=$quest[q_bot_count]) { return true; } else {return false;}
}

function get_qitem_check($quest,$telo,$labir) // проверка на подходимость квестового предмета если их несколько
{
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

  mysql_query("INSERT `labirint_var` (`owner`,`var`,`val`) values('".$telo[id]."', 'qlab_count_bot', '0' ) ON DUPLICATE KEY UPDATE `val` =0;");
}

?>