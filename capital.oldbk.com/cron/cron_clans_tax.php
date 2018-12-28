#!/usr/bin/php
<?php
die();
include "/www/capitalcity.oldbk.com/cron/init.php";
if( !lockCreate("cron_clans_tax") ) {
    exit("Script already running.");
}



$KLAN_PRICE=30;


function send_to_clan($klan_name,$msg)
{
						 		$data=mysql_query("select * from oldbk.users where klan='".$klan_name."';");
		 						while($sok=mysql_fetch_array($data))
							 	{
	 							telegraph_new($sok,$msg,'1',time()+(2*24*3600));
							 	}
}

// за день до окончания оплаты
// проверка всех кто должен завтра оплатить хватает ли денег
// если нет отправка сообщения
	$get_clans_msg=mysql_query("select co.id,co.short,cr.id as rid,cr.short as rshort, co.tax_date  from oldbk.`clans` co  left join oldbk.`clans` cr  on co.rekrut_klan=cr.id where co.base_klan=0 AND (co.short not in ('Adminion','radminion','pal','ytesters','ztesters','xtesters','3testers','4testers','3testers1','4testers1','5testers','6testers','6testers1','rаdminion')) AND co.time_to_del=0 AND co.tax_date>UNIX_TIMESTAMP() AND co.tax_date<=(UNIX_TIMESTAMP()+86400) AND co.tax_date>0  and co.`tax_auto`=1   order by short");
	
	if (mysql_num_rows($get_clans_msg) >0)
		{
		   while($crow=mysql_fetch_array($get_clans_msg))
		   	{
		   	$KLAN_COST=$KLAN_PRICE;
		   	
			   	if ($crow['rid']>0)
			   	{
					$KLAN_COST=$KLAN_COST*2; // у кого есть рекруты
				}
				
				 // проверка казны
				$get_k=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.clans_kazna WHERE `clan_id` = '{$crow['id']}';"));
				
				if ($get_k['kr']<$KLAN_COST)
				{
					$dolg=($KLAN_COST-$get_k['kr']);
					$dtm=date("d.m.Y H:i",$crow['tax_date']);
				   	$msg="Внимание! В казне вашего клана недостаточно средств для уплаты налога. Необходимо пополнить казну на <b>{$dolg} кр.</b> до <b>{$dtm}</b>, иначе клан будет расформирован за неуплату налогов.";
				   	send_to_clan($crow['short'],$msg);
				   	if ($crow['rid']>0)
				   		{
					   	$msg="Внимание! В казне вашего основного клана недостаточно средств для уплаты налога. Необходимо пополнить казну основного клана на <b>{$dolg} кр.</b> до <b>{$dtm}</b>, иначе клан будет расформирован за неуплату налогов.";
					   	send_to_clan($crow['rshort'],$msg);
				   		}
				}
		   	
		   	}
		}

//проверка кто должен оплатить сегодня
// если бабки есть списываем
// если нет отправка сообщения

	$get_clans_msg=mysql_query("select co.id,co.short,cr.id as rid,cr.short as rshort, co.tax_date  from oldbk.`clans` co  left join oldbk.`clans` cr  on co.rekrut_klan=cr.id where co.base_klan=0 AND (co.short not in ('Adminion','radminion','pal','ytesters','ztesters','xtesters','3testers','4testers','3testers1','4testers1','5testers','6testers','6testers1','rаdminion')) AND co.time_to_del=0 AND co.tax_date<=UNIX_TIMESTAMP() AND co.tax_date>0 and co.`tax_auto`=1   order by short");
	
	if (mysql_num_rows($get_clans_msg) >0)
		{
		   while($crow=mysql_fetch_array($get_clans_msg))
		   	{
		   	$KLAN_COST=$KLAN_PRICE;
		   	
			   	if ($crow['rid']>0)
			   	{
					$KLAN_COST=$KLAN_COST*2; // у кого есть рекруты
				}
				
				 // проверка казны
				$get_k=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.clans_kazna WHERE `clan_id` = '{$crow['id']}';"));
				
				if ($get_k['kr']<$KLAN_COST)
				{
					$dolg=($KLAN_COST-$get_k['kr']);
					$dtm=date("d.m.Y H:i",$crow['tax_date']);
				   	$msg="Внимание! В казне вашего клана недостаточно средств для уплаты налога. Необходимо пополнить казну на <b>{$dolg} кр.</b>, иначе клан будет расформирован за неуплату налогов.";
				   	send_to_clan($crow['short'],$msg);
				   	if ($crow['rid']>0)
				   		{
					   	$msg="Внимание! В казне вашего основного клана недостаточно средств для уплаты налога. Необходимо пополнить казну основного клана на <b>{$dolg} кр.</b>, иначе клан будет расформирован за неуплату налогов.";
					   	send_to_clan($crow['rshort'],$msg);
				   		}
				}
				else
				{
				//есть деньги оплачиваем
					
						mysql_query("UPDATE oldbk.clans_kazna set kr=kr-{$KLAN_COST}  WHERE `clan_id` = '{$crow['id']}' and kr>={$KLAN_COST} ");
						
						if (mysql_affected_rows()>0)
						{
							$ids=$crow['id'];   if ($crow['rid']>0) { $ids.=','.$crow['rid'];}
						        $txt=" Оплата из клан казны налога, ".$KLAN_COST." кр.";
						        mysql_query("INSERT INTO oldbk.`clans_kazna_log` (`method` ,`ktype`, `clan_id`, `owner`, `target`, `kdate`)  VALUES  ('2','1','{$crow['id']}','0','".$txt."','".time()."');");						           
							mysql_query("UPDATE oldbk.clans SET tax_timer=0, tax_date='".($crow[tax_date]+60*60*24*31)."' WHERE id in (".$ids.");");
							//echo '<font color=red><b>Вы оплатили налог '.$summ_topay.'кр. из казны клана.</b></font><br>';
						}
						
				}
		   	
		   	}
		}




lockDestroy("cron_clans_tax");
?>