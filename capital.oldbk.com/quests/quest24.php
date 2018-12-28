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
      $k_it=round(($quest[q_item_count])*0.5);
      }


 $it=$quest[q_item];
 $maxc=count($map);

$k_it++;//добавим 1 лишний

$stopc=0;
  while (($k_it > 0) and ($stopc!=600) )
	{

	$rx=mt_rand(2,$maxc-2);
	$ry=mt_rand(2,$maxc-2);

	
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
global $map ,$labpoz,$Qmap,$mysql ;
//echo "ѕодн€тие предмета";
addchp ('<font color=red>¬нимание!</font> '.$quest[qsystem],'{[]}'.$telo['login'].'{[]}',$telo['room'],$telo['id_city']);

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

  if(mysql_affected_rows() > 20) //если было перевыполнено то даем бонус 
  	{
	

		$dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`shop` WHERE `id` =105") );

	if ($dress['id']>0)
			{
			
			$as_present='Ћабиринт';
			
					if(mysql_query("INSERT INTO oldbk.`inventory`
						(`getfrom`,`prototype`,`sowner`,`present`,`owner`,`name`,`type`,`massa`,`cost`, `ecost`, `img`,`maxdur`,`isrep`,
							`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
							`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark` ".$str." , 
							`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`nsex`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`,`mfbonus`,`gmp`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`, `includemagic` , `includemagicdex` , `includemagicmax` , `includemagicname` , `includemagicuses` , `includemagiccost` , `includemagicekrcost`
						)
						VALUES
						('10','{$dress['id']}','{$as_sowner}','{$as_present}','{$telo['id']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['ecost']}, '{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}' ".$sql." ,
						'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress[nsex]}',
						'{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."',
						'{$dress['goden']}','{$dress['razdel']}','{$dress['group']}','{$dress['mfbonus']}','{$dress['gmp']}','{$telo[id_city]}','{$dress['ab_mf']}','{$dress['ab_bron']}','{$dress['ab_uron']}','{$dress['includemagic']}' , '{$dress['includemagicdex']}' , '{$dress['includemagicmax']}' , '{$dress['includemagicname']}' , '{$dress['includemagicuses']}' , '{$dress['includemagiccost']}' , '{$dress['includemagicekrcost']}'
						) ;"))
						{
							$good = 1;
							$dress['id']=mysql_insert_id();
						}
	
				if ($good == 1)
					{
	
						$rec['owner']=$telo[id];
						$rec['owner_login']=$telo[login];
						$rec['owner_balans_do']=$telo[money];
						$rec['owner_balans_posle']=$telo[money];
						$rec['target']=0;
						$rec['target_login']='Ћабиринт';
						$rec['type']=255;
						$rec['sum_kr']=0;
						$rec['sum_ekr']=0;
						$rec['sum_kom']=0;
						$rec['item_id']='cap'.$dress['id'];
						$rec['item_name']=$dress['name'];
						$rec['item_count']=1;
						$rec['item_type']=$dress['type'];
						$rec['item_cost']=$dress['cost'];
						$rec['item_dur']=$dress['duration'];
						$rec['item_maxdur']=$dress['maxdur'];
						$rec['item_ups']=0;
						$rec['item_unic']=0;
						$rec['item_incmagic']='';
						$rec['item_incmagic_count']='';
						$rec['item_arsenal']='';
						add_to_new_delo($rec);
					
					echo "<br><font color=red><b>¬ы получили:\"".$dress['name']."\" за перевыполнение нормы<b></font>";	
					}
			}
	



	}

}

?>