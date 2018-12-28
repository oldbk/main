#!/usr/bin/php
<?php
//ini_set('display_errors','On');
//echo "start ------------";
include "/www/capitalcity.oldbk.com/cron/init.php";

if( !lockCreate("cron_item_break_job") ) {
	exit("Script already running.");
}

// ломаем шмот
$query = mysql_query("SELECT * FROM oldbk.`inventory` WHERE  `dressed` = 0 AND `dategoden` > 0 AND `dategoden` <= '".time()."'  limit 10000");
while($it = mysql_fetch_assoc($query)) {

	$owner=check_users_city_data($it['owner']);

	if (!((($owner['room']>=197 and $owner['room']<=199) or
  ($owner['room']>=211 and $owner['room']<240) or
  ($owner['room']>240 and $owner['room']<270) or
  ($owner['room']>270 and $owner['room']<290) ) or
  ($owner['in_tower'] >0) ))
	{

	if($it[add_pick]!='') {
		undress_img($it);
	}

	if($it['setsale']>0) {
		mysql_query("DELETE FROM oldbk.`comission_indexes` WHERE id_item = '".$it[id]."' LIMIT 1");
	}

	if($it['arsenal_klan']!='')
		{
		mysql_query("DELETE FROM oldbk.`clans_arsenal` WHERE id_inventory = '".$it[id]."' LIMIT 1");
		}

	mysql_query("DELETE FROM oldbk.`inventory` WHERE `id` = '".$it['id']."' LIMIT 1;");


	if ($owner) {
	 	$rec['owner']=$owner[id];
		$rec['owner_login']=$owner[login];
		$rec['owner_balans_do']=$owner['money'];
		$rec['owner_balans_posle']=$owner['money'];
		$rec['target']=0;
		$rec['target_login']='Срок годности предмета';
		$rec['type']=35;  //разрушение предмета
		$rec['sum_kr']=0;
		$rec['sum_ekr']=0;
		$rec['sum_kom']=0;
		$rec['item_id']=get_item_fid($it);
		$rec['item_name']=$it['name'];
		$rec['item_count']=1;
		$rec['item_type']=$it['type'];
		$rec['item_cost']=$it['cost'];
		$rec['item_dur']=$it['duration'];
		$rec['item_maxdur']=$it['maxdur'];
		$rec['item_ups']=$it['ups'];
		$rec['item_unic']=$it['unik'];
		$rec['item_incmagic']=$it['includemagicname'];
		$rec['item_incmagic_count']=$it['includemagicuses'];
		$rec['item_proto']=$it['prototype'];
		$rec['item_sowner']=($it['sowner']>0?1:0);
		$rec['item_incmagic_id']=$it['includemagic'];
		$rec['item_arsenal']='';
		add_to_new_delo($rec); //юзеру

		gift_from_item_break($owner,$it);

	}

	}
}


// Забираем арендовский шмот + если одета шмотка+ не берем тех кто в ритсалище или оружейке
$query = mysql_query("SELECT *  FROM oldbk.`inventory` WHERE  ( `prokat_do` > 0 AND `prokat_do` <= '".time()."' AND `prokat_idp` > 0 ) OR (  `dressed` = 1 AND `dategoden` > 0 AND `dategoden` <= '".time()."' )  ");
while($it = mysql_fetch_assoc($query)) {

	$tuser = check_users_city_data($it['owner']);

if (!((($tuser['room']>=197 and $tuser['room']<=199) or
  ($tuser['room']>=211 and $tuser['room']<240) or
  ($tuser['room']>240 and $tuser['room']<270) or
  ($tuser['room']>270 and $tuser['room']<290) ) or
  ($tuser['in_tower'] >0) ))
	{


	$delete_item=0; //нулимся
	if($it[add_pick]!='') {
		undress_img($it);
	}



	if ($tuser['id_city'] == 2) {
		$dbase = "angels.";
	}
	elseif ($tuser['id_city'] == 1) {
		$dbase = "avalon.";
	} else {
		$dbase = "oldbk.";
	}

	// если одет снимаем
	if ($it['dressed'] == 1) {
		if ($tuser[battle]==0) {
			//1. ищим слот
			switch($it['id']) {
				case $tuser['sergi']: $slot1 = 'sergi'; break;
				case $tuser['kulon']: $slot1 = 'kulon'; break;
				case $tuser['weap']:  $slot1 = 'weap'; break;
				case $tuser['bron']:  $slot1 = 'bron'; break;
				case $tuser['r1']:    $slot1 = 'r1'; break;
				case $tuser['r2']:    $slot1 = 'r2'; break;
				case $tuser['r3']:    $slot1 = 'r3'; break;
				case $tuser['helm']:  $slot1 = 'helm'; break;
				case $tuser['perchi']:$slot1 = 'perchi'; break;
				case $tuser['shit']:  $slot1 = 'shit'; break;
				case $tuser['boots']: $slot1 = 'boots'; break;
				case $tuser['m1']:    $slot1 = 'm1'; break;
				case $tuser['m2']:    $slot1 = 'm2'; break;
				case $tuser['m3']:    $slot1 = 'm3'; break;
				case $tuser['m4']:    $slot1 = 'm4'; break;
				case $tuser['m5']:    $slot1 = 'm5'; break;
				case $tuser['m6']:    $slot1 = 'm6'; break;
				case $tuser['m7']:    $slot1 = 'm7'; break;
				case $tuser['m8']:    $slot1 = 'm8'; break;
				case $tuser['m9']:    $slot1 = 'm9'; break;
				case $tuser['m10']:   $slot1 = 'm10'; break;
				case $tuser['m11']:   $slot1 = 'm11'; break;
				case $tuser['m12']:   $slot1 = 'm12'; break;
				case $tuser['m13']:   $slot1 = 'm13'; break;
				case $tuser['m14']:   $slot1 = 'm14'; break;
				case $tuser['m15']:   $slot1 = 'm15'; break;
				case $tuser['m16']:   $slot1 = 'm16'; break;
				case $tuser['m17']:   $slot1 = 'm17'; break;
				case $tuser['m18']:   $slot1 = 'm18'; break;
				case $tuser['m19']:   $slot1 = 'm19'; break;
				case $tuser['m20']:   $slot1 = 'm20'; break;
				case $tuser['nakidka']:   $slot1 = 'nakidka'; break;
				case $tuser['rubashka']:   $slot1 = 'rubashka'; break;
				case $tuser['runa1']:   $slot1 = 'runa1'; break;
				case $tuser['runa2']:   $slot1 = 'runa2'; break;
				case $tuser['runa3']:   $slot1 = 'runa3'; break;
			}

			//2. снимаем и обновляем перса


				$add_exp_bonus="";


						if ($it['prototype']==55510351)
						{
						//снимается артовая ёлка опыт -10%
						$add_exp_bonus=" u.expbonus=u.expbonus-'0.1', ";
						}

						if ($it['prototype']==55510352)
						{
						//снимается артовая ёлка опыт -10%
						$add_exp_bonus=" u.expbonus=u.expbonus-'0.3',u.rep_bonus=u.rep_bonus-'0.2', ";
						}

						if ($it['prototype']==410027) {
							$add_exp_bonus=" u.expbonus=u.expbonus-'0.1',u.rep_bonus=u.rep_bonus-'0.1', ";
						}
						if ($it['prototype']==410028) {
							$add_exp_bonus=" u.expbonus=u.expbonus-'0.3',u.rep_bonus=u.rep_bonus-'0.2', ";
						}






			if (mysql_query("UPDATE ".$dbase."`users` as u, oldbk.`inventory` as i SET u.{$slot1} = 0, i.dressed = 0,
				u.sila = u.sila - i.gsila,
				u.lovk = u.lovk - i.glovk,
				u.inta = u.inta - i.ginta,
				u.intel = u.intel - i.gintel,
				u.mudra = u.mudra - i.gmp,
				u.maxmana = (u.maxmana-i.gmp*10),
				u.maxhp = u.maxhp - i.ghp,
				u.noj = u.noj - i.gnoj,
				u.topor = u.topor - i.gtopor,
				u.dubina = u.dubina - i.gdubina,
				u.mec = u.mec - i.gmech,
				u.mfire = u.mfire - i.gfire,
				u.mwater = u.mwater - i.gwater,
				u.mair = u.mair - i.gair,
				u.mearth = u.mearth - i.gearth,
				u.mlight = u.mlight - i.glight,
				u.mgray = u.mgray - i.ggray, ".$add_exp_bonus."
				u.mdark = u.mdark - i.gdark
					WHERE i.id = u.{$slot1} AND i.dressed = 1 AND i.owner = {$tuser['id']} AND u.id = {$tuser['id']};"))
			{
				//удачно снята обновляем макс хп если надо
				mysql_query("UPDATE ".$dbase."`users` SET `hp` = `maxhp`, `fullhptime` = ".time()." WHERE  `hp` > `maxhp` AND `id` = '{$tuser['id']}' LIMIT 1;");

				//удаляем
				mysql_query("DELETE FROM oldbk.`inventory` WHERE `id` = '".$it['id']."' LIMIT 1;");
				$delete_item=1;
			}
		}
	} else {
		mysql_query("DELETE FROM oldbk.`inventory` WHERE `id` = '".$it['id']."' LIMIT 1;");
		$delete_item=1;
	}

	if ($delete_item==1) {
	         //пишем нужный лог если была удалена
	     	if ($it['prokat_idp'] > 0) {

		     	if ($it['idcity'] == 2) {
				mysql_query("UPDATE angels.`prokat` SET `kol`=`kol`+1  WHERE `idp` = '".$it['prokat_idp']."' LIMIT 1;");
			}
			else
			if ($it['idcity'] == 1) {
				mysql_query("UPDATE avalon.`prokat` SET `kol`=`kol`+1  WHERE `idp` = '".$it['prokat_idp']."' LIMIT 1;");
			} else {
				mysql_query("UPDATE oldbk.`prokat` SET `kol`=`kol`+1  WHERE `idp` = '".$it['prokat_idp']."' LIMIT 1;");
			}

			$rec['owner']=$tuser[id];
			$rec['owner_login']=$tuser[login];
			$rec['owner_balans_do']=$tuser[money];
			$rec['owner_balans_posle']=$tuser[money];
			$rec['target']=0;
			$rec['target_login']="Окончание проката";
			$rec['type']=186; //Окончание проката
			$rec['sum_kr']=0;
			$rec['sum_ekr']=0;
			$rec['sum_kom']=0;
			$rec['item_id']=get_item_fid($it);
			$rec['item_name']=$it['name'];
			$rec['item_count']=1;
			$rec['item_type']=$it['type'];
			$rec['item_cost']=$it['cost'];
			$rec['item_dur']=$it['duration'];
			$rec['item_maxdur']=$it['maxdur'];
			$rec['item_ups']=$it['ups'];
			$rec['item_unic']=$it['unik'];
			$rec['item_incmagic']=$it['includemagicname'];
			$rec['item_incmagic_count']=$it['includemagicuses'];
			$rec['item_proto']=$it['prototype'];
			$rec['item_sowner']=($it['sowner']>0?1:0);
			$rec['item_incmagic_id']=$it['includemagic'];
			$rec['item_arsenal']='';
			add_to_new_delo($rec);
		} else {
	     		$rec['owner']=$tuser[id];
			$rec['owner_login']=$tuser[login];
			$rec['owner_balans_do']=$tuser['money'];
			$rec['owner_balans_posle']=$tuser['money'];
			$rec['target']=0;
			$rec['target_login']='Срок годности предмета';
			$rec['type']=35;  //разрушение предмета
			$rec['sum_kr']=0;
			$rec['sum_ekr']=0;
			$rec['sum_kom']=0;
			$rec['item_id']=get_item_fid($it);
			$rec['item_name']=$it['name'];
			$rec['item_count']=1;
			$rec['item_type']=$it['type'];
			$rec['item_cost']=$it['cost'];
			$rec['item_dur']=$it['duration'];
			$rec['item_maxdur']=$it['maxdur'];
			$rec['item_ups']=$it['ups'];
			$rec['item_unic']=$it['unik'];
			$rec['item_incmagic']=$it['includemagicname'];
			$rec['item_incmagic_count']=$it['includemagicuses'];
			$rec['item_proto']=$it['prototype'];
			$rec['item_sowner']=($it['sowner']>0?1:0);
			$rec['item_incmagic_id']=$it['includemagic'];
			$rec['item_arsenal']='';
			add_to_new_delo($rec); //юзеру

			gift_from_item_break($tuser,$it);
		}
	}


	}
}

function DropToArsByTime($dbase,$cid) {
	/// возврат шмоток перса в арсенал по времени
	/// если чар не аристалище и находится не в бою
	/// если в бою заберет после боя в следущем проходе

	$query = mysql_query("SELECT * FROM oldbk.`inventory` WHERE (`prokat_do` > 0 AND `prokat_do` <= '".time()."' AND `arsenal_klan` !='') and owner not in (select id from ".$dbase.".users where ((room>=197 and room<=199) or (room>=211 and room<240) or (room>240 and room<270) or (room>270 and room<290)) and id_city=".$cid." and battle=0)");

	while($it = mysql_fetch_assoc($query)) {
		$res = DropItemm($it['id']); // снимаем шмотку если она висит
		if ($res) {
			$tuser=check_users_city_data($it['owner']);

			// удачно снята или небыла одета
			// возвращаем вещь в арс

			mysql_query('UPDATE oldbk.`inventory` SET owner = 22125, present = "", letter="", prokat_do=0, dressed=0 WHERE id = '.$it['id']);

			$log_text = 'Предмет:"'.$it[name].'" ['.$it[duration].'/'.$it[maxdur].'] [ups:'.$it['ups'].'/unik:'.$it['unik'].'/inc:'.$it['includemagicname'].'] вернулся в арсенал';

	                $rec['owner']=$tuser[id];
			$rec['owner_login']=$tuser[login];
			$rec['owner_balans_do']=$tuser['money'];
			$rec['owner_balans_posle']=$tuser['money'];
			$rec['target']=0;
			$rec['target_login']='Арсенал клана '.$tuser[klan];
			$rec['type']=187;  //разрешение предмета
			$rec['sum_kr']=0;
			$rec['sum_ekr']=0;
			$rec['sum_kom']=0;
			$rec['item_id']=get_item_fid($it);
			$rec['item_name']=$it['name'];
			$rec['item_count']=1;
			$rec['item_type']=$it['type'];
			$rec['item_cost']=$it['cost'];
			$rec['item_dur']=$it['duration'];
			$rec['item_maxdur']=$it['maxdur'];
			$rec['item_ups']=$it['ups'];
			$rec['item_arsenal']=$tuser[klan];
			$rec['item_unic']=$it['unik'];
			$rec['item_incmagic']=$it['includemagicname'];
			$rec['item_incmagic_count']=$it['includemagicuses'];
			$rec['item_proto']=$it['prototype'];
			$rec['item_sowner']=($it['sowner']>0?1:0);
			$rec['item_incmagic_id']=$it['includemagic'];
			$rec['item_arsenal']='';
			add_to_new_delo($rec); //юзеру

			//апдеет индексной таблицы
			mysql_query("UPDATE oldbk.clans_arsenal SET owner_current='0' WHERE id_inventory='{$it['id']}'");

			// записать в лог арсенала
			mysql_query("INSERT INTO oldbk.clans_arsenal_log (klan,pers,text,date) VALUES ('{$it['arsenal_klan']}','{$it['owner']}','{$log_text}','".time()."')");

		 }
	}
}

DropToArsByTime("oldbk",0);
DropToArsByTime("avalon",1);
DropToArsByTime("angels",2);

function DropFromArsByLeft()
	{
	/// система возврата шмоток из арсенала - сразу на два города

  	$ars= mysql_query("select * from oldbk.clans_arsenal_back;");


	while($row = mysql_fetch_array($ars))
		{
		//проверяем предмет
		$item=mysql_fetch_array(mysql_query("select * from oldbk.inventory where id='".$row[item_id]."' and owner='".$row[owner_current]."' "));
		if ($item[id] > 0)
		{
		//снимаем картинку если надо
			if($item[add_pick]!='') { undress_img($item); }
		//предмет есть
		$cur_user=mysql_fetch_array(mysql_query("select * from oldbk.users where id='{$row[owner_current]}'"));
		if ($cur_user[id_city]==1) { $cur_user=mysql_fetch_array(mysql_query("select * from avalon.users where id='{$row[owner_current]}'")); }
		elseif ($cur_user[id_city]==2) { $cur_user=mysql_fetch_array(mysql_query("select * from angels.users where id='{$row[owner_current]}'")); }

		if (($cur_user[id]>0) and  ($cur_user[battle]==0) )
			{
			//чар есть и не в бою
		if    (!( (($cur_user[room]>=197) and ($cur_user[room]<=199))
		     OR (($cur_user[room]>=211) and ($cur_user[room]<240))
		     OR (($cur_user[room]>240) and ($cur_user[room]<270))
		     OR (($cur_user[room]>270) and ($cur_user[room]<290)) ))
			{
			 //+не в оружейке и не в ристалище

				if ($item[dressed]>0)
				{
		 		// раздели если предмет одет
		  		undressall($cur_user[id],$cur_user[id_city]);
		  		}
		  	//возвращаем предмет - в арсенал полюбому
		        mysql_query("update oldbk.inventory set owner='22125', present='', letter='', prokat_do=0  where id='{$row['item_id']}' and owner='{$row['owner_current']}' and  dressed=0 ;");
		        if ( mysql_affected_rows() > 0)
		        	{
					// апдейтнули вещь
					$log_text = '"'.$cur_user['login'].'" вернул в арсенал при выходе из клана "'.$item[arsenal_klan].'" :"'.$item[name].'" ['.$item[duration].'/'.$item[maxdur].']';
					$rec['owner']=$cur_user['id'];
					$rec['owner_login']=$cur_user['login'];
					$rec['owner_balans_do']=$cur_user['money'];
					$rec['owner_balans_posle']=$cur_user['money'];
					$rec['target']=22125;
					$rec['target_login']='Арсенал';
					$rec['type']=188;
					$rec['sum_kr']=0;
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;
					$rec['item_id']=get_item_fid($item);
					$rec['item_name']=$item['name'];
					$rec['item_count']=1;
					$rec['item_type']=$item['type'];
					$rec['item_cost']=$item['cost'];
					$rec['item_dur']=$item['duration'];
					$rec['item_maxdur']=$item['maxdur'];
					$rec['item_ups']=$item['ups'];
					$rec['item_unic']=$item['unik'];
					$rec['item_incmagic']=$item['includemagicname'];
					$rec['item_incmagic_count']=$item['includemagicuses'];
					$rec['item_proto']=$item['prototype'];
					$rec['item_sowner']=($item['sowner']>0?1:0);
					$rec['item_incmagic_id']=$item['includemagic'];
					$rec['item_arsenal']=$item['arsenal_klan'];
					add_to_new_delo($rec); //юзеру

					mysql_query("UPDATE oldbk.clans_arsenal set owner_current=0 WHERE id_inventory='{$row[item_id]}'");
					mysql_query("INSERT INTO oldbk.clans_arsenal_log (klan,pers,text,date) VALUES ('{$item[arsenal_klan]}','{$row[owner_current]}','{$log_text}','".time()."')");

					addchp ('<font color=red>Внимание!</font> У Вас изъят предмет '.$item[name].' и передан в арсенал клана '.$item[arsenal_klan].' .','{[]}'.$cur_user['login'].'{[]}');
					// и удаляем таблицу возврата
	       				mysql_query("DELETE FROM oldbk.clans_arsenal_back where id='".$row[id]."'");
				}

			}
			}

		}
		else
		{
		//нет предмета
       		// запись была но дето пропала шмотка или испортилась или еще чето
       		// удаляем запись
	       		mysql_query("DELETE FROM oldbk.clans_arsenal_back where id='".$row[id]."'");
       		}

   	}
}

DropFromArsByLeft();


///возвраз заблудившихся вещей из арсенала людям которые уже не в этом клане
function ReturnItems()
{

$get_my_item=mysql_query("select ca.*, u.login, u.klan from clans_arsenal ca LEFT JOIN users u on owner_original=u.id where klan_name!=u.klan  and gift=0 ;");

while($row = mysql_fetch_array($get_my_item))
  {

  $get_test_item= mysql_fetch_array(mysql_query("select * from oldbk.inventory where id='{$row[id_inventory]}'"));

  if ($get_test_item[id]>0)
  {
  //предмет есть

   if ( $row[owner_current]==0)
  {
  //щас не у кого
   if ($row[login]!='')
   	{
   	//вернули хозяину
	    mysql_query("update oldbk.inventory set owner='{$row[owner_original]}', dressed=0, arsenal_klan='', arsenal_owner='' where id='{$row['id_inventory']}' ;");
	    //пишем в лог арсенала что предмет передан хозяину
	    $log_text = "Предмет {$get_test_item[name]} [{$get_test_item[duration]}/{$get_test_item[maxdur]}] передан владельцу {$row[login]}.";
	    mysql_query("INSERT INTO oldbk.clans_arsenal_log (klan,pers,text,date) VALUES ('{$row[klan_name]}','{$row[owner_original]}','{$log_text}','".time()."')");
	    //удаляем запись в арсе
	    mysql_query("DELETE from oldbk.clans_arsenal  WHERE id_inventory='{$row['id_inventory']}'");
	 }
	 else
	 {
	 //уже нет такого чара
	 //ставим ее делитеру
	    mysql_query("update oldbk.inventory set owner='446'  where id='{$row['id_inventory']}' ;");
	    //удаляем запись в арсе
	    mysql_query("DELETE from oldbk.clans_arsenal  WHERE id_inventory='{$row['id_inventory']}'");
	 }
   }
    else
    	{
   //предмет юзается надо вернуть в арсенал
   //пишем в таб. возврата
    	mysql_query("INSERT INTO `oldbk`.`clans_arsenal_back` SET `item_id`='{$row['id_inventory']}',`owner_current`='{$row[owner_current]}',`owner_original`='{$row[owner_original]}';");

    	}



    }
    else
    {
    //нет предмета
    	    //удаляем запись в арсе
	    mysql_query("DELETE from oldbk.clans_arsenal  WHERE id_inventory='{$row['id_inventory']}'");
    }

  }


}

ReturnItems();
//Арендная лавка

function MyDie($txt = "") {
	if (strlen($txt)) echo $txt."\n";
	lockDestroy("cron_item_break_job");
	die();
}

function DropItemm($itemid) {
	$q = mysql_query('SELECT * FROM oldbk.`inventory` WHERE id = '.$itemid);
	if (mysql_num_rows($q) == 0) {
		echo "Dropitem: no inventory\n";
		return FALSE;
	}

	$it = mysql_fetch_assoc($q);

	if($it['add_pick'] != '') {
       		undress_img($it);
	}

	$tuser = check_users_city_data($it['owner']);
		if ($tuser['id_city'] == 2) {
			$dbase = "angels.";
		} elseif ($tuser['id_city'] == 1) {
			$dbase = "avalon.";
		} else {
			$dbase = "oldbk.";
		}

	if  ( ($tuser[room] >=197 and $tuser[room]<=199)
		or ($tuser[room]>=211 and $tuser[room]<240)
		or ($tuser[room]>240  and $tuser[room]<270)
		or ($tuser[room]>270  and $tuser[room]<290))
		{
			echo "Dropitem: no drop in restal: ".$tuser['id']."\n";
			return false;
		}

	if ($it['dressed'] == 1) {

		if ($tuser === FALSE) {
			echo "Dropitem: no user found\n";
			return false;
		}


		if ($tuser['battle'] == 0) {
			// ищем слот
			switch($it['id']) {
				case $tuser['sergi']: $slot1 = 'sergi'; break;
				case $tuser['kulon']: $slot1 = 'kulon'; break;
				case $tuser['weap']:  $slot1 = 'weap'; break;
				case $tuser['bron']:  $slot1 = 'bron'; break;
				case $tuser['r1']:    $slot1 = 'r1'; break;
				case $tuser['r2']:    $slot1 = 'r2'; break;
				case $tuser['r3']:    $slot1 = 'r3'; break;
				case $tuser['helm']:  $slot1 = 'helm'; break;
				case $tuser['perchi']:$slot1 = 'perchi'; break;
				case $tuser['shit']:  $slot1 = 'shit'; break;
				case $tuser['boots']: $slot1 = 'boots'; break;
				case $tuser['m1']:    $slot1 = 'm1'; break;
				case $tuser['m2']:    $slot1 = 'm2'; break;
				case $tuser['m3']:    $slot1 = 'm3'; break;
				case $tuser['m4']:    $slot1 = 'm4'; break;
				case $tuser['m5']:    $slot1 = 'm5'; break;
				case $tuser['m6']:    $slot1 = 'm6'; break;
				case $tuser['m7']:    $slot1 = 'm7'; break;
				case $tuser['m8']:    $slot1 = 'm8'; break;
				case $tuser['m9']:    $slot1 = 'm9'; break;
				case $tuser['m10']:   $slot1 = 'm10'; break;
				case $tuser['m11']:   $slot1 = 'm11'; break;
				case $tuser['m12']:   $slot1 = 'm12'; break;
				case $tuser['m13']:   $slot1 = 'm13'; break;
				case $tuser['m14']:   $slot1 = 'm14'; break;
				case $tuser['m15']:   $slot1 = 'm15'; break;
				case $tuser['m16']:   $slot1 = 'm16'; break;
				case $tuser['m17']:   $slot1 = 'm17'; break;
				case $tuser['m18']:   $slot1 = 'm18'; break;
				case $tuser['m19']:   $slot1 = 'm19'; break;
				case $tuser['m20']:   $slot1 = 'm20'; break;
				case $tuser['nakidka']:   $slot1 = 'nakidka'; break;
				case $tuser['rubashka']:   $slot1 = 'rubashka'; break;
			}
			$q = mysql_query('
				UPDATE '.$dbase.'users as u, `inventory` as i
					SET u.'.$slot1.' = 0,
					i.dressed = 0,
					u.sila = u.sila - i.gsila,
					u.lovk = u.lovk - i.glovk,
					u.inta = u.inta - i.ginta,
					u.intel = u.intel - i.gintel,
					u.maxhp = u.maxhp - i.ghp,
					u.mudra = u.mudra - i.gmp,
					u.maxmana = (u.maxmana-i.gmp*10),
					u.noj = u.noj - i.gnoj,
					u.topor = u.topor - i.gtopor,
					u.dubina = u.dubina - i.gdubina,
					u.mec = u.mec - i.gmech,
					u.mfire = u.mfire - i.gfire,
					u.mwater = u.mwater - i.gwater,
					u.mair = u.mair - i.gair,
					u.mearth = u.mearth - i.gearth,
					u.mlight = u.mlight - i.glight,
					u.mgray = u.mgray - i.ggray,
					u.mdark = u.mdark - i.gdark
   						WHERE i.id = u.'.$slot1.' AND i.dressed = 1 AND i.owner = '.$tuser['id'].' AND u.id = '.$tuser['id']
			);
			if ($q) {
				// обновляем хп
				mysql_query('UPDATE '.$dbase.'users SET `hp` = `maxhp`, `fullhptime` = '.time().' WHERE  `hp` > `maxhp` AND `id` = '.$tuser['id']);
				return TRUE;
			} else {
				echo "Dropitem: user undressed error\n";
				return false;
			}
		} else {
			echo "Dropitem: item in battle\n";
			return FALSE;
		}
	} else {
		return TRUE;
	}
	echo "Dropitem: func end\n";

	return FALSE;
}

function CronRentalShopEndTime($dbase) {
	$rentalshopid = 449;

	mysql_query('START TRANSACTION') or mydie();

	// выбираем вещи у которых закончился срок аренды и возвращаем их обратно в прокатную лавку с мессагой для владельца
	$q2i = mysql_query('SELECT * FROM '.$dbase.'.`rentalshop` WHERE endtime < '.time().' AND tempowner <> 0 AND endtime <> 0 FOR UPDATE') or mydie(mysqL_error().":".__LINE__);

	while($item = mysql_fetch_assoc($q2i)) {
		$res = DropItemm($item['itemid']); // снимаем шмотку если она висит
		if ($res) {
			// удачно снята или небыла одета
			// возвращаем вещь в лавку
			mysql_query('UPDATE oldbk.`inventory` SET owner = '.$rentalshopid.', present = "", letter="", dressed=0 WHERE id = '.$item['itemid']) or mydie(mysqL_error().":".__LINE__);

			// удаляем временного владельца и обнуляем счётчик
			mysql_query('UPDATE '.$dbase.'.`rentalshop` SET tempowner = 0, endtime = 0 WHERE id = '.$item['id']) or mydie(mysqL_error().":".__LINE__);

			// находим временного хозяина
			$tempowner = check_users_city_data($item['tempowner']) or mydie(mysqL_error().":".__LINE__);

			// находим хозяина вещи
			$owner = check_users_city_data($item['owner']) or mydie(mysqL_error().":".__LINE__.":".$item['owner']);

			// получаем описание вещи
			$q = mysql_query('SELECT * FROM oldbk.`inventory` WHERE id = '.$item['itemid']) or mydie(mysqL_error().":".__LINE__);
			$itemd = mysql_fetch_assoc($q) or mydie(mysqL_error().":".__LINE__);

			// отправляем системку временному владельцу
			cron_send_mess($tempowner,'<font color=red>Внимание!</font> У вас закончился срок аренды: "'.htmlspecialchars($itemd['name'],ENT_QUOTES).'". Вещь возвращена в арендную лавку.');

		        //new_delo
			$rec = array();
 			$rec['owner']=$tempowner[id];
			$rec['owner_login']=$tempowner[login];
			$rec['owner_balans_do']=$tempowner['money'];
			$rec['owner_balans_posle']=$tempowner['money'];
			$rec['target']=$rentalshopid;
			$rec['target_login']="арендная лавка";
			$rec['type']=218;
			$rec['item_id']=get_item_fid($itemd);
			$rec['item_name']=$itemd['name'];
			$rec['item_count']=1;
			$rec['item_type']=$itemd['type'];
			$rec['item_cost']=$itemd['cost'];
			$rec['item_dur']=$itemd['duration'];
			$rec['item_maxdur']=$itemd['maxdur'];
			$rec['item_ups']=$itemd['ups'];
			$rec['item_unic']=$itemd['unik'];
			$rec['item_incmagic']=$itemd['includemagicname'];
			$rec['item_incmagic_count']=$itemd['includemagicuses'];
			$rec['item_proto']=$itemd['prototype'];
			$rec['item_sowner']=($itemd['sowner']>0?1:0);
			$rec['item_incmagic_id']=$itemd['includemagic'];
			$rec['item_arsenal']='';
			$rec['add_info'] = "";
			add_to_new_delo($rec) or mydie(mysqL_error().":".__LINE__);

			// отправляем системку хозяину что вещь вернулась в лавку
			cron_send_mess($owner,'<font color=red>Внимание!</font> Ваша вещь была возвращена в арендную лавку: "'.htmlspecialchars($itemd['name'],ENT_QUOTES).' ['.$itemd['duration'].'/'.$itemd['maxdur'].']"');
		}
	}
	mysql_query('COMMIT') or mydie();
}


function CronRentalShopMaxEndTime($dbase) {
	mysql_query('START TRANSACTION') or mydie();

	// выбираем вещи у которых закончился срок максимальной аренды и они не находятся ни у кого в аренде
	$q2i = mysql_query('SELECT * FROM '.$dbase.'.`rentalshop` WHERE maxendtime < '.time().' AND tempowner = 0 AND endtime = 0 FOR UPDATE') or mydie(mysqL_error().":".__LINE__);

	while($item = mysql_fetch_assoc($q2i)) {
		// получаем описание вещи
		$q = mysql_query('SELECT * FROM oldbk.`inventory` WHERE id = '.$item['itemid']) or mydie(mysqL_error().":".__LINE__);
		$itemd = mysql_fetch_assoc($q) or mydie(mysqL_error().":".__LINE__);

		// находим хозяина вещи
		$owner = check_users_city_data($item['owner']) or mydie(mysqL_error().":".__LINE__);

	        //new_delo
		$rec = array();
		$rec['owner']=$owner['id'];
		$rec['owner_login']=$owner['login'];
		$rec['target']=$rentalshopid;
		$rec['target_login']="арендная лавка";
		$rec['owner_balans_do']=$owner['money'];
		$rec['owner_balans_posle']=$owner['money'];
		$rec['type']=219; //возвращена вещь из арендной лавке хозяину
		$rec['item_id']=get_item_fid($itemd);
		$rec['item_name']=$itemd['name'];
		$rec['item_count']=1;
		$rec['item_type']=$itemd['type'];
		$rec['item_cost']=$itemd['cost'];
		$rec['item_dur']=$itemd['duration'];
		$rec['item_maxdur']=$itemd['maxdur'];
		$rec['item_ups']=$itemd['ups'];
		$rec['item_unic']=$itemd['unik'];
		$rec['item_incmagic']=$itemd['includemagicname'];
		$rec['item_incmagic_count']=$itemd['includemagicuses'];
		$rec['item_proto']=$itemd['prototype'];
		$rec['item_sowner']=($itemd['sowner']>0?1:0);
		$rec['item_incmagic_id']=$itemd['includemagic'];
		$rec['item_arsenal']='';
		$rec['add_info'] = "";
		add_to_new_delo($rec); //юзеру

		// возвращаем вещь владельцу
		mysql_query('UPDATE oldbk.`inventory` SET owner = '.$owner['id'].' WHERE id = '.$item['itemid']) or mydie(mysqL_error().":".__LINE__);

		// удаляем запись в лавке
		mysql_query('DELETE FROM '.$dbase.'.`rentalshop` WHERE id = '.$item['id']) or mydie(mysqL_error().":".__LINE__);
		cron_send_mess($owner,'<font color=red>Внимание!</font> Ваша вещь была возвращена вам из арендной лавки: "'.htmlspecialchars($itemd['name'],ENT_QUOTES).' ['.$itemd['duration'].'/'.$itemd['maxdur'].']"');
	}
	mysql_query('COMMIT') or mydie(mysqL_error().":".__LINE__);
}
//возврат шмоток из комка


	$data = mysql_query("SELECT i.*,u.login,u.money,u.id as uid, u.id_city as uid_city, u.room, u.odate,u.login FROM oldbk.`comission_indexes` ci
		left join oldbk.`inventory` i
		on i.id=ci.id_item
		left join oldbk.`users` u
		on ci.owner = u.id
		WHERE ci.timer<".time()." AND ci.timer>0");
	if(mysql_num_rows($data)>0)
	{
		$ids='';
		while($row = mysql_fetch_array($data))
		{
			if($row[id]>0)
			{
				echo "UPDATE oldbk.`inventory` SET setsale=0 WHERE id='{$row['id']}' AND owner='{$row['owner']}'; <br>";
				mysql_query("UPDATE oldbk.`inventory` SET setsale=0 WHERE id='{$row['id']}' AND owner='{$row['owner']}';");
				$rec['owner']=$row['uid'];
				$rec['owner_login']=$row['login'];
				$rec['owner_balans_do']=$row['money'];
				$rec['owner_balans_posle']=$row['money'];
				$rec['target']=0;
				$rec['target_login']='Комок';
				$rec['type']=121; //забрал из комка
				$rec['sum_kr']=0;
				$rec['sum_ekr']=0;
				$rec['sum_kom']=0;
				$rec['item_id']=get_item_fid($row);
				$rec['item_name']=$row['name'];
				$rec['item_count']=1;
				$rec['item_type']=$row['type'];
				$rec['item_cost']=$row['cost'];
				$rec['item_dur']=$row['duration'];
				$rec['item_maxdur']=$row['maxdur'];
				$rec['item_ups']=$row['ups'];
				$rec['item_unic']=$row['unik'];
				$rec['item_incmagic_id']=$row['includemagic'];
				$rec['item_ecost']=$row['ecost'];
				$rec['item_proto']=$row['prototype'];
				$rec['item_sowner']=0;
				$rec['item_incmagic']=$row['includemagicname'];
				$rec['item_incmagic_count']=$row['includemagicuses'];
				$rec['item_arsenal']='';
				add_to_new_delo($rec); //юзеру
				//print_r($rec);
				$ids.=$row[id].',';

				$owner[id]=$row[uid];
				$owner[id_city]=$row[uid_city];
				$owner[login]=$row[login];
				$owner[room]=$row[room];
				$owner[odate]=$row[odate];
				/*
				echo '<br>';
				print_r($owner);
				echo '<br>';*/
				if($row[id]<146684)
				{
					$first_txt=", а так же 1 кр.  в связи с реконструкцией Комиссионного магазина";
					mysql_query("UPDATE ".$db_city[$owner[id_city]]."`users` set money=money+1 WHERE id ='".$owner[id]."' LIMIT 1;");
				}
				else
				{
					$first_txt=".";
				}

				cron_send_mess($owner,'<font color=red>Внимание!</font> Вам возвращена вещь: "'.htmlspecialchars($row['name'],ENT_QUOTES).' ['.$row['duration'].'/'.$row['maxdur'].']"'.$first_txt);
			}

		}

		$ids=substr($ids,0,-1);
		//echo "DELETE FROM oldbk.`comission_indexes` WHERE id_item in (".$ids."); <br>";
		if ($ids!='')
			{
			mysql_query("DELETE FROM oldbk.`comission_indexes` WHERE id_item in (".$ids.");");
			}
	}
	else
	{
	//	echo 'Нет шмоток';
	}





CronRentalShopEndTime("oldbk");
CronRentalShopEndTime("avalon");
CronRentalShopEndTime("angels");

CronRentalShopMaxEndTime("oldbk");
CronRentalShopMaxEndTime("avalon");
CronRentalShopMaxEndTime("angels");

lockDestroy("cron_item_break_job");
?>
