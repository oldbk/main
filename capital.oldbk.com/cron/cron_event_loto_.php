#!/usr/bin/php
<?php
include "/www/capitalcity.oldbk.com/cron/init.php";
require_once('/www/capitalcity.oldbk.com/functions.php');
if( !lockCreate("cron_event_loto") )
{
	exit("Script already running.");
}

$max_owner_win=4; //максимально сколько тело может выиграть


function mk_my_item_loto($telo,$proto)
{
	$dress=mysql_fetch_array(mysql_query("select * from oldbk.eshop where id='{$proto}' ;"));

	if ($dress['id']==538)
	{
		$dress['sowner']=$telo['id'];
	}
	else
	{
		$dress['sowner']=0;
	}

	if ($dress[id]>0)
	{
		if(mysql_query("INSERT INTO oldbk.`inventory`
		(`prototype`,`owner`,`sowner`,`name`,`type`,`massa`,`cost`, `ecost`, `img`,`maxdur`,`isrep`,`letter`,
		`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,
		`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
		`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`nsex`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`otdel`,`group`,`mfbonus`,`gmp`,`arsenal_klan` , `arsenal_owner`,`idcity`,`ab_mf`,`ab_bron`,`ab_uron`,`includemagic` , `includemagicdex` , `includemagicmax` , `includemagicname` , `includemagicuses` , `includemagiccost` , `includemagicekrcost`,`present`
		)
		VALUES
			('{$dress['id']}','{$telo[id]}','{$dress['sowner']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']}, {$dress['ecost']}, '{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['letter']}','{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
			'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress[nsex]}',
			'{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".(($dress['goden'])?($dress['goden']*24*60*60+time()):"")."',
			'{$dress['goden']}','{$dress['razdel']}','{$dress['group']}','{$dress['mfbonus']}','{$dress['gmp']}','','','{$telo[id_city]}','{$dress['ab_mf']}','{$dress['ab_bron']}','{$dress['ab_uron']}', '{$dress['includemagic']}' , '{$dress['includemagicdex']}' , '{$dress['includemagicmax']}' , '{$dress['includemagicname']}' , '{$dress['includemagicuses']}' , '{$dress['includemagiccost']}' , '{$dress['includemagicekrcost']}' , '{$dress['present']}'
			) ;"))
		{
			$good = 1;
			$insert_item_id=mysql_insert_id();
			$dress['idcity']=$telo[id_city];
			$dress['id']=$insert_item_id;
		} else {
			$good = 0;
		}


		if ($good) {
			$rec['owner']=$telo[id];
			$rec['owner_login']=$telo[login];
			$rec['target']=0;
			$rec['target_login']='Пасхальная лотерея';
			$rec['owner_balans_do']=$telo[money];
			$rec['owner_balans_posle']=$telo[money];
			$rec['type']=1185;//
			$rec['sum_kr']=0;
			$rec['sum_ekr']=$dress['ecost'];
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
			$rec['item_incmagic']=$dress['includemagic'];
			$rec['item_incmagic_count']=$dress['includemagicdex'];
			$rec['item_arsenal']='';
			$rec['add_info']='';
			add_to_new_delo($rec);
			//return $dress['name']."[0/".$dress['maxdur']."]".", ";
			return $dress['name'];
		} else {
			return false;
		}
	} else {
		return false;
	}
}


if (time()>=mktime(12,00,00,4,13,2015))
{
///////////////////////////////////////////////////////////////////////////////////////////////////////////
// 0 проверка активна ли лото

	$get_lot=mysql_fetch_array(mysql_query("select * from oldbk.variables where var='event_loto_status' "));
	if ($get_lot['value']==1)
	{
		//все ок продолжаем

		//1 . выгребаем призы - которые есть

		$get_items=mysql_query("select * from oldbk.event_loto_items where kol>0");
		if (mysql_num_rows($get_items)>0 )
		{
			$ii=0;
			while($item=mysql_fetch_array($get_items))
			{
				$ii++;
				//есть призы
				//ещем кандидата
				$get_owner=mysql_fetch_array(mysql_query("select * from oldbk.event_loto where win<'{$max_owner_win}' order by rand() limit 1"));
				//$get_owner=mysql_fetch_array(mysql_query("select * from oldbk.event_loto where win>=5 order by rand() limit 1")); // for test
				//$get_owner=mysql_fetch_array(mysql_query("select * from oldbk.event_loto where owner in (326,457757) order by rand() limit 1")); // for test

				if ($get_owner['owner']>0)
				{
					$telo=mysql_fetch_array(mysql_query("select * FROM oldbk.users where id='{$get_owner['owner']}' "));
					$winitem=mk_my_item_loto($telo,$item['itemproto']);
					if ($winitem)
					{
						//поздравляем
						telepost_new($telo,'<font color=red>Внимание!</font>У Вас выиграл билет Пасхальной лотереи №'.$get_owner['id'].'. Вы получили <b>"'.$winitem.'"</b>. Поздравляем!');
						//записыаем ему +1 во всех его билетах по овнеру
						mysql_query("UPDATE `oldbk`.`event_loto` SET `win`=`win`+1 WHERE `owner`='{$telo['id']}' ");
						//пишем в лог
						$to_log_text="<FONT class=date>".date("d.m.y H:i",time())."</FONT> Предмет <b>".$winitem."</b> - выиграл персонаж:".s_nick($telo['id'],$telo['align'],$telo['klan'],$telo['login'],$telo['level'])." - Билет № ".$get_owner['id'];
						echo $to_log_text; //лог текстовый
						echo "<br>";
						mysql_query("INSERT INTO `oldbk`.`event_loto_win` SET `owner`='{$telo['id']}',`inf`='{$to_log_text}' ");
						//делаем -1 в таблицу призов
						mysql_query("UPDATE `oldbk`.`event_loto_items` SET `kol`=`kol`-1 WHERE `id`='{$item['id']}' " );
					}
				}

			}
			//раздача этой минуты закончена
			$mi=(int)(date("i",time()));
			if (($mi==10) OR  ($mi==20) OR  ($mi==30) OR  ($mi==40) OR  ($mi==50) OR  ($mi==0) )
			{
				//системки каждые 10 мин
				$get_all_wins=mysql_fetch_array(mysql_query("select count(id) as ko from oldbk.event_loto_win"));
				$TEXT='[Комментатор] Внимание! В Пасхальной лотерее разыграно уже '.$get_all_wins['ko'].' призов. Список победителей можно посмотреть ';
				$TEXT.='<a href="javascript:void(0)" onclick='.(!is_array($_SESSION['vk'])?"top.":"parent.").'cht("http://capitalcity.oldbk.com/event_lotery.php")>здесь</a>. ';
				$TEXT.='С праздником!  ';
				addch2all($TEXT);
				//addchp ('<font color=red>'.$TEXT.'</font>','{[]}Bred{[]}');
			}

		}
		else
		{
			//Finish
			//призов нет
			//пишем системку что конец и ставим флаг в 0
			mysql_query("UPDATE `oldbk`.`variables` SET `value`='0' WHERE `var`='event_loto_status';");
			$TEXT='[Комментатор] Внимание! Розыгрыш Пасхальной лотереи окончен. Разыграны все 600 призов. Список победителей можно посмотреть ';
			$TEXT.='<a href="javascript:void(0)" onclick='.(!is_array($_SESSION['vk'])?"top.":"parent.").'cht("http://capitalcity.oldbk.com/event_lotery.php")>здесь</a>. ';
			$TEXT.='С праздником!  ';
			addch2all($TEXT);
			//addchp ('<font color=red>'.$TEXT.'</font>','{[]}Bred{[]}');
		}

	}
	else
	{
		echo "лото выключено";
	}

}
else
{
	echo "лото выключено - еще не время";
}


lockDestroy("cron_event_loto");
?>