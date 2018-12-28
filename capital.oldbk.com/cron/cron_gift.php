#!/usr/bin/php
<?php
include "/www/capitalcity.oldbk.com/cron/init.php";

if( !lockCreate("cron_gift_job") ) {
    exit("Script already running.");
}

$mktime=mktime(16,0,0,date("m"),date("d"),date("y")) + 60 ;

//выбираем подарки для рассылки
$gifts = mysql_query("select * from oldbk.inventory where owner=448 and add_time <='{$mktime}' ; ");
//echo "select * from oldbk.inventory where owner=448 and add_time <='{$mktime}' ; " ;

while($rgift = mysql_fetch_array($gifts)) {
        $to=check_users_city_data($rgift[sowner]);
       
       
        
        
	// меняем овнера на Совнера, Совнера затираем - в поле карман надоходится пол дарителя!!
	// Меняем  поле present - там ЛОГИН и ID, осталвяем ТОЛЬКО логин
	if($rgift[otdel]==72)
	{
	        //A-Tech:|:28453:28453
	        $from=explode(':|:',$rgift[present]);
	        $from_1=explode(':',$from[1]);
	        $from[1]=$from_1[0];
		$from[0]=$from[0].':|:'.$from[1];
	}
	else
	{
		//A-Tech:28453
		$from=explode(':',$rgift[present]);
		//$from[0]=$from[0].':|:'.$from[1];
	}
	
	
	mysql_query("UPDATE oldbk.`inventory` SET `owner`='{$rgift[sowner]}',`sowner`=0, `karman`=0,`present`='".$from[0]."'  WHERE `id`='{$rgift[id]}';");
        $rec['owner']=$to[id];
	$rec['owner_login']=$to[login];
	$rec['owner_balans_do']=$to['money'];
	$rec['owner_balans_posle']=$to['money'];
	$rec['target']=$from[1];
	$rec['target_login']=$from[0];
	$rec['type']=209;//Получаю предмет
	$rec['sum_kr']=0;
	$rec['sum_ekr']=0;
	$rec['sum_kom']=0;
	$rec['item_id']=get_item_fid($rgift);
	$rec['item_name']=$rgift['name'];
	$rec['item_count']=1;
	$rec['item_type']=$rgift['type'];
	$rec['item_cost']=$rgift['cost'];
	$rec['item_dur']=$rgift['duration'];
	$rec['item_maxdur']=$rgift['maxdur'];
	$rec['item_ups']=$rgift['ups'];
	$rec['item_unic']=$rgift['unik'];
	$rec['item_incmagic']=$rgift['includemagicname'];
	$rec['item_incmagic_count']=$rgift['includemagicuses'];
	$rec['item_proto']=$rgift['prototype'];
	$rec['item_sowner']=($rgift['sowner']>0?1:0);
	$rec['item_incmagic_id']=$rgift['includemagic'];
	$rec['item_arsenal']='';
	add_to_new_delo($rec); //юзеру

	$buket_name=$rgift['name'];

	if ($rgift['karman'] == 0) {
		$action="подарила";
	} else {
		$action="подарил";
	}

	if($to[odate]>=(time()-60)) {
		addchp ('<font color=red>Внимание!</font> <span oncontextmenu=OpenMenu()>'.$from[0].'</span> '.$action.' вам <B>'.$buket_name.'</B>.   ','{[]}'.$to['login'].'{[]}',-1,0);
	} else {
		// если в офе
		mysql_query("INSERT INTO oldbk.`telegraph` (`owner`,`date`,`text`) values ('".$to['id']."','','".'<font color=red>Внимание!</font> <span oncontextmenu=OpenMenu()>'.$from[0].'</span> '.$action.' вам <B>'.$buket_name.'</B>.   '."');");
	}
}

lockDestroy("cron_gift_job");
?>