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
 
 $qcount=mysql_fetch_array(mysql_query("select count(id) as qitem from inventory where owner='{$telo[id]}' and sowner='{$telo[id]}' and prototype='{$quest[q_item]}' and setsale=0 ;"));
 
 $itemsmap = fopen($fmap,"a+");

  if ($qcount[qitem] > ($quest[q_item_count]*0.5)  ) 
  		{
		$k_it=$quest[q_item_count]-$qcount[qitem];
  		}
      else
      {
      $k_it=round(($quest[q_bot_count])*0.5);
      }

 $it=$quest[q_item];
 $maxc=count($map);
			$k_it++;//добавим 1 лишний
$stopc=0;
  while (($k_it > 0) and ($stopc!=600) )
	{

	$rx=mt_rand(8,$maxc-2); // разбрасываем квест поинты - не всамом начале лабы
	$ry=mt_rand(8,$maxc-2);

	
	if ($map[$rx][$ry]=='R') // ставим метки только там где монстры
		{
	 	$map[$rx][$ry]='Q'; 
		$k_it--;
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
global $mob,$rmt ;
//тут же можно кинуть системку
	////////////////////////////////////////id => кол.
	$Q_MOB=array( $quest[q_bot] => 1);
	$mob[$rmt]=$mob[$rmt]+$Q_MOB; // добавл€ем монстра к существующей патти
}

function get_qitem($quest,$telo,$labir)  // подн€тие с пола квестового итема
{
// функции добавлени€ в коде лабы
global $map ,$labpoz,$Qmap,$mysql ;
//echo "ѕодн€тие предмета";
addchp ('<font color=red>¬нимание!</font> '.$quest[qsystem],'{[]}'.$telo['login'].'{[]}',$telo['room'],$telo['id_city']);

}

function get_drop_qitem($quest,$telo,$labir) //выпадение из бота
{



}

function get_kill_bot($quest,$telo,$labir) // убивание бота
{
global $labpoz,$mysql ;
//инсертим не активный - квест итем - активируетс€ сам  если бой будет победой
mysql_query("INSERT `labirint_items` (`map`,`item`,`x`,`y`,`active`,`count`,`val`,`owner`) values('".$labir."','I','".$labpoz[x]."','".$labpoz[y]."','0','-1','{$_SESSION['questdata'][q_item]}','{$telo[id]}' ) ON DUPLICATE KEY UPDATE `active` =1 ;");
}

function get_qcount($quest,$telo,$labir) //функци€ определени€ сколько собрано чего надо
{
global $map ,$labpoz,$Qmap,$mysql ;
$qcount=mysql_fetch_array(mysql_query("select count(id) as qitem from inventory where owner='{$telo[id]}' and sowner='{$telo[id]}' and prototype='{$quest[q_item]}' and setsale=0 ;"));
echo $qcount[qitem];
echo "/";
echo $quest[q_item_count];
echo "<br>";
if ($qcount[qitem]>=$quest[q_item_count]) { return true; } else {return false;}
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
mysql_query("delete from inventory where owner='{$telo[id]}' and prototype='{$quest[q_item]}' and setsale=0 and sowner='{$telo[id]}'");

  if(mysql_affected_rows() > 4) //если было перевыполнено то даем бонус 
  	{

	mysql_query("INSERT INTO `inventory` (`getfrom`,`name`,`duration`,`maxdur`,`cost`,`owner`,`nlevel`,`nsila`,`nlovk`,`ninta`,`nvinos`,`nintel`,`nmudra`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nalign`,`minu`,`maxu`,`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`img`,`text`,`dressed`,`bron1`,`bron2`,`bron3`,`bron4`,`dategoden`,`magic`,`type`,`present`,`sharped`,`massa`,`goden`,`needident`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`letter`,`isrep`,`update`,`setsale`,`prototype`,`otdel`,`bs`,`gmp`,`includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`includemagiccost`,`includemagicekrcost`,`gmeshok`,`tradesale`,`karman`,`stbonus`,`upfree`,`ups`,`mfbonus`,`mffree`,`type3_updated`,`bs_owner`,`nsex`,`present_text`,`add_time`,`labonly`,`labflag`,`prokat_idp`,`prokat_do`,`arsenal_klan`,`repcost`,`up_level`,`ecost`,`group`,`ekr_up`,`unik`,`add_pick`,`pick_time`,`sowner`) VALUES ('10','ѕознание Ћабиринта',0,1,1,'{$telo[id]}',7,0,0,0,0,3,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'lmap.gif','',0,0,0,0,0,0,78,12,'',0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'',0,'',0,50078,'52',0,0,0,0,0,'',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL,0,0,0,0,NULL,'',0,0,0,0,NULL,0,NULL,NULL,0);");
		echo "<br><font color=red><b>¬ы получили:\"ѕознание Ћабиринта\" за перевыполнение нормы<b></font>";				


	}

}

?>