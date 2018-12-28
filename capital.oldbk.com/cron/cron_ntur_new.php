#!/usr/bin/php
<?php
ini_set('display_errors','On');
Error_Reporting(E_ALL);

include "/www/capitalcity.oldbk.com/cron/init.php";
require_once('/www/capitalcity.oldbk.com/functions.zayavka.php'); // функи заявок


if( !lockCreate("cron_ntur_job") ) {
    exit("Script already running.");
}

function mydie($txt) {
 echo time().":".$txt."\n";
 lockDestroy("cron_ntur_job");
 die();
}




function count_zfree($arr)
	{
	$ret=0;
		for ($i=1;$i<=16;$i++)
		{
		$pm="o".$i;
		 if ( $arr[$pm]>0 )
		 	{
			$ret++;
		 	}
		}
	return (16-$ret);
	}

function make_new_autozay($tur_type)
{
	mysql_query("INSERT INTO `ntur_users` SET `ntype`='{$tur_type}',`stat`=0,`nazva`='<auto>OLDBK',`pas`='',`koment`='Автозаявка' ;");
	if (mysql_affected_rows() >0)
		{
		return true;
		}

return false;
}


function make_rand_div($tarray) //функа рандомного деления накоманды - ждет масив и делит этот масив рандомно на один двумерный
{
 shuffle($tarray);
 $team_arr=array();
 $team=1;

        foreach($tarray as $k=>$v)
 	{
 	 if ($team==1)
 	 	{
 	 	 $team_arr[1][]=$v;
 	 	 $team=2;
 	 	}
 	 	else
 	 	{
 	 	$team_arr[2][]=$v;
 	 	 $team=1;
 	 	}
 	}
return $team_arr;
}


function insert_items($nom,$wc,$oc,$ty) //номер тела , кол- оружия, кол. предметов, тип турнира - раскидывает нужное кол. шмота - сразу на два тела
{
global $owners_by_team ; // масив тел

//ид для пушек с 1000018 по 1000026 для уровня 4
$item_wep[4]=array(1000018,1000019,1000020,1000021,1000022,1000023,1000024,1000025,1000026);
$item_wep[8]=array(1000267,1000268,1000269,1000270,1000271,1000272,1000273,1000274,1000275,1000276,1000277);

$slots_arr=array(1,2,4,5,5,5,8,9,10,11,28); // масив слотов - кроме пушек

/////////////////////////////////////////////////////////////////////////////////////////////////////////////
$item_arr[4][1]=array(1000001,1000002,1000003,1000004,1000005);
$item_arr[4][2]=array(1000006,1000007,1000008,1000009,1000010,1000011,1000012,1000013,1000014,1000015,1000016,1000017);
$item_arr[4][4]=array(1000027,1000028,1000029,1000030,1000031,1000032,1000033,1000034,1000035,1000036,1000037,1000038,1000039,1000040);
$item_arr[4][5]=array(1000041,1000042,1000043,1000044,1000045,1000046,1000047,1000048,1000049,1000050,1000051,1000052);
$item_arr[4][8]=array(1000053,1000054,1000055,1000056,1000057,1000058,1000059,1000060,1000061,1000062);
$item_arr[4][9]=array(1000063,1000064,1000065,1000066,1000067,1000068,1000069,1000070);
$item_arr[4][10]=array(1000071,1000072,1000073,1000074);
$item_arr[4][11]=array(1000075,1000076,1000077,1000078,1000079,1000080,1000081,1000082);
$item_arr[4][28]=array(1000083,1000084,1000085,1000086,1000087,1000088,1000089,1000090,1000091,1000092);
/////------------//////-----------//////////------------///////////////
$item_arr[8][1]=array(1000231,1000232,1000233,1000234,1000235,1000236,1000237,1000238,1000239,1000240,1000241,1000242,1000243,1000244,1000245,1000246,1000247,1000248);
$item_arr[8][2]=array(1000249,1000250,1000251,1000252,1000253,1000254,1000255,1000256,1000257,1000258,1000259,1000260,1000261,1000262,1000263,1000264,1000265,1000266);
$item_arr[8][4]=array(1000278,1000279,1000280,1000281,1000282,1000283,1000284,1000285,1000286,1000287,1000288,1000289,1000290,1000291,1000292);
$item_arr[8][5]=array(1000296,1000297,1000298,1000299,1000300,1000301,1000302,1000303,1000304,1000305,1000306,1000307);
$item_arr[8][8]=array(1000201,1000202,1000203,1000204,1000205,1000206);
$item_arr[8][9]=array(1000207,1000208,1000209,1000210,1000211,1000212,1000213,1000214,1000215,1000216,1000217,1000218,1000219);
$item_arr[8][10]=array(1000220,1000221,1000222,1000223,1000224,1000225,1000226);
$item_arr[8][11]=array(1000227,1000228,1000229,1000230);
$item_arr[8][28]=array(1000293,1000294,1000295);
///-----------------------------------------------------------------------------------------------
$item_weps=$item_wep[$ty]; // назначаем масив по типу  турнира
$item_arrs=$item_arr[$ty]; // назначаем масив по типу  турнира


	// 1. раздаем пушки
	  for($i=1;$i <=$wc;$i++) //цикл - кол. пушек
	  {

	  	shuffle($item_weps); $rand_wep=mt_rand(0,(count($item_weps)-1) );
	  	$rand_wep=$item_weps[$rand_wep];
	  	if ($rand_wep>0)
	  		{

	  		$item=mysql_fetch_array(mysql_query("select * from oldbk.inventory where id={$rand_wep}")) or mydie(mysql_error().":".__LINE__);
	  		if ($item[id]>0)
	  		    {
	  			for($tt=1;$tt <=2;$tt++) 	// кидаем зеркально каждой команде
	  			{
	  			$mowner=$owners_by_team[$tt][$nom];

	  			mysql_query("INSERT INTO `oldbk`.`inventory` SET `name`='{$item[name]}',`duration`='{$item[duration]}',`maxdur`='{$item[maxdur]}',`cost`='{$item[cost]}',`owner`='{$mowner}',
	  			`nlevel`='{$item[nlevel]}',`nsila`='{$item[nsila]}',`nlovk`='{$item[nlovk]}',`ninta`='{$item[ninta]}',`nvinos`='{$item[nvinos]}',`nintel`='{$item[nintel]}',`nmudra`='{$item[nmudra]}',
	  			`nnoj`='{$item[nnoj]}',`ntopor`='{$item[ntopor]}',`ndubina`='{$item[ndubina]}',`nmech`='{$item[nmech]}',`nalign`='{$item[nalign]}',`minu`='{$item[minu]}',`maxu`='{$item[maxu]}',
	  			`gsila`='{$item[gsila]}',`glovk`='{$item[glovk]}',`ginta`='{$item[ginta]}',`gintel`='{$item[gintel]}',`ghp`='{$item[ghp]}',`mfkrit`='{$item[mfkrit]}',`mfakrit`='{$item[mfakrit]}',`mfuvorot`='{$item[mfuvorot]}',`mfauvorot`='{$item[mfauvorot]}',
	  			`gnoj`='{$item[gnoj]}',`gtopor`='{$item[gtopor]}',`gdubina`='{$item[gdubina]}',`gmech`='{$item[gmech]}',`img`='{$item[img]}',
	  			`text`='{$item[text]}',`dressed`='0',`bron1`='{$item[bron1]}',`bron2`='{$item[bron2]}',`bron3`='{$item[bron3]}',`bron4`='{$item[bron4]}',`dategoden`='{$item[dategoden]}',`magic`='{$item[magic]}',`type`='{$item[type]}',
	  			`present`='{$item[present]}',`sharped`='{$item[sharped]}',`massa`='{$item[massa]}',`goden`='{$item[goden]}',`needident`='{$item[needident]}',`nfire`='{$item[nfire]}',`nwater`='{$item[nwater]}',`nair`='{$item[nair]}',`nearth`='{$item[nearth]}',`nlight`='{$item[nlight]}',
	  			`ngray`='{$item[ngray]}',`ndark`='{$item[ndark]}',`gfire`='{$item[gfire]}',`gwater`='{$item[gwater]}',`gair`='{$item[gair]}',`gearth`='{$item[gearth]}',`glight`='{$item[glight]}',
	  			`ggray`='{$item[ggray]}',`gdark`='{$item[gdark]}',`letter`='{$item[letter]}',`isrep`='{$item[isrep]}',`prototype`='{$item[prototype]}',`otdel`='{$item[otdel]}',`bs`='{$item[bs]}',`gmp`='{$item[gmp]}',
	  			`includemagic`='{$item[includemagic]}',`includemagicdex`='{$item[includemagicdex]}',`includemagicmax`='{$item[includemagicmax]}',`includemagicname`='{$item[includemagicname]}',`includemagicuses`='{$item[includemagicuses]}',
	  			`includemagiccost`='{$item[includemagiccost]}',`includemagicekrcost`='{$item[includemagicekrcost]}',
	  			`gmeshok`='{$item[gmeshok]}',`stbonus`='{$item[stbonus]}',`upfree`='{$item[upfree]}',`ups`='{$item[ups]}',`mfbonus`='{$item[mfbonus]}',`mffree`='{$item[mffree]}',`type3_updated`='{$item[type3_updated]}',`bs_owner`='3',
	  			`nsex`='{$item[nsex]}',`add_time`='{$item[add_time]}',`repcost`='{$item[repcost]}',`up_level`='{$item[up_level]}',`ecost`='{$item[ecost]}',`group`='{$item[group]}',`unik`='{$item[unik]}',
	  			`sowner`='{$mowner}',`idcity`=0,`ab_mf`='{$item[ab_mf]}',`ab_bron`='{$item[ab_bron]}',`ab_uron`='{$item[ab_uron]}';") or mydie(mysql_error().":".__LINE__);
	  			}
	  		    }
	  		}
	  }

	  //перемешиваем слоты
	  shuffle($slots_arr);

	  //указатель на слот
	  $uk_slot=0;

	  //2, раздаем остальное
	  for ($i=1;$i<=$oc;$i++) // цикл кол. остальных предметов
	  {

	 		if (!($slots_arr[$uk_slot] > 0))
		   	{
		   	//если  добрались до конца скидываем указатель в 0 и заново мешаем масив слотов
	   		  $uk_slot=0;
		   	  shuffle($slots_arr);
		   	}

		   //спрашиваем шмотку
		$mas_items=$item_arrs[$slots_arr[$uk_slot]]; //масив для нужного слота
		shuffle($mas_items); // перемешиваем
		$get_itm_id=$mas_items[0];

		$item=mysql_fetch_array(mysql_query("select * from oldbk.inventory where id={$get_itm_id}"  )) or mydie(mysql_error().":".__LINE__);

		if ($item[id]>0)
	  	 {
	  		for($tt=1;$tt <=2;$tt++) 	// кидаем зеркально каждой команде
	  		{
				$mowner=$owners_by_team[$tt][$nom];
	  			mysql_query("INSERT INTO `oldbk`.`inventory` SET `name`='{$item[name]}',`duration`='{$item[duration]}',`maxdur`='{$item[maxdur]}',`cost`='{$item[cost]}',`owner`='{$mowner}',
	  			`nlevel`='{$item[nlevel]}',`nsila`='{$item[nsila]}',`nlovk`='{$item[nlovk]}',`ninta`='{$item[ninta]}',`nvinos`='{$item[nvinos]}',`nintel`='{$item[nintel]}',`nmudra`='{$item[nmudra]}',
	  			`nnoj`='{$item[nnoj]}',`ntopor`='{$item[ntopor]}',`ndubina`='{$item[ndubina]}',`nmech`='{$item[nmech]}',`nalign`='{$item[nalign]}',`minu`='{$item[minu]}',`maxu`='{$item[maxu]}',
	  			`gsila`='{$item[gsila]}',`glovk`='{$item[glovk]}',`ginta`='{$item[ginta]}',`gintel`='{$item[gintel]}',`ghp`='{$item[ghp]}',`mfkrit`='{$item[mfkrit]}',`mfakrit`='{$item[mfakrit]}',`mfuvorot`='{$item[mfuvorot]}',`mfauvorot`='{$item[mfauvorot]}',
	  			`gnoj`='{$item[gnoj]}',`gtopor`='{$item[gtopor]}',`gdubina`='{$item[gdubina]}',`gmech`='{$item[gmech]}',`img`='{$item[img]}',
	  			`text`='{$item[text]}',`dressed`='0',`bron1`='{$item[bron1]}',`bron2`='{$item[bron2]}',`bron3`='{$item[bron3]}',`bron4`='{$item[bron4]}',`dategoden`='{$item[dategoden]}',`magic`='{$item[magic]}',`type`='{$item[type]}',
	  			`present`='{$item[present]}',`sharped`='{$item[sharped]}',`massa`='{$item[massa]}',`goden`='{$item[goden]}',`needident`='{$item[needident]}',`nfire`='{$item[nfire]}',`nwater`='{$item[nwater]}',`nair`='{$item[nair]}',`nearth`='{$item[nearth]}',`nlight`='{$item[nlight]}',
	  			`ngray`='{$item[ngray]}',`ndark`='{$item[ndark]}',`gfire`='{$item[gfire]}',`gwater`='{$item[gwater]}',`gair`='{$item[gair]}',`gearth`='{$item[gearth]}',`glight`='{$item[glight]}',
	  			`ggray`='{$item[ggray]}',`gdark`='{$item[gdark]}',`letter`='{$item[letter]}',`isrep`='{$item[isrep]}',`prototype`='{$item[prototype]}',`otdel`='{$item[otdel]}',`bs`='{$item[bs]}',`gmp`='{$item[gmp]}',
	  			`includemagic`='{$item[includemagic]}',`includemagicdex`='{$item[includemagicdex]}',`includemagicmax`='{$item[includemagicmax]}',`includemagicname`='{$item[includemagicname]}',`includemagicuses`='{$item[includemagicuses]}',
	  			`includemagiccost`='{$item[includemagiccost]}',`includemagicekrcost`='{$item[includemagicekrcost]}',
	  			`gmeshok`='{$item[gmeshok]}',`stbonus`='{$item[stbonus]}',`upfree`='{$item[upfree]}',`ups`='{$item[ups]}',`mfbonus`='{$item[mfbonus]}',`mffree`='{$item[mffree]}',`type3_updated`='{$item[type3_updated]}',`bs_owner`='3',
	  			`nsex`='{$item[nsex]}',`add_time`='{$item[add_time]}',`repcost`='{$item[repcost]}',`up_level`='{$item[up_level]}',`ecost`='{$item[ecost]}',`group`='{$item[group]}',`unik`='{$item[unik]}',
	  			`sowner`='{$mowner}',`idcity`=0,`ab_mf`='{$item[ab_mf]}',`ab_bron`='{$item[ab_bron]}',`ab_uron`='{$item[ab_uron]}';") or mydie(mysql_error().":".__LINE__);
	  		}
	  	}
	  $uk_slot++;
	  }

}

//ищем заявки для старта
$start_room=80000;
$log_type[4]=304;
$log_type[8]=308;


	$get_zt=mysql_query("select * from ntur_users where stat=1");
	 if (mysql_num_rows($get_zt)  > 0)
   	{
   	//обработка старта  - 1- статус
		while ($zt_row = mysql_fetch_array($get_zt))
		{
		//меняем статус
		mysql_query("UPDATE ntur_users set stat=2, faza=1, stat_time=NOW() where id='{$zt_row[id]}' and stat=1;");
		if (mysql_affected_rows() >0)
		{
		echo "Старт турнира {$zt_row[id]} <br> \n";
		//пишем в лог - начало турнира
		$owners_rows=array();

			//готовим поля
			for ($i=1;$i<=16;$i++)
			{
			$pm="o".$i;
			 if ( $zt_row[$pm]>0 )
			 	{
				 $owners_rows[]=$zt_row[$pm];
		 		}
			}


		//1. ставим всем интовер 3 шоб не могли ничего делать пока мы их готовим
		mysql_query_100('UPDATE users SET in_tower = 3, room='.($start_room+$zt_row[id]).' WHERE id  in ('.implode(",",$owners_rows).') ') or mydie(mysql_error().":".__LINE__);

		//2.  удаляем старые реальные характеристики - ВСЕМ
		mysql_query_100('DELETE FROM `ntur_realchars` WHERE `owner`  in ('.implode(",",$owners_rows).') ') or mydie(mysql_error().":".__LINE__);


		//3.  убиваем всем бонусы -ВСЕМ
 		mysql_query_100('DELETE FROM users_bonus where owner in ('.implode(",",$owners_rows).') ') or mydie(mysql_error().":".__LINE__);

 		//3.а. убиваем навсякий - остатки шмоток
 		mysql_query_100('DELETE FROM oldbk.inventory where bs_owner=3 and owner in ('.implode(",",$owners_rows).') ') or mydie(mysql_error().":".__LINE__);

		//ищем у кого елки 55510351
		$get_elk=mysql_query("select owner, prototype  from oldbk.inventory  where dressed=1 and bs_owner=0 and (prototype=55510351)  and  owner in (".implode(",",$owners_rows).") group by owner") or mydie(mysql_error().":".__LINE__);
		while ($bonusrow = mysql_fetch_array($get_elk))
			{
			$ownbonusexp[$bonusrow['owner']]=" `expbonus`=`expbonus`-0.1 , ";
			}


		//ищем у кого елки 55510352
		$get_elk=mysql_query("select owner, prototype  from oldbk.inventory  where dressed=1 and bs_owner=0 and prototype=55510352  and  owner in (".implode(",",$owners_rows).") group by owner") or mydie(mysql_error().":".__LINE__);
		while ($bonusrow = mysql_fetch_array($get_elk))
			{
			$ownbonusexp[$bonusrow['owner']]=" `expbonus`=`expbonus`-0.3 ,`rep_bonus`=`rep_bonus`-0.2 , ";
			}

		//ищем у кого елки 410028
		$get_elk=mysql_query("select owner, prototype  from oldbk.inventory  where dressed=1 and bs_owner=0 and prototype=410027  and  owner in (".implode(",",$owners_rows).") group by owner") or mydie(mysql_error().":".__LINE__);
		while ($bonusrow = mysql_fetch_array($get_elk))
			{
			$ownbonusexp[$bonusrow['owner']]=" `expbonus`=`expbonus`-0.1 ,`rep_bonus`=`rep_bonus`-0.1 , ";
			}

		//ищем у кого елки 410028
		$get_elk=mysql_query("select owner, prototype  from oldbk.inventory  where dressed=1 and bs_owner=0 and prototype=410028  and  owner in (".implode(",",$owners_rows).") group by owner") or mydie(mysql_error().":".__LINE__);
		while ($bonusrow = mysql_fetch_array($get_elk))
			{
			$ownbonusexp[$bonusrow['owner']]=" `expbonus`=`expbonus`-0.3 ,`rep_bonus`=`rep_bonus`-0.2 , ";
			}


		// 4. берем информацию о том что дают надетые шмотки у людей из заявки
			$get_drop_stats=mysql_query("select owner, sum(gsila) as gsila, sum(glovk) as glovk, sum(ginta) as ginta, sum(gintel) as gintel ,  sum(gnoj) as gnoj, sum(gtopor) as gtopor, sum(gdubina) as gdubina, sum(gmech) as gmech , sum(gfire) as gfire, sum(gwater) as gwater, sum(gair)  as gair, sum(gearth) as gearth, sum(glight) as glight, sum(ggray) as ggray, sum(gdark) as gdark ,sum(gmp) as gmp from oldbk.inventory  where dressed=1 and bs_owner=0 and owner in (".implode(",",$owners_rows).") group by owner") or mydie(mysql_error().":".__LINE__);
			while ($stats_row = mysql_fetch_array($get_drop_stats))
			{
			//загружаем для дальнейшей работы
			$owner_drop_stats[$stats_row[owner]]=$stats_row;
			}
		echo "загрузка шмот.мф N:{$zt_row[id]}.\n";
		print_r($owner_drop_stats);
		echo "------------------------------------\n";
		//4.а. - снимаем флаг одето   -ВСЕМ сразу
		mysql_query_100('UPDATE oldbk.inventory  SET dressed=0  WHERE owner  in ('.implode(",",$owners_rows).') and dressed=1 ') or mydie(mysql_error().":".__LINE__);

		//5. берем данные их эффектов - на интелект или еще какие другие эффекты которые дают статы
		 $get_drop_eff=mysql_query("select * from effects where type=826 and owner in (".implode(",",$owners_rows).") group by owner ") or mydie(mysql_error().":".__LINE__);

		 while ($stats_row = mysql_fetch_array($get_drop_eff))
			{
			//загружаем для дальнейшей работы
			$owner_drop_eff[$stats_row[owner]]=$stats_row;
			}
		//5.a - удаляем эти эффекты - 791,792,793,794,795 - бафы книг
		mysql_query_100("DELETE FROM  effects where type in (826,791,792,793,794,795) and owner in (".implode(",",$owners_rows).")  ") or mydie(mysql_error().":".__LINE__);

			//5.б- Загружаем профили -дефалтовые -ВСЕХ
			$get_load_prof=mysql_query("SELECT * FROM `ntur_profile` WHERE `owner` in (".implode(",",$owners_rows).")   AND `def` = 1") or mydie(mysql_error().":".__LINE__);


			 while ($prof_row = mysql_fetch_array($get_load_prof))
			 {
			 $owners_prof[$prof_row[owner]]=$prof_row;
			 }


			//5 в. разбиваем  людей на тимы и делем шмот
			$owners_by_team=make_rand_div($owners_rows) ;
			$owners_t1=$owners_by_team[1];
			$owners_t2=$owners_by_team[2];

			//6. - загружаем данные чаров - ВСЕХ
			$get_all_users=mysql_query("SELECT * from users where  id  in (".implode(",",$owners_rows).")  ") or mydie(mysql_error().":".__LINE__);

			$all_logins_hist=array();

			echo "-=Запоминаем статы=- N:{$zt_row[id]}  \n";

			 while ($u = mysql_fetch_array($get_all_users))
			 {
			 //пишем в прогресс
			 mysql_query("INSERT INTO oldbk.users_progress set owner='{$u['id']}', ar270count=1 ON DUPLICATE KEY UPDATE ar270count=ar270count+1");



			 $all_logins_hist[]=make_html_login_battle($u);

			  if (in_array($u[id],$owners_t1)) // определяем в какой тиме будет текущий чел
			  	{
			  	$my_team=1;
			  	}
			  	else
			  	{
			  	$my_team=2;
			  	}

			 		//7. - далле сохраняем родные статы  с вычетом того что было запомненно  по каждому персу и взамен ставим ему из профиля или пустые
			 						// сила без бонуса

						// Сохраняем реальные статы в голом виде + все умения.
					mysql_query('INSERT INTO `ntur_realchars`
						(`owner`,`sila`,`lovk`,`inta`,`vinos`,`intel`,`mudra`,`stats`,`master`,`bpbonussila`,`bpbonushp`,`noj`,`mec`,`topor`,`dubina`,`mfire`,`mwater`,`mair`,`mearth`,`mlight`,`mgray`,`mdark`,`mana`)
						VALUES
						(
							"'.$u['id'].'",
							"'.($u['sila'] - $u['bpbonussila']-$owner_drop_stats[$u['id']][gsila]).'",
							"'.($u['lovk']-$owner_drop_stats[$u['id']][glovk]).'",
							"'.($u['inta']-$owner_drop_stats[$u['id']][ginta]).'",
							"'.$u['vinos'].'",
							"'.($u['intel']-$owner_drop_stats[$u['id']][gintel]-$owner_drop_eff[$u['id']][intel]).'",
							"'.($u['mudra']-$owner_drop_stats[$u['id']][gmp]).'",
							"'.$u['stats'].'",
							"'.$u['master'].'",
							"'.$u['bpbonussila'].'",
							"'.$u['bpbonushp'].'",
							"'.($u['noj']-$owner_drop_stats[$u['id']][gnoj]).'",
							"'.($u['mec']-$owner_drop_stats[$u['id']][gmech]).'",
							"'.($u['topor']-$owner_drop_stats[$u['id']][gtopor]).'",
							"'.($u['dubina']-$owner_drop_stats[$u['id']][gdubina]).'",
							"'.($u['mfire']-$owner_drop_stats[$u['id']][gfire]).'",
							"'.($u['mwater']-$owner_drop_stats[$u['id']][gwater]).'",
							"'.($u['mair']-$owner_drop_stats[$u['id']][gair]).'",
							"'.($u['mearth']-$owner_drop_stats[$u['id']][gearth]).'",
							"'.($u['mlight']-$owner_drop_stats[$u['id']][glight]).'",
							"'.($u['mgray']-$owner_drop_stats[$u['id']][ggray]).'",
							"'.($u['mdark']-$owner_drop_stats[$u['id']][gdark]).'",
							"'.$u['maxmana'].'"
						)'
						) or mydie(mysql_error().":".__LINE__);

					echo "========\n";
					echo 'INSERT INTO `ntur_realchars`
						(`owner`,`sila`,`lovk`,`inta`,`vinos`,`intel`,`mudra`,`stats`,`master`,`bpbonussila`,`bpbonushp`,`noj`,`mec`,`topor`,`dubina`,`mfire`,`mwater`,`mair`,`mearth`,`mlight`,`mgray`,`mdark`,`mana`)
						VALUES
						(
							"'.$u['id'].'",
							"'.($u['sila'] - $u['bpbonussila']-$owner_drop_stats[$u['id']][gsila]).'",
							"'.($u['lovk']-$owner_drop_stats[$u['id']][glovk]).'",
							"'.($u['inta']-$owner_drop_stats[$u['id']][ginta]).'",
							"'.$u['vinos'].'",
							"'.($u['intel']-$owner_drop_stats[$u['id']][gintel]-$owner_drop_eff[$u['id']][intel]).'",
							"'.($u['mudra']-$owner_drop_stats[$u['id']][gmp]).'",
							"'.$u['stats'].'",
							"'.$u['master'].'",
							"'.$u['bpbonussila'].'",
							"'.$u['bpbonushp'].'",
							"'.($u['noj']-$owner_drop_stats[$u['id']][gnoj]).'",
							"'.($u['mec']-$owner_drop_stats[$u['id']][gmech]).'",
							"'.($u['topor']-$owner_drop_stats[$u['id']][gtopor]).'",
							"'.($u['dubina']-$owner_drop_stats[$u['id']][gdubina]).'",
							"'.($u['mfire']-$owner_drop_stats[$u['id']][gfire]).'",
							"'.($u['mwater']-$owner_drop_stats[$u['id']][gwater]).'",
							"'.($u['mair']-$owner_drop_stats[$u['id']][gair]).'",
							"'.($u['mearth']-$owner_drop_stats[$u['id']][gearth]).'",
							"'.($u['mlight']-$owner_drop_stats[$u['id']][glight]).'",
							"'.($u['mgray']-$owner_drop_stats[$u['id']][ggray]).'",
							"'.($u['mdark']-$owner_drop_stats[$u['id']][gdark]).'",
							"'.$u['maxmana'].'"
						)' ;
					echo "==========\n";


				 //8, ставим профиль + прописываем battle_t сразу - шоб потом не ставить
				 // применяем профили
				$mas[4] = 5;
				$mas[8] = 9;

				 if  ($owners_prof[$u['id']][id]>0)
				 	{
				 	// есть профиль

					mysql_query_100('UPDATE `users` SET
							`sila` = "'.$owners_prof[$u['id']]['sila'].'",
							`lovk` = "'.$owners_prof[$u['id']]['lovk'].'",
							`inta` = "'.$owners_prof[$u['id']]['inta'].'",
							`vinos` = "'.$owners_prof[$u['id']]['vinos'].'",
							`intel` = "'.$owners_prof[$u['id']]['intel'].'",
							`mudra` = "'.$owners_prof[$u['id']]['mudra'].'",
							'.$ownbonusexp[$u['id']].'
							`sergi`=0,
							`kulon`=0,
							`perchi`=0,
							`weap`=0,
							`bron`=0,
							`r1`=0,
							`r2`=0,
							`r3`=0,
							`runa1`=0,
							`runa2`=0,
							`runa3`=0,
							`helm`=0,
							`shit`=0,
							`boots`=0,
							`m1`=0,
							`m2`=0,
							`m3`=0,
							`m4`=0,
							`m5`=0,
							`m6`=0,
							`m7`=0,
							`m8`=0,
							`m9`=0,
							`m10`=0,
							`m11`=0,
							`m12`=0,
							`m13`=0,
							`m14`=0,
							`m15`=0,
							`m16`=0,
							`m17`=0,
							`m18`=0,
							`m19`=0,
							`m20`=0,
							`nakidka`=0,
							`rubashka`=0,
							`stats` = 0,
							`noj` = 0,
							`mec` = 0,
							`topor` = 0,
							`dubina` = 0,
							`mfire` = 0,
							`mwater` = 0,
							`mair` = 0,
							`mearth` = 0,
							`mlight` = 0,
							`mgray` = 0,
							`mdark` = 0,
							`master` = "'.$mas[$zt_row[ntype]].'",
							`maxhp` = "'.($owners_prof[$u['id']]['vinos']*6).'",
							`hp` = "'.($owners_prof[$u['id']]['vinos']*6).'",
							`bpbonussila` = 0,
							`mana` = 0,
							`maxmana` = 0,
							`bpbonushp` = 0,
							`battle_t` = '.$my_team.'
						WHERE `id` = '.$u['id']
					) or mydie(mysql_error().":".__LINE__);


				 	}
				 	else
				 	{
				 	//нет профиля - просто сброс статов
				 	//ставим пустые - взависимости от типа турнира
						$asts[4]=34;
						$avin[4]=7;
						$ahp[4]=42;

						$asts[8]=78;
						$avin[8]=11;
						$ahp[8]=66;

						$vinos=$avin[$zt_row[ntype]];
						$hp=$ahp[$zt_row[ntype]];
						$stats=$asts[$zt_row[ntype]];

							mysql_query_100('UPDATE `users` SET
							`sila` = "3",
							`lovk` = "3",
							`inta` = "3",
							`vinos` = "'.$vinos.'",
							`intel` = "0",
							`mudra` = "0",
							`stats` = "'.$stats.'",
							`sergi`=0,
							`kulon`=0,
							`perchi`=0,
							`weap`=0,
							`bron`=0,
							`r1`=0,
							`r2`=0,
							`r3`=0,
							`runa1`=0,
							`runa2`=0,
							`runa3`=0,
							`helm`=0,
							`shit`=0,
							`boots`=0,
							`m1`=0,
							`m2`=0,
							`m3`=0,
							`m4`=0,
							`m5`=0,
							`m6`=0,
							`m7`=0,
							`m8`=0,
							`m9`=0,
							`m10`=0,
							`m11`=0,
							`m12`=0,
							`m13`=0,
							`m14`=0,
							`m15`=0,
							`m16`=0,
							`m17`=0,
							`m18`=0,
							`m19`=0,
							`m20`=0,
							`nakidka`=0,
							`rubashka`=0,
							`noj` = 0,
							`mec` = 0,
							`topor` = 0,
							`dubina` = 0,
							`mfire` = 0,
							`mwater` = 0,
							`mair` = 0,
							`mearth` = 0,
							`mlight` = 0,
							`mgray` = 0,
							`mdark` = 0,
							`master` = "'.$mas[$zt_row[ntype]].'",
							`maxhp` = "'.$hp.'",
							'.$ownbonusexp[$u['id']].'
							`hp` = "'.$hp.'",
							`bpbonussila` = 0,
							`mana` = 0,
							`maxmana` = 0,
							`bpbonushp` = 0,
							`battle_t` = '.$my_team.'
						WHERE `id` = '.$u['id']
						) or mydie(mysql_error().":".__LINE__);

				 	}

			 }
			//////окончили установку всем профилей

				$logtext="<span class=date2>".date("d.m.y H:i")."</span> <b>Начало турнира, участвуют:".implode(",",$all_logins_hist)."<BR>";
			 	mysql_query("INSERT INTO  `ntur_logs` SET id={$zt_row[id]} , active=1, start_time='".time()."' , `logs`= '{$logtext}' , `type`='{$log_type[$zt_row[ntype]]}' ;");

			        foreach($owners_t1 as $k=>$v)
					{
					insert_items($k,2,7,$zt_row[ntype]); //первый разброс : бой 16 на 16 = 1 пуха + 2 шмотки на тело


					}

			if($owners_rows) {
				try {
					$UserList = \components\models\User::whereIn('id', $owners_rows)->get()->toArray();
					foreach ($UserList as $_user_) {
						$UserObj = new \components\models\User($_user_);
						/** @var \components\Component\Quests\Quest $QuestComponent */
						$QuestComponent = $app->quest->setUser($UserObj)->get();

						$Checker = new \components\Component\Quests\check\CheckerEvent();
						$Checker->event_type = \components\Component\Quests\pocket\questTask\EventTask::EVENT_RIST_DO;
						if (($Item = $QuestComponent->isNeed($Checker)) !== false) {
							$QuestComponent->taskUp($Item);
							unset($Item);
						}

						unset($UserObj);
						unset($QuestComponent);
						unset($Checker);
					}
				} catch (Exception $ex) {
					\components\Helper\FileHelper::writeException($ex, 'cron_ntur_new_start');
				}
			}

		/////////////////////////////
		}
		}
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////
// Ищем турниры для 2й статус
		/*
		2-й круг, где 16 человека готовятся - время на одевание 3 минуты
		3-й круг, где 8 человек готовятся - время на одевание 4 минуты
		4-й круг, где 4 человека готовятся - время на одевание 5 минуты
		5-й круг, где 2 человека готовятся - время на одевание 5 минут
		*/


	$get_zt=mysql_query("select * from ntur_users where stat=2 and ( (UNIX_TIMESTAMP(stat_time)+180<=UNIX_TIMESTAMP() and faza=1) OR (UNIX_TIMESTAMP(stat_time)+240<=UNIX_TIMESTAMP() and faza=2) OR (UNIX_TIMESTAMP(stat_time)+300<=UNIX_TIMESTAMP() and faza>2)  ) and battle=0 "); //которые в статусе 2 и время их уже подошло и уже не в бою
	 if (mysql_num_rows($get_zt)  > 0)
   	{
  	   	echo "Обработка статуса 2 <br>\n";
   	//обработка  - создание боя
		while ($zt_row = mysql_fetch_array($get_zt))
		{
		echo "N:{$zt_row[id]} \n";
		$owners_rows=array();
			//готовим поля
			for ($i=1;$i<=16;$i++)
			{
			$pm="o".$i;
			 if ( $zt_row[$pm]>0 )
			 	{
				 $owners_rows[]=$zt_row[$pm];
		 		}
			}
		// загружаем чаров  для создания боя - которые в турнире
				$to_battle_id['team1'] = array();
				$to_battle_id['team2'] = array();

				$to_battle_login['team1'] = array();
				$to_battle_login['team2'] = array();

				$to_battle_hist['team1'] = array();
				$to_battle_hist['team2'] = array();

				$inf_log_t1=array();
				$inf_log_t2=array();


		$get_all_users=mysql_query("SELECT * from users where  id  in (".implode(",",$owners_rows).")  and in_tower=3 and room=".($start_room+$zt_row[id])." and id_grup='{$zt_row[id]}'   ") or mydie(mysql_error().":".__LINE__);

		echo "SELECT * from users where  id  in (".implode(",",$owners_rows).")  and in_tower=3 and room=".($start_room+$zt_row[id])." and id_grup='{$zt_row[id]}'   \n";

		$tt1=0;
		$tt2=0;
		 while ($u = mysql_fetch_array($get_all_users))
			 {
			 //заносим данные согласно командам
				if ($u[battle_t]==1)
				{
						$to_battle_id['team1'][] = $u[id];
               					$to_battle_hist['team1'][]=BNewHist($u); // koд - для хистори в battle
						$to_battle_login['team1'][]=make_login_battle($u); // просто логины чистые
						$inf_log_t1[]=make_html_login_battle($u);
					$tt1++;

				}
				else
				{
						$to_battle_id['team2'][] =  $u[id];
               					$to_battle_hist['team2'][]=BNewHist($u); // koд - для хистори в battle
						$to_battle_login['team2'][]=make_login_battle($u); // просто логины чистые
						$inf_log_t2[]=make_html_login_battle($u);
					$tt2++;
				}
			 }
		echo "Бой {$tt1} /  {$tt2} N:{$zt_row[id]} \n";
		print_r($to_battle_id);

		if ($tt1!=$tt2)
			{
			echo "Ошибка деления! N:{$zt_row[id]} \n";
			}
		///----------/-/-/-/-/--/-/-/-/--/-/-/-/-/-/-/-/-/-/-/-/
		// создаем бой 16 на 16
		$chaos_flag=2; //авто удар
		$time=time();

		$bat_type[4]=304; // оставляем два типа боев
		$bat_type[8]=308;
		$mkbattype=$bat_type[$zt_row[ntype]];

		$bat_com[4]='Одиночные сражения (новичков)';
		$bat_com[8]='Одиночные сражения';
		$mkbatcom=$bat_com[$zt_row[ntype]];

			// создаем лог
			$rrc="<b>".implode(",",$to_battle_login['team1'])."</b> и <b>".implode(",",$to_battle_login['team2'])."</b>"; //для текста в чат
			$hist1=implode("",$to_battle_hist['team1']);//собираем хистори для T1 для battle
			$hist2=implode("",$to_battle_hist['team2']);//собираем хистори для T2 для battle


			mysql_query("INSERT INTO `battle` ( `t1`, `t2` , `t1hist` , `t2hist`, `coment`,`timeout`,`type`,`status`,`to1`,`to2`,`blood`,`CHAOS`, `nomagic` )  VALUES	(  '".implode(";",$to_battle_id['team1'])."' , '".implode(";",$to_battle_id['team2'])."' , '{$hist1}', '{$hist2}' ,  '{$mkbatcom}','3','{$mkbattype}','0','".$time."','".$time."','0','".$chaos_flag."' , 1 )");

			if (mysql_affected_rows()>0)
			{
			//бой создалcя
				$battle_id = mysql_insert_id();
				//обновляем людей  кидем в бой - баттл_т не трогаем

				mysql_query_100("UPDATE users SET `battle` ={$battle_id}  WHERE   id  in (".implode(",",$owners_rows).")  and in_tower=3 and room=".($start_room+$zt_row[id])." and id_grup='{$zt_row[id]}'  ");
				//лог боя
				addlog($battle_id,"!:S:".time().":".$hist1.":".$hist2."\n");
				//отправляем групповую системку
				addch_group('<font color=red>Внимание!</font> Ваш бой начался! <BR>\'; top.frames[\'main\'].location=\'fbattle.php\'; var z = \'   ', array_merge($to_battle_id['team1'],$to_battle_id['team2']));
				//системка
				addch ("<a href=logs.php?log=".$battle_id." target=_blank>Поединок</a> между <B>".$rrc."</B> начался.   ",($start_room+$zt_row[id]),CITY_ID);

				//записываем в таблицу турнира ид боя - шоб невызывать и увеличиваем фазу +1
				mysql_query_100("UPDATE `ntur_users` SET `battle`='{$battle_id}' , `faza`=`faza`+1   WHERE `id`='{$zt_row[id]}' ");

				$log_str[0]='Бой 16/16';
				$log_str[1]='Бой 8/8';
				$log_str[2]='Бой 4/4';
				$log_str[3]='Бой 2/2';
				$log_str[4]='Бой 1/1';

				//print_r($inf_log_t1);	echo "<br>";	print_r($inf_log_t2);	echo "<br>";

				$logtext="<span class=date2>".date("d.m.y H:i")."</span> <b>{$log_str[$zt_row[faza]]}:</b>".implode(",",$inf_log_t1)." <b>против</b>  ".implode(",",$inf_log_t2)." <a href=\"/logs.php?log={$battle_id}\" target=\"_blank\"> »» </a> <BR>";
			 	mysql_query_100("UPDATE  `ntur_logs` SET `logs`= CONCAT(`logs`,'{$logtext}')  WHERE id={$zt_row[id]}  ;");

			}
			else
			{
				mydie(mysql_error().":".__LINE__);
			}

		}
	}

//////////////////////////////
//// обработка 3-го статуса
	$get_zt=mysql_query("select * from ntur_users where stat=3 and battle=0 "); //которые в статусе 3 и не в бою
	 if (mysql_num_rows($get_zt)  > 0)
   	{
   	echo "Обработка статуса 3 <br>\n";
   	///состояние после боя - када  надо разделить тимы по новой и раздать шмотки заново
   			while ($zt_row = mysql_fetch_array($get_zt))
			{
			echo "N:{$zt_row[id]} \n";
			$owners_rows=array();
			//готовим поля
				for ($i=1;$i<=16;$i++)
				{
				$pm="o".$i;
				 if ( $zt_row[$pm]>0 )
				 	{
					 $owners_rows[]=$zt_row[$pm];
		 			}
				}

				//грузим актцальных персов
				$ownres=array();
				$all_own=array();

				$get_all_users=mysql_query("SELECT * from users where  id  in (".implode(",",$owners_rows).")  and in_tower=3 and room=".($start_room+$zt_row[id])." and id_grup='{$zt_row[id]}'   ") or mydie(mysql_error().":".__LINE__);
				 while ($u = mysql_fetch_array($get_all_users))
				 {
				  $ownres[$u[id]]=$u;
				  $all_own[]=$u[id];
				 }

				//разбиваем  людей на тимы
				$owners_by_team=array();
				$owners_by_team=make_rand_div($all_own) ;
				$owners_t1=$owners_by_team[1];
				$owners_t2=$owners_by_team[2];

				echo "N:{$zt_row[id]} \n";
				echo "T1:";
				print_r($owners_t1);
				echo "\n";
				echo "T2:";
				print_r($owners_t2);

				//обновим всех  юзеров проставив им новые команды
				mysql_query_100("UPDATE users set battle_t=1 where id in  (".implode(",",$owners_t1).")  and in_tower=3 and room=".($start_room+$zt_row[id])." and id_grup='{$zt_row[id]}'  ") or mydie(mysql_error().":".__LINE__);
				mysql_query_100("UPDATE users set battle_t=2 where id in  (".implode(",",$owners_t2).")  and in_tower=3 and room=".($start_room+$zt_row[id])." and id_grup='{$zt_row[id]}'  ") or mydie(mysql_error().":".__LINE__);

				// делем шмот раздаем шмот
				  foreach($owners_t1 as $k=>$v)
					{
					$fzzw[1]=2;  $fzzo[1]=7; // 8 на 8 = 2 пухи + 7 шмоток на тело
					$fzzw[2]=3;  $fzzo[2]=15; // 4 на 4 = 3 пухи + 15 шмоток на тело
					$fzzw[3]=4;  $fzzo[3]=20; // 2 на 2 = 4 пухи + 20 шмоток на тело
					$fzzw[4]=5;  $fzzo[4]=31; //1 на 1 = 5 пух + 31 шмотка на тело

					$fzw=$fzzw[$zt_row[faza]]; //  сколько надо раздать пушек в текущей фазе
					$fzo=$fzzo[$zt_row[faza]]; // сколько надо раздать остального в текущей фазе
					//echo " {$k} / {$fzw} / {$fzo} /  {$zt_row[ntype]} <br>";
					insert_items($k,$fzw,$fzo,$zt_row[ntype]);
					}


			mysql_query_100("UPDATE ntur_users set stat=2, stat_time=NOW() where id='{$zt_row[id]}' and stat=3 ") or mydie(mysql_error().":".__LINE__);	 // ставим статус 2 - для подготовки к бою
			}
		//пишем системку - о получени шмоток
		addch_group('<font color=red>Внимание!</font> Вы получили турнирное обмундирование, оно находится у вас в инвентаре!', $all_own);
   	}

///////////////////////////////////////////////
// Ищем турниры для 0й статус и 30 мин

	$get_zt=mysql_query("select * from ntur_users where stat=0 and UNIX_TIMESTAMP(mktime)+1800<=UNIX_TIMESTAMP()  "); //которые в статусе 2 и время их уже подошло и уже не в бою
	 if (mysql_num_rows($get_zt)  > 0)
   	{

   	$bmoney[4]=1;
   	$bmoney[8]=20;

  	   	echo "Обработка статуса 0 - выгон <br>\n";
   	//обработка  - создание боя
		while ($zt_row = mysql_fetch_array($get_zt))
		{
		echo "N:{$zt_row[id]} \n";
		//удаляем такой турнир
		mysql_query_100("UPDATE ntur_users set stat=5  where id='{$zt_row[id]}' and stat=0 ") or mydie(mysql_error().":".__LINE__);	 // ставим статус 5 - отмена - для подготовки к бою

		$get_all_users=mysql_query("SELECT * from users where  room=270  and id_grup='{$zt_row[id]}'   ") or mydie(mysql_error().":".__LINE__);
		 while ($u = mysql_fetch_array($get_all_users))
			 {
			$cpr=$bmoney[$zt_row[ntype]];
				mysql_query_100("UPDATE users set  money=money+'{$cpr}' , room=270, id_grup=0 where id='{$u[id]}' LIMIT 1 ;");//вернем ставку


					//пишем в дело
					$rec['owner']=$u[id];
					$rec['owner_login']=$u[login];
					$rec['owner_balans_do']=$u[money];
					$rec['owner_balans_posle']=($u[money]+$cpr);
					$rec['target']=0;
					$rec['target_login']='ристалище';
					$rec['type']=367;//возврат
					$rec['sum_kr']=$cpr;
					$rec['sum_ekr']=0;
					$rec['sum_kom']=0;
					$rec['item_id']='';
					$rec['item_name']='';
					$rec['item_count']='';
					$rec['item_type']='';
					$rec['item_cost']='';
					$rec['item_dur']='';
					$rec['item_maxdur']='';
					$rec['item_ups']=0;
					$rec['item_unic']=0;
					$rec['item_incmagic']='';
					$rec['item_incmagic_count']='';
					$rec['item_arsenal']='';
					add_to_new_delo($rec);


		    	     	addchp ('<font color=red>Внимание!</font> Турнир не может начаться по причине: мало участников. Вам возвращено '.$cpr.' кр. ','{[]}'.$u[login].'{[]}',$u[room],$u[id_city]);

			 }

		}
	}

	//автозаявки
		$get_next_az=mysql_fetch_array(mysql_query("select *, UNIX_TIMESTAMP(`opentime`) uopentime from ntur_next limit 1;")); // берем время когда будет создана след. заявка
		if ( $get_next_az['id']>0)
				{
				echo "След. турнир для {$get_next_az['turtype']} будет открыт в {$get_next_az['opentime']} \n";
					$lvlmin=8;
					$lvlmax=21;
					$dftype=8;

						/* откл. 4-7 ур.
						if ($get_next_az['turtype']==4)
							{
							$lvlmin=4;
							$lvlmax=7;
							$dftype=4;
							}
						*/


				echo "есть данные \n";
					/*if ( ($get_next_az['sysmessage']==0) and (($get_next_az['uopentime']-time())<=3600) and (($get_next_az['uopentime']-time())>=3480) )
						{
						//нет первой системки  и осталось час или меньше
						$TEXT='<font color=red>Автозаявка на <b>турнир</b> в <a href=http://capitalcity.oldbk.com/help/r270.html target=_blank>Одиночных сражениях</a> на <a href=http://oldbk.com/encicl/rastilka.html target=_blank>Ристалище</a> будет открыта через <b>1 час.</b></font>';
						addch2levels($TEXT,$lvlmin,$lvlmax,0);
						echo $TEXT."\n";
						mysql_query("UPDATE `oldbk`.`ntur_next` SET `sysmessage`=1 WHERE `id`='{$get_next_az['id']}';");
						}
					else
						if ( ($get_next_az['sysmessage']==1) and (($get_next_az['uopentime']-time())<=1800) and (($get_next_az['uopentime']-time())>=1680) )
						{
						//нет 2й системки  и осталось 30 m или меньше
						$TEXT='<font color=red>Автозаявка на <b>турнир</b> в <a href=http://capitalcity.oldbk.com/help/r270.html target=_blank>Одиночных сражениях</a> на <a href=http://oldbk.com/encicl/rastilka.html target=_blank>Ристалище</a> будет открыта через <b>30 минут</b>. Не забудьте установить игровой профиль для турнира!</font>';
						addch2levels($TEXT,$lvlmin,$lvlmax,0);
						echo $TEXT."\n";
						mysql_query("UPDATE `oldbk`.`ntur_next` SET `sysmessage`=2 WHERE `id`='{$get_next_az['id']}';");
						}
					else*/
						if ( ($get_next_az['sysmessage']==0) and (($get_next_az['uopentime']-time())<=900) )
						{
						//нет 3й системки  и осталось 5 m или меньше
						$TEXT='<font color=red>Автозаявка на <b>турнир</b> в <a href=http://capitalcity.oldbk.com/help/r270.html target=_blank>Одиночных сражениях</a> на <a href=http://oldbk.com/encicl/rastilka.html target=_blank>Ристалище</a> будет открыта через <b>15 минут</b>. Не забудьте установить игровой профиль для турнира!</font>';
						addch2levels($TEXT,$lvlmin,$lvlmax,0);
						echo $TEXT."\n";
						mysql_query("UPDATE `oldbk`.`ntur_next` SET `sysmessage`=3 WHERE `id`='{$get_next_az['id']}';");
						}

					if (($get_next_az['uopentime']-time())<=0)
					{
					//пора создать турнир
						if (make_new_autozay($dftype))
							{
							echo "создали авто заявку \n";

							$TEXT='<font color=red>Автозаявка на <b>турнир</b> в <a href=http://capitalcity.oldbk.com/help/r270.html target=_blank>Одиночных сражениях</a> на <a href=http://oldbk.com/encicl/rastilka.html target=_blank>Ристалище</a> - открыта! Свободных мест: <b>16</b>. Займите свое место на пьедестале почета и получите награду в репутации!</font>';
							addch2levels($TEXT,$lvlmin,$lvlmax,0);
							echo $TEXT."\n";

							//следующая  09:00, 15:00 и 21:00
							$new_mktime=$get_next_az['uopentime']+10800; //+3 ч.
							$nextdt=date("Y-m-d H:i:00",$new_mktime);
							echo "$nextdt";


							mysql_query("INSERT INTO `oldbk`.`ntur_next` SET `opentime`='{$nextdt}',`sysmessage`=0, `turtype`='{$dftype}' ;");
							if (mysql_affected_rows() >0)
								{
								//удаляем старую
								mysql_query("DELETE FROM `ntur_next` WHERE `id`='{$get_next_az['id']}' ");
								}

							}
					}

				}

			//системки после создания
					$get_open=mysql_fetch_array(mysql_query("select *, UNIX_TIMESTAMP(mktime) as umktime from ntur_users where stat=0 and nazva='<auto>OLDBK'"));
					if ($get_open['id']>0)
					{
echo "D/";
						$lvlmin=8;
						$lvlmax=21;
						$dftype=8;

						if ($get_open['ntype']==4)
							{
							$lvlmin=4;
							$lvlmax=7;
							$dftype=4;
							}

echo (time()-$get_open['umktime']);
echo "/";
echo time();
echo "/";
echo $get_open['umktime'];

						if (($get_open['sysmcount']==0) and ((time()-$get_open['umktime'])>=300)  )
							{
							$freepl=count_zfree($get_open);
							$TEXT='<font color=red>Автозаявка на <b>турнир</b> в <a href=http://capitalcity.oldbk.com/help/r270.html target=_blank>Одиночных сражениях</a> на <a href=http://oldbk.com/encicl/rastilka.html target=_blank>Ристалище</a> - открыта! Свободных мест: <b>'.$freepl.'</b>. Займите свое место на пьедестале почета и получите награду в репутации!</font>';
							addch2levels($TEXT,$lvlmin,$lvlmax,0);
							echo $TEXT."\n";
							mysql_query("UPDATE `oldbk`.`ntur_users` SET `sysmcount`=1 WHERE `id`='{$get_open['id']}';");
							}
						elseif (($get_open['sysmcount']==1) and ((time()-$get_open['umktime'])>=600)  )
							{
							$freepl=count_zfree($get_open);
							$TEXT='<font color=red>Автозаявка на <b>турнир</b> в <a href=http://capitalcity.oldbk.com/help/r270.html target=_blank>Одиночных сражениях</a> на <a href=http://oldbk.com/encicl/rastilka.html target=_blank>Ристалище</a> - открыта! Свободных мест: <b>'.$freepl.'</b>.</font>';
							addch2levels($TEXT,$lvlmin,$lvlmax,0);
							echo $TEXT."\n";
							mysql_query("UPDATE `oldbk`.`ntur_users` SET `sysmcount`=2 WHERE `id`='{$get_open['id']}';");
							}
							else
							{
							echo "other!";
							}
					}




lockDestroy("cron_ntur_job");

?>
