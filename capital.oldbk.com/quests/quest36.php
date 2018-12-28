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
 
 $qcount=mysql_fetch_array(mysql_query("select count(id) as qitem from oldbk.inventory where owner='{$telo[id]}' and sowner='{$telo[id]}' and prototype='{$quest[q_item]}' and setsale=0 ;"));
 
 $itemsmap = fopen($fmap,"a+");
 $k_it=$quest[q_item_count]-$qcount[qitem];
 $it=$quest[q_item];
 $maxc=count($map);


  $stopc=0;
  while (($k_it > 0) and ($stopc!=600) )
	{

	$rx=mt_rand(4,$maxc-2);
	$ry=mt_rand(4,$maxc-2);

	
	if ($map[$rx][$ry]=='O')
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
global $map ,$labpoz,$Qmap,$mysql ;
mysql_query("INSERT `labirint_items` (`map`,`item`,`x`,`y`,`active`,`count`,`val`,`owner`) values('".$labir."','Q','".$labpoz[x]."','".$labpoz[y]."','1','1','{$Qmap[$labpoz[x]][$labpoz[y]]}','{$telo[id]}' ) ON DUPLICATE KEY UPDATE `active` =1 ;");
mysql_query("INSERT `labirint_items` (`map`,`item`,`x`,`y`,`active`,`val`,`owner`) values('".$labir."','I','".$labpoz[x]."','".$labpoz[y]."','0','{$Qmap[$labpoz[x]][$labpoz[y]]}','{$telo[id]}' ) ON DUPLICATE KEY UPDATE `active` =1 ;");
//тут же можно кинуть системку
}

function get_qitem($quest,$telo,$labir)  // поднятие с пола квестового итема
{
// функции добавления в коде лабы
global $map ,$labpoz,$Qmap,$mysql ;
//echo "Поднятие предмета";
addchp ('<font color=red>Внимание!</font> '.$quest[qsystem],'{[]}'.$telo['login'].'{[]}');

}

function get_drop_qitem($quest,$telo,$labir) //выпадение из бота
{
}

function get_kill_bot($quest,$telo,$labir) // убивание бота
{

}

function get_qcount($quest,$telo,$labir) //функция определения сколько собрано чего надо
{
global $map ,$labpoz,$Qmap,$mysql ;
$qcount=mysql_fetch_array(mysql_query("select count(id) as qitem from oldbk.inventory where owner='{$telo[id]}' and sowner='{$telo[id]}' and prototype='{$quest[q_item]}' and setsale=0 ;"));
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

	$test_dop_inf=mysql_fetch_array(mysql_query("select * from oldbk.inventory where owner='{$telo[id]}' and sowner='{$telo[id]}' and prototype='{$quest[q_item]}' and setsale=0 and duration=0;"));
 	if (($test_dop_inf[id]>0) and ($test_dop_inf[duration]==0))
 		{
 		mysql_query("INSERT INTO oldbk.`inventory` (`getfrom`,`name`,`duration`,`maxdur`,`cost`,`owner`,`nlevel`,`nsila`,`nlovk`,`ninta`,`nvinos`,`nintel`,`nmudra`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nalign`,`minu`,`maxu`,`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`img`,`text`,`dressed`,`bron1`,`bron2`,`bron3`,`bron4`,`dategoden`,`magic`,`type`,`present`,`sharped`,`massa`,`goden`,`needident`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`letter`,`isrep`,`update`,`setsale`,`prototype`,`otdel`,`bs`,`gmp`,`includemagic`,`includemagicdex`,`includemagicmax`,`includemagicname`,`includemagicuses`,`includemagiccost`,`includemagicekrcost`,`gmeshok`,`tradesale`,`karman`,`stbonus`,`upfree`,`ups`,`mfbonus`,`mffree`,`type3_updated`,`bs_owner`,`nsex`,`present_text`,`add_time`,`labonly`,`labflag`,`prokat_idp`,`prokat_do`,`arsenal_klan`,`repcost`,`up_level`,`ecost`,`group`,`ekr_up`,`unik`,`add_pick`,`pick_time`,`sowner`,`idcity`) VALUES ('10','Чек на предъявителя 20кр',0,1,20,'{$telo[id]}',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'lab2_20kr.gif','',0,0,0,0,0,0,0,50,'',0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'',0,NOW(),0,3203,'52',0,0,0,0,0,'',0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL,0,0,0,0,NULL,'',0,0,0,1,NULL,0,NULL,NULL,0,'{$user[id_city]}');"); 		
		echo "<br><font color=red><b>Спасибо что починил! Я компенсирую твои затраты сполна! <br>Вы получили:\"Чек на предъявителя 20кр\".<b></font>";	
 		}
 
	mysql_query("delete from oldbk.inventory where owner='{$telo[id]}' and prototype='{$quest[q_item]}' and setsale=0 and sowner='{$telo[id]}'");
}

?>