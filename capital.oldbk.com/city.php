<?php
//компресия для инфы
///////////////////////////

    if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
    $miniBB_gzipper_encoding = 'x-gzip';
    }
    if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
    $miniBB_gzipper_encoding = 'gzip';
    }
    if (isset($miniBB_gzipper_encoding)) {
    ob_start();
    }
    function percent($a, $b) {
    $c = $b/$a*100;
    return $c;
    }
//////////////////////////////

	session_start();

	//ini_set('display_errors','On');
	if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
	include "connect.php";
	include "functions.php";
	include "map_config.php";


	include "action_days_config.php";
	include "ny_events.php";



	//отключение перевеса на случай волн хаоса
	$begin=$start_volna; //дабы не переписывыать все в сити, просто присваиваем переменные из конфига
	$end=$end_volna;//дабы не переписывыать все в сити, просто присваиваем переменные из конфига


	/////////////Времена года

	$VR_GODA=date("n");
	$ZIMA_array=array(12,1,2);
	$VESNA_array=array(3,4,5);
	$LETO_array=array(6,7,8);
	$OSEN_array=array(9,10,11);

	if (in_array($VR_GODA,$ZIMA_array)) {
		$ZIMA=true;
	} elseif (in_array($VR_GODA,$VESNA_array)) {
		$VESNA=true;
	} elseif (in_array($VR_GODA,$OSEN_array)) {
		$OSEN=true;
	} else {
		$LETO=true;
	}


	if ($user['battle'] != 0) { header('location: fbattle.php'); die(); }
	if ($user['in_tower'] == 1) { header('Location: towerin.php'); die(); }
	if ($user['in_tower'] == 15) { header('Location: dt.php'); die(); }
	if ($user['in_tower'] == 4) { header('Location: jail.php'); die(); }
	if ($user['in_tower'] >= 10) { header('Location: mini_a.php'); die(); }
	if ($user['zayavka']!= 0) {header('location: main.php'); die(); }
	if ($user['lab'] == 1) { header('Location: lab.php'); die(); }
	if ($user['lab'] == 2) { header('Location: lab2.php'); die(); }
	if ($user['lab'] == 3) { header('Location: lab3.php'); die(); }
	if ($user['lab'] == 4) { header('Location: lab4.php'); die(); }

	if ($user['room'] == 999) { header('Location: ruines_start.php'); die(); }
	if ($user['room'] == 90) { header('Location: lord.php'); die(); }
	if (($user['room'] >=1000) and ($user['room'] < 10000))  { header('Location: ruines.php'); die(); }

	if ($user['room'] == 49999) { header('Location: outcity.php'); die(); }
	if ($user['room'] == 49998) { header('Location: aoutcity.php'); die(); }
	if ($user['room'] >= 50000 && $user['room'] <= 53600) { header('Location: map.php'); die(); }
	reset($map_locations);
	while(list($k,$v) = each($map_locations)) {
		if ($v['room'] == $user['room']) { header('Location: '.$v['redirect']); die(); }
	}

	if ($user['room'] == 61000) { header('Location: station.php'); die(); }
	if (($user['room'] > 61000) and ($user['room'] < 62000))  { header('Location: station_go.php'); die(); }
	if ($user['room'] == 70000) { header('Location: castles.php'); die(); }
	if ($user['room'] > 70000 && $user['room'] < 71000) { header('Location: castles_pre.php'); die(); }
	if ($user['room'] > 71000 && $user['room'] < 72000) { header('Location: castles_inside.php'); die(); }
	if ($user['room'] == 72001) { header('Location: castles_tur.php'); die(); }
	if ($user['room'] == 72002) { header('Location: castles_osada.php'); die(); }
	if ($user['room'] >= 400 && $user['room'] <= 450) { header('Location: /action/street/clan/location'); die(); }

	if ($user['room'] == 10000) { header('Location: dt_start.php'); die(); }
	if (($user['room'] > 10000) and ($user['room'] < 11000))  { header('Location: dt.php'); die(); }


	if ($user['room'] == 46)  { header('Location: prokat.php'); die(); }
	if ($user['room'] == 47)  { header('Location: rentalshop.php'); die(); }
	if ($user['room'] == 57)  { header('Location: war_list.php'); die(); }
	if ($user['room'] == 76)  { header('Location: class_armory.php'); die(); }
	if ($user['room'] == 71)  { header('Location: auction.php'); die(); }

	if ($user['room'] == 70)  { header('Location: pawnbroker.php'); die(); }
	if ($user['in_tower'] ==3)   { header('Location: restal270.php'); die(); }
	if (($user['room'] >= 200)AND($user['room'] <= 300))  { header('Location: restal.php'); die(); }
	if (($user['room'] >= 91)AND($user['room'] <= 97))  { header('Location: craft.php'); die(); }

	if ($user['room'] == 80)  { header('Location: garb.php'); die(); }
	if ($user['room'] == 197 || $user['room'] == 199)  { header('Location: armory.php'); die(); }
	if ($user['room'] == 198)  { header('Location: castles_armory.php'); die(); }
	if ($user['labzay'] != 0) { header('Location: startlab.php?err=1'); die(); }
	if ($user['room']==60 or $user['bpzay']==-1) 	{ header('Location: bplace.php'); die();}

	header("Cache-Control: no-cache");
        $meshok=get_meshok();

	if  ($user['id']==14897)
	{
	$snegovik_start=time()-1;
	}

if(!$_SESSION['beginer_quest'][none])
{
	$last_q=check_last_quest(4);
	if($last_q)
	{
		quest_check_type_4($last_q);
		//проверяем квесты на хар-и
	}

	$last_q=check_last_quest(2);
	if($last_q)
	{
		//ECHO '2  TESTSTSTE EDF';
		quest_check_type_2($last_q);
		//проверяем квесты на хар-и
	}
	if(!$_SESSION['beginer_quest'][none])
	{
		$last_q=check_last_quest(5);
		if($last_q)
		{
			quest_check_type_5($last_q);
			//проверяем квесты на хар-и
		}
	}

}

        if(time()>$begin && time()<$end)
	{
		//волны хаоса. не проверяем перевес, чтоб добавться до фонтана.
		$eff_txt=array();

		$sql="SELECT * FROM `effects` WHERE `owner` = '".$user['id']."' AND (`type`=10) AND time>".time().";";
		$d=mysql_query($sql);


		if(mysql_num_rows($d)>0)
		{

			while($r=mysql_fetch_array($d))
			{

				$st=$r['time']-time();
				$txt='секунд.';
				if($st<0)
				{
					$st=0;
				}
				else
				if($st>60)
				{
					$st=$st/60;
					$txt='минут.';
				}
				$eff_txt[10]="Вы не можете передвигаться еще ".$st." ".$txt.". ";

				$eff = true;
			}
		}
	}
	else
	{
		$eff_txt=array();


	//	$d = mysql_fetch_array(mysql_query("SELECT sum(`massa`) FROM oldbk.`inventory` WHERE `owner` = '{$user['id']}' AND `dressed` = 0 AND `setsale` = 0 ; "));
		$my_massa=0;
		$q = mysql_query("SELECT IFNULL(sum(`massa`),0) as massa , setsale, dressed  FROM oldbk.inventory WHERE `owner` = '{$user['id']}'   GROUP by setsale,dressed ");
		while ($row = mysql_fetch_array($q))
			{
			if (($row['setsale'] ==0 )  AND   ($row['dressed'] ==0 )  )
				{
				$my_massa+=$row['massa'];
				}
			}


		$p=mysql_query("SELECT * FROM `effects` WHERE `owner` = '".$user['id']."' AND `type` in (10,11,12,13,14) AND time>".time().";");
		$array_trv=array();
		if(mysql_num_rows($p)>0)
		{

			while($r=mysql_fetch_array($p))
			{

				if($r['type']==10)
				{
					$st=$r['time']-time();
					$txt='секунд.';
					if($st<0)
					{
						$st=0;
					}
					else
					if($st>60)
					{
						$st=round($st/60,2);
						$txt='минут.';
					}
					$eff_txt[10]="Вы не можете передвигаться еще ".$st." ".$txt.". ";
				$eff = true;
				}
				elseif(($r['type']==13) OR ($r['type']==14))
				{
				$array_trv[]=$r['type'];
				$eff_txt[13]="У вас тяжелая травма, вы не можете передвигаться... ";
				$eff = true;
				//echo "YES";
				}
				elseif(($r['type']==11) OR ($r['type']==12))
				{
				//если травма легкая или средняя - то запоминаем ее если есть перевес
				 if ($my_massa > $meshok)
				 	{
					$array_trv[]=$r['type'];
					}
				}
			}


					$tkos=array();
					if ($user['weap']) {$tkos[]=$user['weap'];}
					if ($user['shit']) {$tkos[]=$user['shit'];}
					if (count($tkos)>0)
						{
						$test_kost=mysql_query("select id, prototype  from  oldbk.inventory where owner='{$user['id']}' and id in (".implode(",",$tkos).")");
						$array_kost=array();
						while ($tkw = mysql_fetch_array($test_kost))
							{
								if (($tkw['prototype']==501) OR ($tkw['prototype']==502))
								{
								//если предметы костыли то запоминаем какие
								$array_kost[]=$tkw['prototype'];
								}

							}
						}

					if (count($array_kost)>0)
						{
						/// понимаем какие есть костыли и ставим время перехода
						$ihave_kost=true;
						//print_r($array_kost);
						unset($eff_txt[13]);
						}

		}
		else
		{

		}
	}




	if (($ihave_kost) and ($eff_txt[10]=='')) { $eff =false; } // фикс для тяжей если костыль


	$ignore_massa=false;
	if ( ($ihave_kost) AND ($user['sila']<=0)  ) { $ignore_massa=true; } //если минусовые статы и есть костыль игнорим массу


	// медитация
	$d2=mysql_query("SELECT * FROM `effects` WHERE `owner` = '".$user['id']."' AND (`type`=830) AND time>".time().";");
	if(mysql_num_rows($d2)>0) {
		$eff = true;
		$eff_txt[10] = "Вы находитесь под медитацией и не можете передвигаться";
	}
///Ограничения по весу

	if(($my_massa > $meshok && $_GET['got']) AND ($ignore_massa==false)) {
		//echo "<font color=red><b>У вас переполнен рюкзак, вы не можете передвигаться...</b></font>";
		$msg = "У вас переполнен рюкзак, вы не можете передвигаться...";
		$typet = "e";
		$_GET['got'] =0;
	}

	if(($my_massa > $meshok && $_GET['btlpl']) AND ($ignore_massa==false)) {
		//echo "<font color=red><b>У вас переполнен рюкзак, вы не можете передвигаться...</b></font>";
		$msg = "У вас переполнен рюкзак, вы не можете передвигаться...";
		$typet = "e";
		$_GET['btlpl'] =0;
	}

	if(($my_massa > $meshok && $_GET['cp']) AND ($ignore_massa==false)) {
		//echo "<font color=red><b>У вас переполнен рюкзак, вы не можете передвигаться...</b></font>";
		$msg = "У вас переполнен рюкзак, вы не можете передвигаться...";
		$typet = "e";
		$_GET['cp'] =0;
	}

	if(($my_massa > $meshok && $_GET['bps']) AND ($ignore_massa==false))
	{
		//echo "<font color=red><b>У вас переполнен рюкзак, вы не можете передвигаться...</b></font>";
		$msg = "У вас переполнен рюкзак, вы не можете передвигаться...";
		$typet = "e";
		$_GET['bps'] =0;
	}

	if(($my_massa > $meshok && $_GET['zp']) AND ($ignore_massa==false))
	{
		//echo "<font color=red><b>У вас переполнен рюкзак, вы не можете передвигаться...</b></font>";
		$msg = "У вас переполнен рюкзак, вы не можете передвигаться...";
		$typet = "e";
		$_GET['zp'] =0;
	}

	if(($my_massa > $meshok && $_GET['strah']) AND ($ignore_massa==false))
	{
		//echo "<font color=red><b>У вас переполнен рюкзак, вы не можете передвигаться...</b></font>";
		$msg = "У вас переполнен рюкзак, вы не можете передвигаться...";
		$typet = "e";
		$_GET['strah'] =0;
	}



//Ограничения по уровню	, невидимости или склонке

	if (($user[level] < 3) and ($_GET['btlpl']))
	{
		//echo "<font color=red><b>Вы не можите перейти , уровень маловат...</b></font>";
		$msg = "Вы не можите перейти , уровень маловат...";
		$typet = "e";
		$_GET['btlpl'] =0;
	}

	if ((($user['hidden']>0) ) and ($_GET['btlpl']))
	{
		//echo "<font color=red><b>Невидимка не может сюда попасть...!</b></font>";
		$msg = "Невидимка не может сюда попасть...!";
		$typet = "e";
		unset($_GET['btlpl']);
	}

	if (($user['align']==5) and ($_GET['btlpl']))
	{
		//echo "<font color=red><b>Вы не можете сюда попасть...!</b></font>";
		$msg = "Вы не можете сюда попасть...!";
		$typet = "e";
		unset($_GET['btlpl']);
	}
//Ограничения по травме



	if($eff && $_GET['got'])
	{
		//echo "<font color=red><b>".$eff_txt[10].$eff_txt[13]."</b></font>";
		$msg = $eff_txt[10].$eff_txt[13];
		$typet = "e";
		$_GET['got'] =0;
	}

	if($eff && $_GET['btlpl'])
	{
		//echo "<font color=red><b>".$eff_txt[10].$eff_txt[13]."</b></font>";
		$msg = $eff_txt[10].$eff_txt[13];
		$typet = "e";
		$_GET['btlpl'] =0;
	}

	if($eff && $_GET['strah'])
	{
		//echo "<font color=red><b>".$eff_txt[10].$eff_txt[13]."</b></font>";
		$msg = $eff_txt[10].$eff_txt[13];
		$typet = "e";
		$_GET['strah'] =0;
	}

	if($eff && $_GET['cp'])
	{
		//echo "<font color=red><b>".$eff_txt[10].$eff_txt[13]."</b></font>";
		$msg = $eff_txt[10].$eff_txt[13];
		$typet = "e";
		$_GET['cp'] =0;
	}

	if($eff && $_GET['bps'])
	{
		//echo "<font color=red><b>".$eff_txt[10].$eff_txt[13]."</b></font>";
		$msg = $eff_txt[10].$eff_txt[13];
		$typet = "e";
		$_GET['bps'] =0;
	}

	if($eff && $_GET['zp'])
	{
		//echo "<font color=red><b>".$eff_txt[10].$eff_txt[13]."</b></font>";
		$msg = $eff_txt[10].$eff_txt[13];
		$typet = "e";
		$_GET['zp'] =0;
	}

///Все передвижения

	if ($_GET['strah'])
	{
		MoveToLoc('city.php','Топаем на Страшилкину улицу',21);
	}
	else
	if ($_GET['btlpl'])
	{
		//if ($user['id']==14897)
		{
	 	MoveToLoc('bplace.php','Топаем на Арену Богов',60);
	 	}
/*	 	else
	 	{
	 	echo "<font color=red><b>Завалило...</b></font>";
	 	}
*/

	}
	else
	if ($_GET['cp'])
	{
		MoveToLoc('city.php','Топаем на Центральную площадь',20);
	}
	else
	if ($_GET['bps'])
	{
		MoveToLoc('city.php','Топаем на Большую Парковую улицу',26);
	}
	else
	if ($_GET['zp'])
	{
		MoveToLoc('city.php','Топаем на Замковую площадь',50);
	}
	else
	if ($_GET['torg'])
	{
		MoveToLoc('city.php','Топаем на Торговую улицу',66);
	}
	else
	if ($_GET['master'])
	{
		MoveToLoc('city.php','Топаем на Улицу Мастеров',191);
	}


	$MALA_ATTACK=true; // вкл/выкл кнопка на ЦП
	if ($MALA_ATTACK)
	 	{

	 			if ($ZIMA)
	 				{
		 			$BOTS_conf=array(89);
		 			}
		 			else
		 			{
		 			$BOTS_conf=array(86,87);
		 			}

			$get_bots=mysql_fetch_array(mysql_query("select * from users_clons where id_user in (".implode(",",$BOTS_conf).")  and hp>0 and bot_online!=-2 limit 1;"));
			if ($get_bots[id]>0)
			{
			//если есть боты все ок
			}
			else
			{
			//если ботов нету то выключаем
			$MALA_ATTACK=false;
			}
	 	}

   	if (($user['room']==20))
   	{
	$cp_a=mysql_fetch_array(mysql_query("SELECT * FROM `variables` where `var`='cp_attack_on' ;"));
	if ($cp_a[value]>0)
		{
			//$CP_ATTACK=true; - старая простая напа
			$CP_ATTACK2=true;
		}
   	}


	// стандартную кнопку если есть бой куча мала тоже вырубаем не показываем
	if ($MALA_ATTACK)
		{
			//$CP_ATTACK=false;
			$CP_ATTACK2=false;
		}


	if (($user['room']==20) AND ($_GET[attack]) AND ($CP_ATTACK2==true) )
	{
    		include "magic/attack.php";
    		if ($bet==1)
    		{
    			die();
    		}
	}
	elseif (($user['room']==20) AND ($_GET[attack]) AND ($MALA_ATTACK==true) and ($user[battle]==0) )
	{

    		include "magic/attack_kucha.php";
    		if ($bet==1)
    		{
    			die();
    		}
	}

if($eff_txt[10])//есть путы и тело на ЦП. никуда двинуться не можем!
{
	//echo '<font color=red><b>'.$eff_txt[10].'</b></font>';
}
else
{

	if ($user['room']==20) //Центральная площадь
	{
  $_tykva_start = (new DateTime('2018-10-16 22:00:00'))->getTimestamp();
  $_tykva_end = (new DateTime('2018-11-13 23:59:59'))->getTimestamp();

  $_tykva_start2 = (new DateTime('2018-10-30 00:00:00'))->getTimestamp();
  $_tykva_end2 = (new DateTime('2018-11-01 23:59:59'))->getTimestamp();

		/*
		if ($_GET['got'] && $_GET['level5'] )
		{

			if ((in_array($user['level'],$KO_A_ARRA6)) and ((time()>$KO_start_time6-14400) and (time()<$KO_fin_time6-14400)) )
				{
				$effect = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$user['id']}'  and `type` = '170' LIMIT 1;"));
				if ($effect['id']>0)
					{
					   print "<script>alert('У Вас уже есть такой эффект!')</script>";
					}
				else
					{
					$add_time_eff=$KO_fin_time6-14400;
					$nnm=(int)($KO_A_VAL6*100);
					$eff_name=$KO_A_TITLE6." +".$nnm."%";
					mysql_query("INSERT INTO `effects` SET `type`=170,`name`='{$eff_name}',`time`='{$add_time_eff}',`owner`='{$user[id]}', add_info='{$KO_A_VAL6}' ;");
					mysql_query("UPDATE users set expbonus=expbonus+{$KO_A_VAL6} where id='{$user[id]}' ; ");
					print "<script>alert('Вы получили повышеный опыт: +".$nnm."% до ".date ("d.m.y H:i:s", $add_time_eff)."  !')</script>";
					 }
				}
				else
				{
				   print "<script>alert('Вам к сожалению акция не доступна!')</script>";
				}


		}
		else */
		if ($_GET['got'] && $_GET['level1'])
		{
		header('location: main.php?setch=1&tmp='.(mt_rand(111111,999999)));
		die();
		}
        	else
		if ($_GET['got'] && $_GET['level7'])
		{
		header('location: city.php?strah=1&tmp='.(mt_rand(111111,999999)));
		die();
		}
		else
		if ($_GET['got'] && $_GET['level8'])
		{
		header('location: city.php?bps=1&tmp='.(mt_rand(111111,999999)));
		die();
		}
		else
		if ($_GET['got'] && $_GET['level61'])
		{
		header('location: doska.php?tmp='.(mt_rand(111111,999999)));
		die;
		}
		else
		if ($_GET['got'] && $_GET['level2014'])
		{
			if ((time() >= $ny_events['elkacpstart'] && time() <= $ny_events['elkacpend'])) {
				header('location: elka2019.php?tmp='.(mt_rand(111111,999999)));
				die;
			}
		}
		else
			if ($_GET['got'] && $_GET['level99'] )
			{


				if((time()<=$_tykva_end2)  /*or  ($user['klan']=='radminion')*/ )
				{
					if ((($_GET['level99']==1&&(time()>=$_tykva_start2)))  /*or  ($user['klan']=='radminion') */)
					{
						//праздничный стол
						$count=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`stol` where  `stol`=1 and owner='{$user[id]}' LIMIT 1;"));
						$sql="insert into oldbk.inventory set name = 'Магическое зелье', duration =0, maxdur=1,cost=0, owner=".$user[id].", img = 'gift_halloween_60.gif', dategoden='".(time()+60*60*24*1)."', magic=226, type=50, massa=1, goden=1, prototype=10000, otdel=6, ecost=0, idcity='{$user[id_city]}' ,present_text = 'подарок от Halloween',present = 'подарок от Halloween'";
						if (isset($count['count']) && $count['count'] > 4)
						{
							mysql_query($sql);
							print "<script>alert('Вы вынули из тыквы Магическое зелье!')</script>";
						}
						else
						{
							// даем и считаем
							$rn=rand(0,1);
							if($rn==0)
							{
								mysql_query($sql);
								print "<script>alert('Вы вынули из тыквы Магическое зелье!')</script>";
							}
							else
							{
								$ITname[1]='Пирожное с Привидениями';
								$ITfile[1]='halloween2014_items3.gif';

								$ITname[2]='Пирожное Паук';
								$ITfile[2]='halloween2014_items4.gif';

								$ITname[3]='Десерт Тыква';
								$ITfile[3]='halloween2014_items5.gif';

								$ITname[4]='Карамель Страшилка';
								$ITfile[4]='halloween2014_items6.gif';

								$ITname[5]='Кекс Бешеная Клубника';
								$ITfile[5]='halloween2014_items10.gif';
								$RND=rand(1,5);
								$sql1="INSERT INTO oldbk.inventory set name = '".$ITname[$RND]."', duration =0, maxdur=5,cost=0, owner=".$user[id].", img = '".$ITfile[$RND]."', dategoden='".(time()+60*60*24*1)."', magic=8, type=50, massa=1, goden=1, isrep=0, prototype=105, otdel=6, ecost=0, idcity='{$user[id_city]}' , present_text = 'Тыква на Halloween',present = 'подарок от Halloween';";
								mysql_query($sql1);
								mysql_query("INSERT oldbk.`stol` (`owner`,`stol`,`count`) values  ('".$user[id]."', '1', '1' ) ON DUPLICATE KEY UPDATE `count` =`count`+1;");
								print "<script>alert('Вы вынули из тыквы ".$ITname[$RND]."!')</script>";
							}
						}
					}
					else
					{
						print "<script>alert('Еще не время...')</script>";
					}

				}
				else
				{
					print "<script>alert('Еще не время...')</script>";
				}
			}
		else
		if ($_GET['got'] && $_GET['level2'])
		{
			move_to_trup(22);
			mysql_query("UPDATE `users`  SET `users`.`room` = '22' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
			header('location: shop.php?tmp='.(mt_rand(111111,999999)));
			die();
		}
		else
		if ($_GET['got'] && $_GET['level66'] && (CITY_ID==0))
		{
			header('location: city.php?torg=1&tmp='.(mt_rand(111111,999999)));
			die();
		}
		else
		if ($_GET['got'] && $_GET['level4'])
		{
			move_to_trup(23);
			mysql_query("UPDATE `users`  SET `users`.`room` = '23' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
			header('location: repair.php?tmp='.(mt_rand(111111,999999)));
			die();
		}
		else
		if ($_GET['got'] && $_GET['level11'] && (CITY_ID==0))
		{
			move_to_trup(42);
			mysql_query("UPDATE `users`  SET `users`.`room` = '42' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
			header('location: lotery.php?tmp='.(mt_rand(111111,999999)));
			die();
		}
		else
		if ($_GET['got'] && $_GET['level3'])
		{
			if ($user['align'] == 4)
			{
			echo "<script>alert('Хаосникам вход в комиссионный магазин запрещен!')</script>";
			}
			elseif ($user['level'] < 1)
			{
			echo "<script>alert('Вход в комиссионный магазин только с первого уровня!')</script>";
			}
			elseif ($comis_fire)
			{
			echo "<script>alert('Комиссионный магазин разрушен!')</script>";
			}
			else
			{
				move_to_trup(25);
				mysql_query("UPDATE `users`  SET `users`.`room` = '25' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
				header('location: comission.php?tmp='.(mt_rand(111111,999999)));
				die();
			}

		}
		else
		if ($_GET['got'] && $_GET['level6'])
		{
			if ($user['level'] < 1)
			{
				echo "<script>alert('Вход на почту только с первого уровня!')</script>";
			}
			else
			{
				move_to_trup(27);
				mysql_query("UPDATE `users`  SET `users`.`room` = '27' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
				header('location: post.php?tmp='.(mt_rand(111111,999999)));
				die();
			}
		}
		else
		if ($_GET['got'] && $_GET['level10'])
		{


			move_to_trup(35);
			mysql_query("UPDATE `users`  SET `users`.`room` = '35' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
			header('location: eshop.php?tmp='.(mt_rand(111111,999999)));
			die();

		}
		else
		if ($_GET['got'] && $_GET['level55'] && ((time()>$snegovik_start && time()<$snegovik_stop)))
		{

			$count=mysql_fetch_assoc(mysql_query("SELECT sum(cnt) as s FROM ".$db_city[CITY_ID]."`newyear_snowman` "));
		  	if($count[s]>=$count_bols_st1[CITY_ID][3])
		  	{
		  		?>
		                   	<SCRIPT LANGUAGE="javascript">
		                   	alert('Меня построили из <?=$count[s]?> снежных шариков')</script>
		                <?
		  	}
		  	else
			{
		            if($_GET[bet]==1)
		            {
		                if(mysql_query("DELETE FROM oldbk.`inventory` WHERE owner='".$user[id]."' AND prototype=300300;"))
		                {
		                 	$c=mysql_affected_rows();
		                 	if($c>0)
		                 	{
			               		if(($count[s]+$c)>=$count_bols_st1[CITY_ID][3])
			               		{
			               			mysql_query("UPDATE ".$db_city[CITY_ID]."`variables` SET value=1 WHERE var='snow_man';");
			               		}

			               		mysql_query("INSERT INTO ".$db_city[CITY_ID]."`newyear_snowman`  (`owner`,`cnt`) VALUES ('".$user[id]."','".$c."') ON DUPLICATE KEY UPDATE cnt=cnt+'".$c."';");

						if (mt_rand(1,100)<=3) //3%
						{
						drop_card($user);
						}

								try {
									$User = new \components\models\User($user);
									$Quest = $app->quest
										->setUser($User)
										->get();
									$Checker = new \components\Component\Quests\check\CheckerEvent();
									$Checker->event_type = \components\Component\Quests\pocket\questTask\EventTask::EVENT_GIVE_SNOWBALL;

									if(($Item = $Quest->isNeed($Checker)) !== false) {
										$Quest->taskUp($Item);
									}

								} catch (Exception $ex) {

								}

			                        echo "<script>alert('Вы принесли ".$c." снежных шариков.')</script>";
		                 	}
		                }
		            }
		            else
		            {
				//$count=mysql_fetch_assoc(mysql_query("SELECT sum(cnt) as s FROM oldbk.`newyear_snowman` "));
		   		$data=mysql_query("SELECT * FROM oldbk.`inventory` WHERE owner='".$user[id]."' AND prototype='300300' LIMIT 1;");
		      		if(mysql_num_rows($data))
			      	{
		                   ?>
		                   	<SCRIPT LANGUAGE="javascript">
					if (confirm("Хотите сдать все снежные шарики?\r\n \r\n Уже навалили <?=$count[s]?> снежных шариков"))
					{
						//parent.main.location.href='?got=1&level55=1&bet=1';
						location.href='?got=1&level55=1&bet=1';
					}
					</SCRIPT>
		                   <?
			      	}
			      	else
			      	{
			      	$ostn=(int)($count_bols_st1[CITY_ID][3]-$count[s]);
			      	if ($ostn<0) $ostn=0;
			      		?>
			      		<script>alert('У Вас нет нужных предметов... \r\n \r\n Уже навалили <?=$count[s]?> снежных шариков \r\n \r\n Осталось собрать <?=$ostn;?> снежков. ')</script>
			      		<?
			      	}

			     }
			}
		}
		else
		{
		//echo "OTHER";
		}
	}
	elseif($user['room']==21) //Страшилкина улица
	{
		if ($_GET['got'] && $_GET['level4'])
		{
			header('location: city.php?cp=1&tmp='.(mt_rand(111111,999999)));
			die();
		}
		else
		if ($_GET['got'] && $_GET['level5'])
		{
			move_to_trup(29);
			mysql_query("UPDATE `users`  SET `users`.`room` = '29' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
			header('location: bank.php?tmp='.(mt_rand(111111,999999)));
			die();
		}
		else
/*		if($_GET['got'] && $_GET['level3311'])
		{
			$mess[]="А в темнице сейчас ужин - макароны...";
			$mess[]="Спеши жить, сесть всегда успеешь...";
			$mess[]="Жизнь как трамвай: кто хочет ехать с комфортом, сидит.";
			$mess[]="Не тыкайте в него пальцами, не надо!";
			$mess[]="Неделя в тюрьме — это много. На воле — мало.";
			$mess[]="О, а вы не против отсидеть за что-нибудь? Ребята придумают...";
			$mess[]="Не нарушай, если некогда сидеть…";
			$mess[]="Мы здесь все невиновны, ты разве не знаешь?";
			$mess[]="Люди делятся на две половины: те, кто сидит, и те, кто должен сидеть...";
			$mess[]="Ко всякому человеку нужен особый ключ - и, разумеется, небольшая отдельная камера.";
			$mess[]="Сколько хороших людей встало на путь исправления!";
			$mess[]="Тревожат не те люди, которые сидят. Тревожат люди, которые не сидят...";
			$mess[]="Если хочешь узнать, кто твой настоящий друг, постарайся попасть за решетку.";
			$mess[]="Когда небо обросло, наконец, решетками, мир стал привычен, ограничен и предсказуем.";

			echo "<script>alert('".$mess[mt_rand(0,count($mess)-1)]."')</script>";
		}
		else*/
		if ($_GET['got'] && $_GET['level6'])
		{
			if ($user['align'] == 4)
			{
				echo "<script>alert('Хаосникам вход в магазин запрещен!')</script>";
			}
			else
			{
				MoveToLoc('fshop.php?tmp='.(mt_rand(111111,999999)),'Топаем в Цветочный магазин', 34, true);
			}
		}
		else
		if ($_GET['got'] && $_GET['level16'] && (CITY_ID==1) )
		{
			$eff = mysql_fetch_array(mysql_query("SELECT COUNT(1) FROM `users` WHERE `room` = 51 and `odate` >= ".(time()-60)."  ;"));
			if ($eff[0] >=10) //большая скамейка
			{
				echo "<script>alert('На скамейке нет свободных мест!')</script>";
			}
			else
			{
			//clear off lines users from room by Fred
				mysql_query("UPDATE `users` SET `room` = '26' WHERE `users`.`room` ='51' and battle=0 AND `ldate` < ".(time()-60)." ;");
				move_to_trup(51);
				mysql_query("UPDATE `users`  SET `users`.`room` = '51' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
				header('location: bench.php?tmp='.(mt_rand(111111,999999)));
				die();
			}
		}
		else
		if ($_GET['got'] && $_GET['level3200'])
		{
			MoveToLoc('restal.php','Топаем на Ристалище',200);
		}
		else
		if ($_GET['got'] && $_GET['level2'])
		{

			if (CITY_ID==0)
			{
				move_to_trup(28);
				mysql_query("UPDATE `users`  SET `users`.`room` = '28' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
				header('location: klanedit.php?tmp='.(mt_rand(111111,999999)));
				die();
			}
			else if (CITY_ID==1)
			{
				echo "<script>alert('Временно закрыто!'); </script>";
			}

		}
		else
		if ($_GET['got'] && $_GET['level77'])
		{
			if (($user['hidden'] > 0))
			{
				//echo "<font color=red><b>Невидимка не может попасть в Башню смерти...</b></font>";
				$msg = "Невидимка не может попасть в Башню смерти...";
				$typet = "e";
			}
			elseif($user['align'] == 4)
			{
				//echo "<font color=red><b>Хаосник не может попасть в Башню смерти...</b></font>";
				$msg = "Хаосник не может попасть в Башню смерти...";
				$typet = "e";
			}
			else
			{
				move_to_trup(10000);
				mysql_query("UPDATE `users`  SET `users`.`room` = '10000' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
				header('location: dt_start.php?tmp='.(mt_rand(111111,999999)));
				die();
			}
		}
	}
	elseif($user['room']==26) //парковая улица
	{
		if ($_GET['level4'])
		{
			header('location: city.php?cp=1&tmp='.(mt_rand(111111,999999)));
			die();
		}
		else
		if ($_GET['got'] && $_GET['level191'] && (CITY_ID==0))
		{
			header('location: city.php?master=1&tmp='.(mt_rand(111111,999999)));
			die();
		}
		else
		if ($_GET['got'] && $_GET['level66'] && (CITY_ID==1))
		{
			header('location: city.php?torg=1&tmp='.(mt_rand(111111,999999)));
			die();
		}
		else
		if ($_GET['got'] && $_GET['level10'] && (CITY_ID==0))
		{
			//move_to_trup(49999);
			mysql_query("UPDATE `users`  SET `users`.`room` = '49999' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
			header('location: outcity.php?tmp='.(mt_rand(111111,999999)));
			die();
		}
		else
		if ($_GET['got'] && $_GET['level1'] && (CITY_ID==1)) {
			if ($user['level'] < 6) {
				print "<script>alert('Уровень маловат!')</script>";
			}
			else
			{
				//move_to_trup(49998);
				mysql_query("UPDATE `users`  SET `users`.`room` = '49998' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
				$_POST['target'] = "capital";
				$ABIL = 1;
				$NOCHANGECITY = 1;
				require_once('./magic/city_teleport.php');
				die();
			}
		}
		else
		if ($_GET['got'] && $_GET['level11'] && (CITY_ID==1))
		{
			move_to_trup(42);
			mysql_query("UPDATE `users`  SET `users`.`room` = '42' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
			header('location: lotery.php?tmp='.(mt_rand(111111,999999)));
			die();
		}
		else
		if ($_GET['got'] && $_GET['level72'] )
		{
			if ($user['level']<6)
			{
				echo "<script>alert('Вход только с 6-го уровня')</script>";
			}
			else
			{
				move_to_trup(72);
				mysql_query("UPDATE `users`  SET `users`.`room` = '72' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
				header('location: fair.php?tmp='.(mt_rand(111111,999999)));
				die();
			}
		}
		else
		if ($_GET['got'] && $_GET['level22'])
		{
			echo "<script>alert('Вокзал закрыт!')</script>";
			/*
			if ($user['level'] < 4) {
				echo "<script>alert('Уровень маловат!')</script>";
			} else {
				move_to_trup(61000);
				mysql_query("UPDATE `users`  SET `users`.`room` = '61000' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
				header('location: station.php?tmp='.(mt_rand(111111,999999)));
				die();
			}*/
		}
		else
		if ($_GET['got'] && $_GET['level5'])
		{
			move_to_trup(43);
			mysql_query("UPDATE `users`  SET `users`.`room` = '43' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
			header('location: znahar.php?tmp='.(mt_rand(111111,999999)));
			die();
		}
		else
		if ($_GET['got'] && $_GET['level6'] && (CITY_ID==0) )
		{
			$eff = mysql_fetch_array(mysql_query("SELECT COUNT(1) FROM `users` WHERE `room` = 51 and `odate` >= ".(time()-60)."  ;"));
			if ($eff[0] >=10) //большая скамейка
			{
			echo "<script>alert('На скамейке нет свободных мест!')</script>";
			}
			else
			{
			//clear off lines users from room by Fred
			mysql_query("UPDATE `users` SET `room` = '26' WHERE `users`.`room` ='51' and battle=0 AND `ldate` < ".(time()-60)." ;");
			move_to_trup(51);
			mysql_query("UPDATE `users`  SET `users`.`room` = '51' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
			header('location: bench.php?tmp='.(mt_rand(111111,999999)));
			die();
			}
		}
		else
		if ($_GET['got'] && $_GET['level7'] && (CITY_ID==0))
		{
			$eff = mysql_fetch_array(mysql_query("SELECT COUNT(1) FROM `users`  WHERE  `room` = 52  and `odate` >= ".(time()-60)." ;"));
			if ($eff[0] >=5)
			{
			echo "<script>alert('На скамейке нет свободных мест!')</script>";
			}
			else
			{
			//clear off lines users from room by Fred
			mysql_query("UPDATE `users` SET `room` = '26' WHERE `users`.`room` ='52' and battle=0 AND `ldate` < ".(time()-60)." ;");
			move_to_trup(52);
			mysql_query("UPDATE `users`  SET `users`.`room` = '52' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
			header('location: bench.php?tmp='.(mt_rand(111111,999999)));
			die();
			}
		}
		else
		if ($_GET['got'] && $_GET['level8'])
		{
			$eff = mysql_fetch_array(mysql_query("SELECT COUNT(1) FROM `users`  WHERE `room` = 53 and `odate` >= ".(time()-60)." ;"));
			if ($eff[0] >=2)
			{
			echo "<script>alert('На скамейке нет свободных мест!')</script>";
			}
			else
			{
			//clear off lines users from room by Fred
			mysql_query("UPDATE `users` SET `room` = '26' WHERE `users`.`room` ='53' and battle=0  AND `ldate` < ".(time()-60)." ;");
			move_to_trup(53);
			mysql_query("UPDATE `users`  SET `users`.`room` = '53' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
			header('location: bench.php?tmp='.(mt_rand(111111,999999)));
			die();
			}
		}
		else
		if ($_GET['got'] && $_GET['level3'])
		{
		header('location: city.php?zp=1&tmp='.(mt_rand(111111,999999)));
		die();
		}
	}
	elseif($user['room']==50) //Замковая площадь
	{
		if ($_GET['got'] && $_GET['level60'])
		{
			header('location: city.php?btlpl=1&tmp='.(mt_rand(111111,999999)));
			die();
		}
		else
		if ($_GET['got'] && $_GET['level1'])
		{
			move_to_trup(999);
			mysql_query("UPDATE `users` SET `users`.`room` = '999' WHERE `users`.`id`  = '{$_SESSION['uid']}' ;");
			header('location: ruines_start.php?tmp='.(mt_rand(111111,999999)));
			die();
		}
		else
		if ($_GET['got'] && $_GET['level7'] && (CITY_ID==1))
		{
			$eff = mysql_fetch_array(mysql_query("SELECT COUNT(1) FROM `users`  WHERE  `room` = 52 and `odate` >= ".(time()-60)." ;"));
			if ($eff[0] >=5)
			{
			echo "<script>alert('На скамейке нет свободных мест!')</script>";
			}
			else
			{
				//clear off lines users from room by Fred
				mysql_query("UPDATE `users` SET `room` = '26' WHERE `users`.`room` ='52' and battle=0  AND `ldate` < ".(time()-60)." ;");
				move_to_trup(52);
				mysql_query("UPDATE `users`  SET `users`.`room` = '52' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
				header('location: bench.php?tmp='.(mt_rand(111111,999999)));
				die();
			}
		}
		else
		if ($_GET['level4'])
		{
			header('location: city.php?bps=1&tmp='.(mt_rand(111111,999999)));
			die();
		}
		else
		if ($_GET['got'] && $_GET['level49'])
		{
			move_to_trup(49);
			mysql_query("UPDATE `users`  SET `users`.`room` = '49' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
			header('location: church.php?tmp='.(mt_rand(111111,999999)));
			die();
		}
		else
		if ($_GET['got'] && $_GET['level48'])
		{
			move_to_trup(48);
			mysql_query("UPDATE `users`  SET `users`.`room` = '48' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
			header('location: cshop.php?tmp='.(mt_rand(111111,999999)));
			die();
		}
		else
		if ($_GET['got'] && $_GET['level45'])
		{
			if ($user['level'] < 4)
			{
				echo "<script>alert('Вход в Лабиринт только с 4-го уровня!')</script>";
			}
			elseif ($user['hidden']> 0 )
			{
				echo "<script>alert('Невидимка не может попасть в Лабиринт...!')</script>";
			}
			else
			{
				move_to_trup(45);
				mysql_query("UPDATE `users`  SET `users`.`room` = '45' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
				header('location: startlab.php?tmp='.(mt_rand(111111,999999)));
				die();
			}
		}
	}
	elseif($user['room']==66) //Торговая улица
	{
		if ($_GET['got'] && $_GET['level70'])
		{
			/*
			if ($user[align]==4)
			{
				echo "<script>alert('Хаосники не могут сюда войти...')</script>";
			}
			else
			{
			*/
				move_to_trup(70);
				mysql_query("UPDATE `users`  SET `users`.`room` = '70' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
				header('location: pawnbroker.php?tmp='.(mt_rand(111111,999999)));
				die();
			//}
		}
		else
		if ($_GET['got'] && $_GET['level71'])
		{
			if ($user[align]==4)
			{
				echo "<script>alert('Хаосники не могут сюда войти...')</script>";
			}
			else
			{
				move_to_trup(71);
				mysql_query("UPDATE `users`  SET `users`.`room` = '71' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
				header('location: auction.php?tmp='.(mt_rand(111111,999999)));
				die();
			}
		}
		else
		if($_GET['got'] && $_GET['level67'])
		{
	              	header('location: fontan.php?tmp='.(mt_rand(111111,999999)));
	              	die();
		}
		else
		if ($_GET['got'] && $_GET['level88'])
		{



			move_to_trup(46);
			mysql_query("UPDATE `users`  SET `users`.`room` = '46' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
			header('location: prokat.php?tmp='.(mt_rand(111111,999999)));
			die();



		}
		else
		if ($_GET['got'] && $_GET['level47'])
		{
			move_to_trup(47);
			mysql_query("UPDATE `users`  SET `users`.`room` = '47' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
			header('location: rentalshop.php?tmp='.(mt_rand(111111,999999)));
			die();
		}
		else
		if ($_GET['got'] && $_GET['level20'] && (CITY_ID==0))
		{
			header('location: city.php?cp=1&tmp='.(mt_rand(111111,999999)));
			die();
		}
		else
		if ($_GET['got'] && $_GET['level20'] && (CITY_ID==1))
		{
			header('location: city.php?bps=1&tmp='.(mt_rand(111111,999999)));
			die();
		}
	}
	elseif($user['room']==191) // улица мастеров
	{
		if ($_GET['got'] && $_GET['level91'] && (CITY_ID==0))
		{
			move_to_trup(91);
			mysql_query("UPDATE `users`  SET `users`.`room` = '91' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
			header('Location: craft.php');
			die();
		}
		if ($_GET['got'] && $_GET['level92'] && (CITY_ID==0))
		{
			move_to_trup(92);
			mysql_query("UPDATE `users`  SET `users`.`room` = '92' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
			header('Location: craft.php');
			die();
		}
		if ($_GET['got'] && $_GET['level93'] && (CITY_ID==0))
		{
			move_to_trup(93);
			mysql_query("UPDATE `users`  SET `users`.`room` = '93' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
			header('Location: craft.php');
			die();
		}
		if ($_GET['got'] && $_GET['level94'] && (CITY_ID==0))
		{
			move_to_trup(94);
			mysql_query("UPDATE `users`  SET `users`.`room` = '94' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
			header('Location: craft.php');
			die();
		}
		if ($_GET['got'] && $_GET['level95'] && (CITY_ID==0))
		{
			move_to_trup(95);
			mysql_query("UPDATE `users`  SET `users`.`room` = '95' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
			header('Location: craft.php');
			die();
		}

		if ($_GET['got'] && $_GET['level96'] && (CITY_ID==0))
		{
			move_to_trup(96);
			mysql_query("UPDATE `users`  SET `users`.`room` = '96' WHERE `users`.`id` = '{$_SESSION['uid']}' ;");
			header('Location: craft.php');
			die();
		}

		if ($_GET['got'] && $_GET['level8'] && (CITY_ID==0))
		{
			header('location: city.php?bps=1&tmp='.(mt_rand(111111,999999)));
			die();
		}
	}

}

//HTML -верстка экран
?>
<HTML><HEAD>
<link rel=stylesheet type="text/css" href="http://i.oldbk.com/i/main.css">
<link rel="stylesheet" href="/i/btn.css" type="text/css">
<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
<META Http-Equiv=Cache-Control Content=no-cache>
<meta http-equiv=PRAGMA content=NO-CACHE>
<META Http-Equiv=Expires Content=0>
<META HTTP-EQUIV="imagetoolbar" CONTENT="no">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script type="text/javascript" src="i/js/noty/packaged/jquery.noty.packaged.min.js"></script>
<script type="text/javascript" src="i/js/noty/packaged/custom.js"></script>
<script type="text/javascript" src="/i/globaljs.js"></script>

<style>
    IMG.aFilter { filter:Glow(Color=d7d7d7,Strength=9,Enabled=0); cursor:hand }
    .noty_message { padding: 5px !important;}
</style>
<style type="text/css">
img, div { behavior: url(/i/city/ie/iepngfix.htc) }
</style>

<SCRIPT LANGUAGE="JavaScript">
<?

{
?>
var Hint3Name = '';
// Заголовок, название скрипта, имя поля с логином

function findlogin(title, script, name){
    var el = document.getElementById("hint3");
	el.innerHTML = '<table width=100% cellspacing=1 cellpadding=0 bgcolor=CCC3AA><tr><td align=center><B>'+title+'</td><td width=15 align=right valign=top style="cursor: pointer" onclick="closehint3();"><BIG><B>x</td></tr><tr><td colspan=2>'+
	'<form action="'+script+'" method=POST><table width=100% cellspacing=0 cellpadding=2 bgcolor=FFF6DD><tr><td colspan=2><INPUT TYPE=hidden name=sd4 value="6">'+
	'Укажите логин персонажа:<small><BR>(можно щелкнуть по логину в чате)</TD></TR><TR><TD width=50% align=right><INPUT TYPE=text id="'+name+'" NAME="'+name+'"></TD><TD width=50%><INPUT TYPE="submit" value=" »» "></TD></TR></TABLE></FORM></td></tr></table>';
	el.style.visibility = "visible";
	el.style.left = 1000;
	el.style.top = 75;
	document.getElementById(name).focus();
	Hint3Name = name;
}

function closehint3(clearstored){
	if(clearstored)
	{
		var targetform = document.getElementById('formtarget');
		targetform.action += "&clearstored=1";
		targetform.submit();
	}
	document.getElementById("hint3").style.visibility="hidden";
    Hint3Name='';
}

<?
}
?>

	  function show(ele) {
	      var srcElement = document.getElementById(ele);
	      if(srcElement != null) {
	          if(srcElement.style.display == "block") {
	            srcElement.style.display= 'none';
	          }
	          else {
	            srcElement.style.display='block';
	          }
	      }
	  }

function solo(n)
{


		<?php if(!is_array($_SESSION['vk'])) echo'top.'; else echo'parent.'; ?>changeroom=n;
		window.location.href='?got=1&level'+n+'=1';

}

function imover(im)
{
	im.filters.Glow.Enabled=true;
//	im.style.visibility="hidden";
}

function imout(im)
{
	im.filters.Glow.Enabled=false;
//	im.style.visibility="visible";
}

function Down() {<?php if(!is_array($_SESSION['vk'])) echo'top.'; else echo'parent.'; ?>CtrlPress = window.event.ctrlKey}

	document.onmousedown = Down;
</SCRIPT>
		<!-- Asynchronous Tracking GA top piece counter -->
<script type="text/javascript">

var _gaq = _gaq || [];

var rsrc = /mgd_src=(\d+)/ig.exec(document.URL);
    if(rsrc != null) {
        _gaq.push(['_setCustomVar', 1, 'mgd_src', rsrc[1], 2]);
    }

_gaq.push(['_setAccount', 'UA-17715832-1']);
_gaq.push(['_addOrganic', 'm.yandex.ru', 'text', true]);
_gaq.push(['_addOrganic', 'images.yandex.ru', 'text', true]);
_gaq.push(['_addOrganic', 'blogs.yandex.ru', 'text', true]);
_gaq.push(['_addOrganic', 'video.yandex.ru', 'text', true]);
_gaq.push(['_addOrganic', 'go.mail.ru', 'q']);
_gaq.push(['_addOrganic', 'm.go.mail.ru', 'q', true]);
_gaq.push(['_addOrganic', 'mail.ru', 'q']);
_gaq.push(['_addOrganic', 'google.com.ua', 'q']);
_gaq.push(['_addOrganic', 'images.google.ru', 'q', true]);
_gaq.push(['_addOrganic', 'maps.google.ru', 'q', true]);
_gaq.push(['_addOrganic', 'nova.rambler.ru', 'query']);
_gaq.push(['_addOrganic', 'm.rambler.ru', 'query', true]);
_gaq.push(['_addOrganic', 'gogo.ru', 'q']);
_gaq.push(['_addOrganic', 'nigma.ru', 's']);
_gaq.push(['_addOrganic', 'search.qip.ru', 'query']);
_gaq.push(['_addOrganic', 'webalta.ru', 'q']);
_gaq.push(['_addOrganic', 'sm.aport.ru', 'r']);
_gaq.push(['_addOrganic', 'akavita.by', 'z']);
_gaq.push(['_addOrganic', 'meta.ua', 'q']);
_gaq.push(['_addOrganic', 'search.bigmir.net', 'z']);
_gaq.push(['_addOrganic', 'search.tut.by', 'query']);
_gaq.push(['_addOrganic', 'all.by', 'query']);
_gaq.push(['_addOrganic', 'search.i.ua', 'q']);
_gaq.push(['_addOrganic', 'index.online.ua', 'q']);
_gaq.push(['_addOrganic', 'web20.a.ua', 'query']);
_gaq.push(['_addOrganic', 'search.ukr.net', 'search_query']);
_gaq.push(['_addOrganic', 'search.com.ua', 'q']);
_gaq.push(['_addOrganic', 'search.ua', 'q']);
_gaq.push(['_addOrganic', 'poisk.ru', 'text']);
_gaq.push(['_addOrganic', 'go.km.ru', 'sq']);
_gaq.push(['_addOrganic', 'liveinternet.ru', 'ask']);
_gaq.push(['_addOrganic', 'gde.ru', 'keywords']);
_gaq.push(['_addOrganic', 'affiliates.quintura.com', 'request']);
_gaq.push(['_trackPageview']);
_gaq.push(['_trackPageLoadTime']);
</script>
<!-- Asynchronous Tracking GA top piece end -->
</HEAD>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 bgcolor="#d7d7d7">
			<div id="pl" class="popup-block" style="z-index: 300; position: absolute; display: none; top: 10%;left: 50%; width: 475px; min-width: 475px; height: 625px; min-height: 625px; background-image: url('http://i.oldbk.com/i/diz/bg_14.png');background-repeat: no-repeat;">
			</div>
			<div id="pl2" style="z-index: 300; position: absolute; display: none; top: 10%;left: 50%; width: 475px; min-width: 475px; min-height: 275px; background-color:#fff6dd;">
			</div>


<?

make_quest_div();
?>
<script>
$(function(){
	$('.popup-block .close').live('click', function(){
		$(this).closest('.popup-block').hide(200);
	});

	$('[data-type="ajax"]').click(function(e){
		e.preventDefault();
		var $self = $(this);
		$.ajax({
			url: $self.attr('data-url'),
			dataType: $self.attr('data-response'),
			success: function(response) {
				if(response['redirect'] !== undefined) {
					window.location.href = response['redirect'];
					return;
				}

				if(response['remove_id'] !== undefined && $('#' + response['remove_id']).length) {
					$('#' + response['remove_id']).remove();
				}
				$('body').append(response['content']);
			}
		});
	});
});
function getdivdata(id,param,event)
			{
				if (window.event)
				{
					event = window.event;
				}
				if (event )
				{

				       $.get('divdata.php?', function(data) {
					  $('#pl').html(data);
					  $('#pl').show(200, function() {
						});
					});

				 $('#pl').css({ position:'absolute',left: ($(window).width()-$('#pl').outerWidth())/2, top: '120px'  });


				}

			}

			function closeinfo()
			{
			  	$('#pl').hide(200);
			}

$(window).resize(function() {
 $('#pl').css({ position:'absolute',left: ($(window).width()-$('#pl').outerWidth())/2, top: '120px'  });
});

function getftreedata(id,param,event)
			{
				if (window.event)
				{
					event = window.event;
				}
				if (event )
				{

				       $.get('ftreedata.php?step='+param+'&id='+id, function(data) {

					  $('#pl2').html(data);
					  $('#pl2').show(200, function() {
						});
					});

				 $('#pl2').css({ position:'absolute',left: ($(window).width()-$('#pl2').outerWidth())/2, top: '10px', width:'540px'  });


				}

			}

			function closeinfo()
			{
			  	$('#pl2').hide(200);
			}

$(window).resize(function() {
 $('#pl2').css({ position:'absolute',left: ($(window).width()-$('#pl2').outerWidth())/2, top: '10px' , width:'540px'  });
});

</script>
<TABLE width=100% height=100% border=0 cellspacing="0" cellpadding="0">
<TR>
	<TD align=center></TD>
	<TD align=right>
        <div class="btn-control">
            <INPUT class="button-dark-mid btn" TYPE="button" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/city<?=$user[room];?>.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')">
	<?

	if (($user[room]==20) AND ($CP_ATTACK2==true))
		{
			echo "<br><font color=red>Хулиганы и бандиты сегодня беспредельничают на улицах города! Будьте осторожны!&nbsp;&nbsp;<br><small>При нападении иллюзия невидимости и перевоплощения рассеивается.</small></font><br>";
			echo '<INPUT class="button-mid btn" TYPE="button" value="Напасть" style="background-color:red" onclick="findlogin(\'Введите имя персонажа\', \'city.php?attack=1\', \'target\');" >';
		}
	elseif (($user[room]==20) AND ($MALA_ATTACK==true))
		{
			echo "<br><font color=red>Внимание идет бой Куча-мала!&nbsp;&nbsp;</font><br>";
			echo '<form method=GET><INPUT class="button-big btn" TYPE="submit" name=attack value="Вмешаться в кучу-малу" style="background-color:red;"></form>';
		}
	?>
        </div>
	</TD></TR>
	<TR><TD align=center  valign=top colspan=2 ><?

	function buildset($id,$img,$top,$left,$des) {
		$imga = ImageCreateFromGif("i/city/sub/".$img.".gif");
		#Get image width / height
		$x = ImageSX($imga);
		$y = ImageSY($imga);
		unset($imga);
		echo "<div style=\"position:absolute; left:{$left}px; top:{$top}px; width:{$x}; height:${y}; z-index:90; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=100, Style=0);\"
	 	><img src=\"http://i.oldbk.com/i/city/sub/{$img}.gif\" width=\"${x}\" height=\"${y}\" alt=\"{$des}\" title=\"{$des}\" class=\"aFilter\" onmouseover=\"imover(this)\" onmouseout=\"imout(this);\"
	 	id=\"{$id}\" onclick=\"solo({$id})\" /></div>";
	}

	function buildsetPNG($id,$img,$top,$left,$des,$ajax_params = array()) {
		$ajax_placeholder = array(
			'enable' => !empty($ajax_params),
			'url' => null,
			'response_type' => 'html'
		);
		$ajax_params = array_merge($ajax_placeholder, $ajax_params);
		$imga = ImageCreateFromPNG("i/city/sub/".$img.".png");
		#Get image width / height
		$x = ImageSX($imga);
		$y = ImageSY($imga);
		unset($imga);

		if ($id==14)
		{
		$dofunc="getdivdata(1,0,event);";
		}
		elseif ($id==777)
		{
		$dofunc="getftreedata(0,0,event);";
		}
		else
		{
		$dofunc="solo({$id})";
		}
		if($ajax_params['enable'] === true) {
			$dofunc = 'javascript:void(0);';
		}

		$zindex = 90;
		if ($id == 0) $zindex = 2;

		if (strpos($_SERVER['HTTP_USER_AGENT'],"MSIE 6.0"))
		 {
		 echo "<div style=\"";
		 if ($id!=102) {  echo "cursor: pointer; ";  }
		 echo " position:absolute; left:{$left}px; top:{$top}px; width:{$x}; height:${y}; z-index:".($zindex)."; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=100, Style=0);\"
		 ><img src=\"http://i.oldbk.com/i/city/sub/{$img}.png\" width=\"${x}\" height=\"${y}\" alt=\"{$des}\" title=\"{$des}\" class=\"aFilter\" onmouseover=\"this.src='http://i.oldbk.com/i/city/sub/{$img}2.png'\" onmouseout=\"this.src='http://i.oldbk.com/i/city/sub/{$img}.png'\"
		 id=\"{$id}\" ";
  		 if ($id!=102) {  echo "onclick=\"{$dofunc}\"";  }
		 if($ajax_params['enable'] === true) { echo ' data-type="ajax" data-response="'.$ajax_params['response_type'].'" data-url="'.$ajax_params['url'].'"'; }
		 echo " /></div>";
		 }
		 else
		 {
 		 echo "<div style=\"";
		 if ($id!=102) {  echo "cursor: pointer; ";  }
		 echo "position:absolute; left:{$left}px; top:{$top}px; width:{$x}; height:${y}; z-index:".($zindex)."; \"
		 ><img src=\"http://i.oldbk.com/i/city/sub/{$img}.png\" width=\"${x}\" height=\"${y}\" alt=\"{$des}\" title=\"{$des}\" class=\"aFilter2\" onmouseover=\"this.src='http://i.oldbk.com/i/city/sub/{$img}2.png'\" onmouseout=\"this.src='http://i.oldbk.com/i/city/sub/{$img}.png'\"
		 id=\"{$id}\" ";
 		 if ($id!=102) {  echo "onclick=\"{$dofunc}\"";  }
		 if($ajax_params['enable'] === true) { echo ' data-type="ajax" data-response="'.$ajax_params['response_type'].'" data-url="'.$ajax_params['url'].'"'; }
		 echo " /></div>";
		 }
	 }

	function buildsetGIF($id,$img,$top,$left,$des) {
		$imga = ImageCreateFromGIF("i/city/sub/".$img.".gif");
		#Get image width / height
		$x = ImageSX($imga);
		$y = ImageSY($imga);
		unset($imga);

		if (strpos($_SERVER['HTTP_USER_AGENT'],"MSIE 6.0"))
	 {
	 echo "<div style=\"position:absolute; cursor: pointer; left:{$left}px; top:{$top}px; width:{$x}; height:${y}; z-index:90; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=100, Style=0);\"
	 ><img src=\"http://i.oldbk.com/i/city/sub/{$img}.gif\" width=\"${x}\" height=\"${y}\" alt=\"{$des}\" title=\"{$des}\" class=\"aFilter\" onmouseover=\"this.src='http://i.oldbk.com/i/city/sub/{$img}2.gif'\" onmouseout=\"this.src='http://i.oldbk.com/i/city/sub/{$img}.gif'\"
	 id=\"{$id}\" onclick=\"solo({$id})\" /></div>";
	 }
	 else
	 {
	 echo "<div style=\"position:absolute; cursor: pointer; left:{$left}px; top:{$top}px; width:{$x}; height:${y}; z-index:90; \"
	 ><img src=\"http://i.oldbk.com/i/city/sub/{$img}.gif\" width=\"${x}\" height=\"${y}\" alt=\"{$des}\" title=\"{$des}\" class=\"aFilter2\" onmouseover=\"this.src='http://i.oldbk.com/i/city/sub/{$img}2.gif'\" onmouseout=\"this.src='http://i.oldbk.com/i/city/sub/{$img}.gif'\"
	 id=\"{$id}\" onclick=\"solo({$id})\" /></div>";
	 }



	 }

////////////////////////////////////////////////////


if (ADMIN && (isset($_GET['zima']) || isset($_GET['vesna']) || isset($_GET['osen']) || isset($_GET['leto']))) {
	$ZIMA = false;
	$VESNA = false;
	$OSEN = false;
	$LETO = false;

	if (isset($_GET['zima'])) $ZIMA = true;
	if (isset($_GET['vesna'])) $VESNA = true;
	if (isset($_GET['osen'])) $OSEN = true;
	if (isset($_GET['leto'])) $LETO = true;
}



$BAR_SHOW=true;
if (($user['klan']=='radminion') OR ($user['klan']=='testTest') OR ($user['id']==188) )
	{
	$BAR_SHOW=true;
	}

////////////////////////////////////////////////////
//RENDER CAPITAL
if (CITY_ID==0)
{
if ($user['room'] == 20)
{
//Центральная площадь
			if((int)date("H") > 5 && (int)date("H") < 22)
			{
				if ($ZIMA) {
					$fon = 'zima_cap_cp_day';
				} else if ($LETO) {
					$fon = 'capquare_day';
				} else if ($VESNA) {
					$fon = 'vesna_cap_cp_day';
				} else if ($OSEN) {
					$fon = 'osen_cp_bg_day2';
				}

			}
			else
			{
				if ($ZIMA) {
					$fon = 'zima_cap_cp_night';
				} else if ($LETO) {
					$fon = 'capquare_night3';
				} else if ($VESNA) {
					$fon = 'vesna_cap_cp_night';
				} else if ($OSEN) {
					$fon = 'osen_cp_bg_night2';
				}
			}

		echo "<table width=1><tr><td>";

		if ($BAR_SHOW)
			{
			echo "<div style=\"position:relative;left: 0px;top: 0px;\" id=\"bar_box\" name=\"bar_box\">";
			progress_bar_city(1);
			echo "</div>";
			}

		echo "<div style=\"position:relative;\"><div style=\"position:relative; \" id=\"ione\" name=\"ione\">";





		if($ZIMA) //новогодний снег :)
		{
			echo "<img src=\"http://i.oldbk.com/i/snow_transp.gif\" alt=\"\" border=\"0\" style=\"position:absolute;\"  />";
		}


      //летучие мыши если осень и ночь
      if 	($fon == 'osen_cp_bg_night2')
        {
        if((time()>=$_tykva_start) and (time()<=$_tykva_end))
          {
          echo '<script type="text/javascript" src="//i.oldbk.com/i/js/bats.js"></script> ';
          }
        }


		echo "<img src=\"http://i.oldbk.com/i/city/",$fon,".jpg\" alt=\"\" border=\"0\"/>";

		if ($ZIMA) {
			buildsetPNG(1,"zima_club",30,235,"Бойцовский клуб");
			buildsetPNG(4,"zima_rem",202,290,"Ремонтная мастерская");
			buildsetPNG(10,"zima_berezka",205,435, "Магазин 'Березка'");
			buildsetPNG(6,"zima_po4ta",180,540,"Почта");
	    		buildsetPNG(66,"zima_cap_arr_top",180,650,"Торговая улица");
			if(!(time()>$ny_events['elkacpstart'] && time() < $ny_events['elkacpend'])) {
	  			buildsetPNG(14,"statue_png",222,365,"Памятник");
			}

			buildsetPNG(2,"zima_shop",202,171,"Магазин");
			buildsetPNG(3,"zima_kom",205,105,"Первый комиссионный магазин");
			buildsetPNG(61,"zima_stella",260,530,"Доска объявлений");
			buildsetPNG(11,"zima_loto",230,615,"Лотерея Сталкеров");
			buildsetPNG(8,"zima_cap_arr_left",258,21,"Большая парковая улица");
			buildsetPNG(7,"zima_cap_arr_right",260,710,"Страшилкина улица");


		} elseif ($LETO) {
			buildsetPNG(1,"2clubb_png",30,235,"Бойцовский клуб");
			buildsetPNG(4,"rem_png",202,290,"Ремонтная мастерская");
			buildsetPNG(10,"berezka_png",205,435, "Магазин 'Березка'");
			buildsetPNG(6,"po4ta_png",180,540,"Почта");
	    		buildsetPNG(66,"cp_u2_png",180,650,"Торговая улица");

/*
if ((in_array($user['level'],$KO_A_ARRA6)) and ((time()>$KO_start_time6-14400) and (time()<$KO_fin_time6-14400)) )
		{
		//акция активна
			if((int)date("H") > 5 && (int)date("H") < 22)
			{
			buildsetGIF(5,"action_stat5",222,365,"Памятник");
			}
			else
			{
			buildsetGIF(5,"action_stat5n",222,365,"Памятник");
			}
		}
		else*/
			$_datetime = new DateTime();
			$_startaugust = new DateTime('2016-08-10 00:00:00');
			$_endaugust = new DateTime('2016-09-01 00:00:00');
			if(($_datetime >= $_startaugust && $_datetime <= $_endaugust) || ADMIN) {
				buildsetPNG(14,"flowersseller",222,354,"Цветочница",array(
					'url' => '/action/quest/daily',
					'response_type' => 'json'
				));
			} else {
				buildsetPNG(14,"statue_png",222,365,"Памятник");;
			}

			buildsetPNG(2,"gshop_png",202,171,"Магазин");
			buildsetPNG(3,"kom_png",205,105,"Первый комиссионный магазин");
			buildsetPNG(61,"stella_png",260,530,"Доска объявлений");
			buildsetPNG(11,"loto_png",230,615,"Лотерея Сталкеров");
			buildsetPNG(8,"arr_left_png",258,21,"Большая парковая улица");
			buildsetPNG(7,"arr_right_png",260,710,"Страшилкина улица");
		} elseif ($VESNA) {
			buildsetPNG(1,"vesna_cap_club",30,235,"Бойцовский клуб");
			buildsetPNG(4,"vesna_cap_rem",202,290,"Ремонтная мастерская");
			buildsetPNG(10,"vesna_cap_berezka",205,435, "Магазин 'Березка'");
			buildsetPNG(6,"vesna_cap_po4ta",180,540,"Почта");
	    		buildsetPNG(66,"cp_u2_png",180,650,"Торговая улица");

			if(time() <= 1457470799) {
				buildsetPNG(14,"flowersseller",222,354,"Цветочница",array(
						'url' => '/action/quest/quest8',
						'response_type' => 'json'
				));
			} else {
				buildsetPNG(14,"vesna_cap_statue",222,365,"Памятник");;
			}
			buildsetPNG(2,"vesna_cap_shop",202,171,"Магазин");

			buildsetPNG(3,"vesna_cap_kom",205,105,"Первый комиссионный магазин");
			buildsetPNG(61,"vesna_cap_stella",260,530,"Доска объявлений");
			buildsetPNG(11,"vesna_cap_loto",230,615,"Лотерея Сталкеров");
			buildsetPNG(8,"arr_left_png",258,21,"Большая парковая улица");
			buildsetPNG(7,"arr_right_png",260,710,"Страшилкина улица");
		} elseif ($OSEN) {



			buildsetPNG(1,"osen_club",30,235,"Бойцовский клуб");
			buildsetPNG(4,"osen_rem",202,290,"Ремонтная мастерская");
			buildsetPNG(10,"osen_berezka",205,435, "Магазин 'Березка'");
			buildsetPNG(6,"osen_po4ta",180,540,"Почта");
	    		buildsetPNG(66,"cp_u2_png",180,650,"Торговая улица");
  			buildsetPNG(14,"osen_statue",222,365,"Памятник");
			buildsetPNG(2,"osen_shop",202,171,"Магазин");
			buildsetPNG(3,"osen_kom",205,105,"Первый комиссионный магазин");
			buildsetPNG(61,"osen_stella",260,530,"Доска объявлений");
			buildsetPNG(11,"osen_loto",230,615,"Лотерея Сталкеров");
			buildsetPNG(8,"arr_left_png",258,21,"Большая парковая улица");
			buildsetPNG(7,"arr_right_png",260,710,"Страшилкина улица");

      if((time()>=$_tykva_start) and (time()<=$_tykva_end))
        {
    	   buildsetPNG(99,"tykva11",248,286,"Тыква");
         }
		}

		if( (time()>$ny_events['elkacpstart'] && time() < $ny_events['elkacpend']))
		{
			buildsetGIF(2014,"tree2",150,325,"Новогодняя елка");
		}


		if((time()>$snegovik_start && time()<$snegovik_stop))
		{

			$count=mysql_fetch_assoc(mysql_query("SELECT sum(cnt) as s FROM ".$db_city[CITY_ID]."`newyear_snowman` "));


			if($count[s]>=$count_bols_st1[CITY_ID][3] )
			{
				$img='sneg_3';
				$name='Снеговик';
				//$alert="<script>alert('Меня построили из ".$count[s]." снежков!')</script>";
				$x=140;
				$y=243;
			}
			else
			if($count[s]>$count_bols_st1[CITY_ID][2] )
			{
				$img='sneg_3';
				$name='Снеговик';
				$alert=null;
				$x=140;
				$y=243;
			}
			else
			if($count[s]>$count_bols_st1[CITY_ID][1] )
			{
				$img='snow_ball2';
				$name='Снежный ком';
				$alert=null;
				$x=140;
				$y=261;
			}
			else
			if($count[s]>$count_bols_st1[CITY_ID][0] )
			{
				$img='snow_ball1';
				$name='Снежный ком';
				$alert=null;
				$x=140;
				$y=275;
			}
			else
			{
				$img='sneg_0';
				$name='Снег';
				$alert=null;
				$x=140;
				$y=277;

			}
			buildsetPNG(55,$img,$y,$x,$name);
		}

	echo "</td></tr></table>";
	echo '<div id=hint3 class=ahint></div>';
}
elseif ($user['room'] == 21)
{
//Страшилкина улица
			if((int)date("H") > 5 && (int)date("H") < 22)
			{
				if ($ZIMA) {
					$fon = 'zima_cap_strash_day';
				} elseif ($LETO) {
					$fon = 'strr_day';
				} elseif ($VESNA) {
					$fon = 'vesna_cap_strash_day';
				} elseif ($OSEN) {
					$fon = 'osen_strah_bg_day2';
				}
			 }
			 else
			 {
				if ($ZIMA) {
					$fon = 'zaim_cap_strash_night';
				} elseif ($LETO) {
					$fon = 'strr_night';
				} elseif ($VESNA) {
					$fon = 'vesna_cap_strash_night';
				} elseif ($OSEN) {
					$fon = 'osen_strah_bg_night2';
				}

			 }
	echo "<table width=1><tr><td>";

	if ($BAR_SHOW)
			{
			echo "<div style=\"position:relative;left: 0px;top: -20px;\" id=\"bar_box\" name=\"bar_box\">";
			progress_bar_city(1);
			echo "</div>";
			}

	echo "<div style=\"position:relative; \" id=\"ione\">";



	if($ZIMA) //новогодний снег :)
		{
			echo "<img src=\"http://i.oldbk.com/i/snow_transp.gif\" alt=\"\" border=\"0\" style=\"position:absolute;\"  />";
		}



	echo "<img src=\"http://i.oldbk.com/i/city/",$fon,".jpg\" alt=\"\" border=\"0\"/>";

	if ($ZIMA) {
		buildsetPNG(4,"zima_cap_arr_left",258,21,"Центральная площадь");
		buildsetPNG(2,"zima_cap_registratura",170,113,"Регистратура кланов");
		buildsetPNG(0,"zima_cap_tree",165,20,"Дерево");
		buildsetPNG(77,"zima_cap_tower",5,315,"Башня смерти");
		buildsetPNG(5,"zima_cap_bank",180,485,"Банк");
		buildsetPNG(6,"zima_cap_flowershop",220,613,"Цветочный магазин");
		buildsetPNG(3200,"zima_cap_arr_right",258,708,"Ристалище");
	} elseif ($LETO) {
		buildsetPNG(4,"arr_left_png",258,21,"Центральная площадь");
		buildsetPNG(2,"clans_reg_png",170,113,"Регистратура кланов");

		require_once('config_ko.php');

		if ($user['klan']=='radminion')
			{
			$KO_start_time22=time()-1;
			$KO_fin_time22=time()+1;
			}

		if ( ((time()>$KO_start_time22) and (time()<$KO_fin_time22))  )
			{
			buildsetPNG(777,"ftree_png",165,20,"Дерево Желаний");
			}
			else
			{
			buildsetPNG(0,"tree_png",165,20,"Дерево");
			}

		buildsetPNG(77,"bs_png",5,315,"Башня смерти");
		buildsetPNG(5,"bank_png",180,485,"Банк");
		//buildsetPNG(3311,"prison",145,635,"Темница");
		buildsetPNG(6,"fl_shop_png",220,613,"Цветочный магазин");
		buildsetPNG(3200,"arr_right_png",258,708,"Ристалище");
	} elseif ($VESNA) {
		buildsetPNG(4,"arr_left_png",258,21,"Центральная площадь");
		buildsetPNG(2,"vesna_cap_registratura",170,113,"Регистратура кланов");
		buildsetPNG(0,"tree_png",165,20,"Дерево");
		buildsetPNG(77,"vesna_cap_tower",5,315,"Башня смерти");

		buildsetPNG(5,"vesna_cap_bank",180,485,"Банк");
		buildsetPNG(6,"vesna_cap_flowershop",220,613,"Цветочный магазин");

		buildsetPNG(3200,"arr_right_png",258,708,"Ристалище");
	} elseif ($OSEN) {
		buildsetPNG(4,"arr_left_png",258,21,"Центральная площадь");
		buildsetPNG(2,"osen_registratura",170,113,"Регистратура кланов");
	//	buildsetPNG(0,"tree_png",165,20,"Дерево");
	if ($user['klan']=='radminion') $KO_start_time42=time()-1;
			if ( ((time()>$KO_start_time42) and (time()<$KO_fin_time42))  )
			{
			buildsetPNG(777,"autumn_ftree",165,20,"Дерево Желаний");
			}
			else
			{
			buildsetPNG(0,"autumn_tree",165,20,"Дерево");
			}

		buildsetPNG(77,"osen_tower",5,315,"Башня смерти");

		buildsetPNG(5,"osen_bank",180,485,"Банк");
		buildsetPNG(6,"osen_flowershop",220,613,"Цветочный магазин");
		buildsetPNG(3200,"arr_right_png",258,708,"Ристалище");
	}



	echo "</td></tr></table>";
}
elseif ($user['room'] == 26)
{
//Парковая улица

			if((int)date("H") > 5 && (int)date("H") < 22)
			{
				if ($ZIMA) {
					$fon = 'winter_park_day.jpg';
				} elseif ($LETO) {
					$fon = 'summer_park_day.jpg';
				} elseif ($VESNA) {
					$fon = 'spring_park_day.jpg';
				} elseif ($OSEN) {
					$fon = 'autumn_park_day.jpg';
				}
			}
			else
			{
				if ($ZIMA) {
					$fon = 'winter_park_night.jpg';
				} elseif ($LETO) {
					$fon = 'summer_park_night.jpg';
				} elseif ($VESNA) {
					$fon = 'spring_park_night.jpg';
				} elseif ($OSEN) {
					$fon = 'autumn_park_night.jpg';
				}
			}

	echo "<table width=1><tr><td>";

	if ($BAR_SHOW)
			{
			echo "<div style=\"position:relative;left: 0px;top: 0px;\" id=\"bar_box\" name=\"bar_box\">";
			progress_bar_city(1);
			echo "</div>";
			}

	echo "<div style=\"position:relative; \" id=\"ione\">";


	if($ZIMA) //новогодний снег :)
		{
			echo "<img src=\"http://i.oldbk.com/i/snow_transp.gif\" alt=\"\" border=\"0\" style=\"position:absolute;\"  />";
		}
	echo "<img src=\"http://i.oldbk.com/i/city/new/park/".$fon."\" alt=\"\" border=\"0\"/>";

	if ($ZIMA) {
		buildsetPNG(191,"zima_cap_arr_top",188,645,"Улица Мастеров");
		buildsetPNG(3,"zima_cap_arr_left",259,27,"Замковая площадь");
		buildsetPNG(4,"zima_cap_arr_right",259,715,"Центральная площадь");
	} elseif ($LETO) {
		buildsetPNG(191,"cp_u2_png",188,645,"Улица Мастеров");
		buildsetPNG(3,"arr_left_png",259,27,"Замковая площадь");
		buildsetPNG(4,"arr_right_png",259,715,"Центральная площадь");
	} elseif ($VESNA) {
		buildsetPNG(191,"cp_u2_png",188,645,"Улица Мастеров");
		buildsetPNG(3,"arr_left_png",259,27,"Замковая площадь");
		buildsetPNG(4,"arr_right_png",259,715,"Центральная площадь");
	} elseif ($OSEN) {
		buildsetPNG(191,"cp_u2_png",188,645,"Улица Мастеров");
		buildsetPNG(3,"arr_left_png",259,27,"Замковая площадь");
		buildsetPNG(4,"arr_right_png",259,715,"Центральная площадь");
	}
	?>

	<img width=198 height=162 style="cursor:pointer; z-index:3; position: absolute; left: 70px; top: 150px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Ярмарка" title="Ярмарка" onmouseover="this.src='http://i.oldbk.com/i/city/new/park/hover_fair.png'" onmouseout="this.src='http://i.oldbk.com/i/map/empty_gif.gif'" OnClick="solo(72);" />
	<img width=127 height=111 style="cursor:pointer; z-index:3; position: absolute; left: 278px; top: 108px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Городские ворота" title="Городские ворота" onmouseover="this.src='http://i.oldbk.com/i/city/new/park/hover_gate.png'" onmouseout="this.src='http://i.oldbk.com/i/map/empty_gif.gif'" OnClick="solo(10);" />
	<img width=162 height=99 style="cursor:pointer; z-index:3; position: absolute; left: 494px; top: 146px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Знахарь" title="Знахарь" onmouseover="this.src='http://i.oldbk.com/i/city/new/park/hover_znahar.png'" onmouseout="this.src='http://i.oldbk.com/i/map/empty_gif.gif'" OnClick="solo(5);" />

	<img width=78 height=52 style="cursor:pointer; z-index:3; position: absolute; left: 627px; top: 252px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Большая скамейка" title="Большая скамейка" onmouseover="this.src='http://i.oldbk.com/i/city/new/park/hover_3sk.png'" onmouseout="this.src='http://i.oldbk.com/i/map/empty_gif.gif'" OnClick="solo(6);" />
	<img width=65 height=47 style="cursor:pointer; z-index:3; position: absolute; left: 416px; top: 252px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Средняя скамейка" title="Средняя скамейка" onmouseover="this.src='http://i.oldbk.com/i/city/new/park/hover_2sk.png'" onmouseout="this.src='http://i.oldbk.com/i/map/empty_gif.gif'" OnClick="solo(7);" />
	<img width=35 height=26 style="cursor:pointer; z-index:3; position: absolute; left: 481px; top: 232px;" src="http://i.oldbk.com/i/map/empty_gif.gif" alt="Маленькая скамейка" title="Маленькая скамейка" onmouseover="this.src='http://i.oldbk.com/i/city/new/park/hover_1sk.png'" onmouseout="this.src='http://i.oldbk.com/i/map/empty_gif.gif'" OnClick="solo(8);" />

	<?php
	echo "</td></tr></table>";
}
elseif ($user['room'] == 191)
{
//улица мастеров
			if((int)date("H") > 5 && (int)date("H") < 22)
			{
				if ($ZIMA) {
					$fon = 'winter_locbg_master_day_notree3';
				} elseif ($LETO) {
					$fon = 'summer_locbg_master_day_notree3';
				} elseif ($VESNA) {
					$fon = 'spring_locbg_master_day_notree3';
				} elseif ($OSEN) {
					$fon = 'autumn_locbg_master_day_notree3';
				}
			}
			else
			{
				if ($ZIMA) {
					$fon = 'winter_locbg_master_night_notree3';
				} elseif ($LETO) {
					$fon = 'summer_locbg_master_night_notree3';
				} elseif ($VESNA) {
					$fon = 'spring_locbg_master_night_notree3';
				} elseif ($OSEN) {
					$fon = 'autumn_locbg_master_night_notree3';
				}
			}

	echo "<table width=1><tr><td>";

	if ($BAR_SHOW)
			{
			echo "<div style=\"position:relative;left: 0px;top: 0px;\" id=\"bar_box\" name=\"bar_box\">";
			progress_bar_city(1);
			echo "</div>";
			}
	echo "<div style=\"position:relative; \" id=\"ione\">";



	if($ZIMA) {
		echo "<img src=\"http://i.oldbk.com/i/snow_transp.gif\" alt=\"\" border=\"0\" style=\"position:absolute;\"  />";
	}
	/*
	if($OSEN) // временный листопад
	{
		echo "<img src=\"http://i.oldbk.com/i/osen-listva-temp.png\" alt=\"\" border=\"0\" style=\"position:absolute;\"  />";
	}
	*/

	echo "<img usemap=\"#master\" src=\"http://i.oldbk.com/i/city/",$fon,".jpg\" alt=\"\" border=\"0\"/>";
	echo '<script>
	function mapHover(id) {
		obj = document.getElementById(id);
		obj.style.display = "";
	}
	function mapLeave(id) {
		obj = document.getElementById(id);
		obj.style.display = "none";
	}
	</script>';


	if ($ZIMA) {
		$path = "winter";
		buildsetPNG(8,"zima_cap_arr_left",258,21,"Большая парковая улица");
		buildsetPNG(0,"tree_master_winter_day",221,21,"Дерево");
	} elseif ($LETO) {
		$path = "summer";
		buildsetPNG(8,"arr_left_png",258,21,"Большая парковая улица");
		buildsetPNG(0,"tree_master_summer_day",221,21,"Дерево");
	} elseif ($VESNA) {
		$path = "spring";
		buildsetPNG(8,"arr_left_png",258,21,"Большая парковая улица");
		buildsetPNG(0,"tree_master_spring_day",221,21,"Дерево");
	} elseif ($OSEN) {
		$path = "autumn";
		buildsetPNG(8,"arr_left_png",258,21,"Большая парковая улица");
		buildsetPNG(0,"tree_master_autumn_day",221,21,"Дерево");
	}

	?>

		<style>
			.img-holder {
				position: absolute;
				left: 50%;
				top: 0;
				margin-left: -390px;
				width: 780px;
			}
			.img-holder img {
				position: relative;
				z-index: 2;
			}
			.action-list {
				list-style: none;
				position: absolute;
				margin: 0;
				padding: 0;
				top: 0;
				left: 0;
				height: 100%;
				width: 100%;
			}
			.action-list li {
				display: none;
				position: absolute;
				background: 0 0 no-repeat;
			}
			.action-list .tavern-hover {
				background-image: url("http://i.oldbk.com/i/city/sub/master_<?=$path;?>_tavern_hover_2.png");
				top: 145px;
				left: 65px;
				width: 177px;
				height: 160px;
			}
			.action-list .lab-hover {
				background-image: url("http://i.oldbk.com/i/city/sub/master_<?=$path;?>_magiclab_hover_2.png");
				top: 93px;
				left: 214px;
				width: 177px;
				height: 160px;
			}
			.action-list .smithy-hover {
				background-image: url("http://i.oldbk.com/i/city/sub/master_<?=$path;?>_smithy_hover_2.png");
				top: 175px;
				left: 357px;
				width: 177px;
				height: 160px;
			}
			.action-list .jeweler-hover {
				background-image: url("http://i.oldbk.com/i/city/sub/master_<?=$path;?>_jeweler_hover_2.png");
				top: 87px;
				left: 502px;
				width: 177px;
				height: 160px;
			}
			.action-list .woodworker-hover {
				background-image: url("http://i.oldbk.com/i/city/sub/master_<?=$path;?>_woodworker_hover_2.png");
				top: 178px;
				left: 590px;
				width: 177px;
				height: 160px;
			}
			.action-list .armorer-hover {
				background-image: url("http://i.oldbk.com/i/city/sub/master_armorer_hover_2.png");
				top: 52px;
				left: 346px;
				width: 177px;
				height: 160px;
			}
		</style>
		<script>
			$(document).ready(function () {
				var areaAction = function ($target, action) {
					var areaClass = $.trim($target.attr('class')).replace(/^area\-/i, '');
					$('.' + areaClass + '-hover')[action]('fast');
				};

				$('map area').hover(
					function (e) {
						$('.action-list li').hide();
						areaAction($(e.target), 'fadeIn');
					},
					function (e) {
						areaAction($(e.target), 'fadeOut');
					}
				);
			});
		</script>
		<div class="img-holder">
			<?php
			if ($OSEN) {
			?>
				<img src="http://i.oldbk.com/i/autumn_leaves.png" alt="" border="0" style="position:absolute;left:0px;top:0px;" usemap="#map">
			<?php } else {
				?>
				<img src="http://i.oldbk.com/i/1pxgif.gif" width="780" height="330" usemap="#map" style="z-index:1">
			<?php
			} ?>
			<ul class="action-list">
				<li class="armorer-hover"></li>
				<li class="smithy-hover"></li>
				<li class="lab-hover"></li>
				<li class="tavern-hover"></li>
				<li class="jeweler-hover"></li>
				<li class="woodworker-hover"></li>
			</ul>
		</div>
		<map name="map">
			<area href="?got=1&level96=1" shape="rect" class="area-armorer" coords="346,52,523,212" alt="Башня оружейников" title="Башня оружейников ">
			<area href="?got=1&level91=1" shape="rect" class="area-smithy" coords="357,175,534,335" alt="Кузница" title="Кузница">
			<area href="?got=1&level92=1" shape="rect" class="area-tavern" coords="65,145,242,305" alt="Таверна" title="Таверна">
			<area href="?got=1&level93=1" shape="rect" class="area-lab" coords="214,93,391,253" alt="Лаборатория магов и алхимиков" title="Лаборатория магов и алхимиков">
			<area href="?got=1&level94=1" shape="rect" class="area-jeweler" coords="502,87,679,247" alt="Мастерская ювелиров и портных" title="Мастерская ювелиров и портных">
			<area href="?got=1&level95=1" shape="rect" class="area-woodworker" coords="590,178,767,338" alt="Мастерская плотника" title="Мастерская плотника ">
		</map>
	<?


	echo "</td></tr></table>";
}
elseif ($user['room'] == 50)
{
//Замковая улица
			if((int)date("H") > 5 && (int)date("H") < 22)
			{
				if ($ZIMA) {
					$fon = 'zima_cap_zamk_day';
				} elseif ($LETO) {
					$fon = 'zmkv_day';
				} elseif ($VESNA) {
					$fon = 'vesna_cap_zamk_day';
				} elseif ($OSEN) {
					$fon = 'osen_zamk_bg_day2';
				}
			}
			else
			{
				if ($ZIMA) {
					$fon = 'zima_cap_zamk_night';
				} elseif($LETO) {
					$fon = 'zmkv_night';
				} elseif($VESNA) {
					$fon = 'vesna_cap_zamk_night';
				} elseif($OSEN) {
					$fon = 'osen_zamk_bg_night2';
				}
			}

	echo "<table width=1><tr><td>";

	if ($BAR_SHOW)
			{
			echo "<div style=\"position:relative;left: 0px;top: 0px;\" id=\"bar_box\" name=\"bar_box\">";
			progress_bar_city(1);
			echo "</div>";
			}

	echo "<div style=\"position:relative; \" id=\"ione\">";


	if($ZIMA) //новогодний снег :)
		{
			echo "<img src=\"http://i.oldbk.com/i/snow_transp.gif\" alt=\"\" border=\"0\" style=\"position:absolute;\"  />";
		}
	echo "<img src=\"http://i.oldbk.com/i/city/",$fon,".jpg\" alt=\"\" border=\"0\"/>";

	if ($ZIMA) {
		buildsetPNG(60,"zima_cap_arr_left",258,21,"Арена Богов");
		buildsetPNG(1,"zima_cap_ruins",166,48,"Руины Старого Замка");
		buildsetPNG(45,"zima_cap_lab",130,327,"Вход в Лабиринт Хаоса");
		buildsetPNG(48,"zima_cap_lavka",240,425,"Храмовая лавка");
		buildsetPNG(49,"zima_cap_hram",173,550,"Храм Древних");
		buildsetPNG(4,"zima_cap_arr_right",260,710,"Большая парковая улица");
	} elseif ($LETO) {
		buildsetPNG(60,"arr_left_png",258,21,"Арена Богов");
		buildsetPNG(1,"ruins_png",166,48,"Руины Старого Замка");
		buildsetPNG(45,"lab_png",130,327,"Вход в Лабиринт Хаоса");
		buildsetPNG(48,"lavka_png",240,425,"Храмовая лавка");
		buildsetPNG(49,"hram_png",173,550,"Храм Древних");
		buildsetPNG(4,"arr_right_png",260,710,"Большая парковая улица");
	} elseif ($VESNA) {
		buildsetPNG(60,"arr_left_png",258,21,"Арена Богов");
		buildsetPNG(1,"vesna_cap_ruins",166,48,"Руины Старого Замка");
		buildsetPNG(45,"vesna_cap_lab",130,327,"Вход в Лабиринт Хаоса");
		buildsetPNG(48,"vesna_cap_lavka",240,425,"Храмовая лавка");
		buildsetPNG(49,"vesna_cap_hram",173,550,"Храм Древних");
		buildsetPNG(4,"arr_right_png",260,710,"Большая парковая улица");
	} elseif ($OSEN) {
		buildsetPNG(60,"arr_left_png",258,21,"Арена Богов");
		buildsetPNG(1,"osen_ruins",166,48,"Руины Старого Замка");
		buildsetPNG(45,"osen_lab",130,327,"Вход в Лабиринт Хаоса");
		buildsetPNG(48,"osen_lavka",240,425,"Храмовая лавка");
		buildsetPNG(49,"osen_hram",173,550,"Храм Древних");
		buildsetPNG(4,"arr_right_png",260,710,"Большая парковая улица");
	}

	echo "</td></tr></table>";
}
elseif ($user['room'] == 66)
{
//Торговая улица
			if((int)date("H") > 5 && (int)date("H") < 22)
			{
				if ($ZIMA) {
					$fon = 'zima_cap_torg_day';
				} elseif ($LETO) {
					$fon = 'torg_bg_day2';
				} elseif ($VESNA) {
					$fon = 'vesna_cap_torg_day';
				} elseif ($OSEN) {
					$fon = 'osen_torg_bg_day2';
				}
			}
			else
			{
				if ($ZIMA) {
					$fon = 'zima_cap_torg_night';
				} elseif ($LETO) {
					$fon = 'torg_bg_night2';
				} elseif ($VESNA) {
					$fon = 'vesna_cap_torg_night';
				} elseif ($OSEN) {
					$fon = 'osen_torg_bg_night2';
				}
			}
	echo "<table width=1><tr><td>";

	if ($BAR_SHOW)
			{
			echo "<div style=\"position:relative;left: 0px;top: 0px;\" id=\"bar_box\" name=\"bar_box\">";
			progress_bar_city(1);
			echo "</div>";
			}

	echo "<div style=\"position:relative; \" id=\"ione\">";



	if($ZIMA) //новогодний снег :)
		{
			echo "<img src=\"http://i.oldbk.com/i/snow_transp.gif\" alt=\"\" border=\"0\" style=\"position:absolute;\"  />";
		}
	echo "<img src=\"http://i.oldbk.com/i/city/",$fon,".jpg\" alt=\"\" border=\"0\"/>";

	if ($ZIMA) {
		buildsetPNG(88,"zima_cap_prokat",155,480,"Прокатная лавка");
		buildsetPNG(71,"t_build4",120,300,"Аукцион");
		buildsetPNG(70,"zima_cap_lombard",150,565,"Ломбард");
		buildsetPNG(47,"zima_cap_arenda",175,70,"Арендная лавка");
		buildsetPNG(20,"zima_cap_arr_left",259,25,"Центральная площадь");
		buildsetPNG(102,"zima_cap_stop",259,720,"Проход закрыт");
		buildsetPNG(67,"zima_cap_fontan",210,350,"Фонтан Удачи");
	} elseif ($LETO) {
		buildsetPNG(88,"nprokat_png",155,480,"Прокатная лавка");
		buildsetPNG(71,"t_build4",120,298,"Аукцион");
		buildsetPNG(70,"lmbrd_png",150,565,"Ломбард");
		buildsetPNG(47,"auk_png",175,70,"Арендная лавка");
		buildsetPNG(20,"cap_rist_arr_left",259,25,"Центральная площадь");
		buildsetPNG(102,"stop_png",259,720,"Проход закрыт");
		buildsetPNG(67,"fontan",210,350,"Фонтан Удачи");
	} elseif ($VESNA) {
		buildsetPNG(88,"vesna_cap_build3",155,480,"Прокатная лавка");
		buildsetPNG(71,"t_build4",120,300,"Аукцион");
		buildsetPNG(70,"vesna_cap_build2",150,565,"Ломбард");
		buildsetPNG(47,"vesna_cap_build1",175,70,"Арендная лавка");
		buildsetPNG(20,"arr_left_png",259,25,"Центральная площадь");
		buildsetPNG(102,"stop_png",259,720,"Проход закрыт");
		buildsetPNG(67,"vesna_cap_fontan",210,350,"Фонтан Удачи");
	} elseif ($OSEN) {
		buildsetPNG(88,"osen_build3",155,480,"Прокатная лавка");
		buildsetPNG(71,"t_build4",120,300,"Аукцион");
		buildsetPNG(70,"osen_build2",150,565,"Ломбард");
		buildsetPNG(47,"osen_build1",175,70,"Арендная лавка");
		buildsetPNG(20,"arr_left_png",259,25,"Центральная площадь");
		buildsetPNG(102,"stop_png",259,720,"Проход закрыт");
		buildsetPNG(67,"osen_fontan",210,350,"Фонтан Удачи");
	}

	echo "</td></tr></table>";
}
}
//RENDER AVALON
if (CITY_ID==1)
{
	if ($user['room'] == 20) {
//Центральная площадь

		if((int)date("H") > 5 && (int)date("H") < 22)
		{
			$fon = 'cp_avalon_city_day';
		}
		else
		{
			$fon = 'cp_avalon_city_night';
		}

		echo "<table width=1><tr><td><div style=\"position:relative; \" id=\"ione\"><img src=\"http://i.oldbk.com/i/city/",$fon,".jpg\" alt=\"\" border=\"0\"/>";
		buildsetPNG(1,"avalon_club",10,282,"Бойцовский клуб");
		buildsetPNG(4,"ava_repeir",205,560,"Ремонтная мастерская");
		buildsetPNG(10,"ava_berezka",120,605, "Магазин 'Березка'");
		buildsetPNG(6,"ava_post",172,485,"Почта");
		//buildsetPNG(99,"tykva11",220,265,"Тыква");
		buildsetPNG(2,"ava_shop",190,90,"Магазин");
		if ($comis_fire)
		{
		//buildsetPNG(3,"komok_fire_avalon",115,1,"Первый комиссионный магазин разрушен");
		buildsetPNG(3,"ava_kom_dead",115,1,"Первый комиссионный магазин разрушен");
		}
		else
		{
		buildsetPNG(3,"ava_kom",115,1,"Первый комиссионный магазин");
		}

		buildsetPNG(61,"av_board",250,507,"Доска объявлений");
		buildsetPNG(8,"ava_st_left",225,21,"Большая парковая улица");
		buildsetPNG(7,"ava_st_right",225,710,"Страшилкина улица");

		if((time()>$elka_render_start && time()<$elka_render_stop))
		{
			buildsetGIF(2013,"elka_ava_2",133,288,"Новогодняя елка");
		}


		if((time()>$snegovik_start && time()<$snegovik_stop))
		{
			$count=mysql_fetch_assoc(mysql_query("SELECT sum(cnt) as s FROM ".$db_city[CITY_ID]."`newyear_snowman` "));

			if($count[s]>=$count_bols_st1[CITY_ID][3] )
			{
				$img='sneg_3';
				$name='Снеговик';
				//$alert="<script>alert('Меня построили из ".$count[s]." снежков!')</script>";
				$x=425;
				$y=210;
			}
			else
			if($count[s]>$count_bols_st1[CITY_ID][2] )
			{
				$img='sneg_3';
				$name='Снеговик';
				$alert=null;
				$x=425;
				$y=210;
			}
			else
			if($count[s]>$count_bols_st1[CITY_ID][1] )
			{
				$img='snow_ball2';
				$name='Снежный ком';
				$alert=null;
				$x=425;
				$y=228;
			}
			else
			if($count[s]>$count_bols_st1[CITY_ID][0] )
			{
				$img='snow_ball1';
				$name='Снежный ком';
				$alert=null;
				$x=425;
				$y=242;
			}
			else
			{
				$img='sneg_0';
				$name='Снег';
				$alert=null;
				$x=425;
				$y=245;

			}
			buildsetPNG(55,$img,$y,$x,$name);
		}

	echo "</td></tr></table>";
	echo '<div id=hint3 class=ahint></div>';
}
elseif ($user['room'] == 21)
{
//Страшилкина улица
			if((int)date("H") > 5 && (int)date("H") < 22)
			{
			 $fon = 'av_srt_day';
			 }
			 else
			 {
			 $fon = 'av_srt_night';
			 }


	echo "<table width=1><tr><td><div style=\"position:relative; \" id=\"ione\"><img src=\"http://i.oldbk.com/i/city/",$fon,".jpg\" alt=\"\" border=\"0\"/>";
	buildsetPNG(4,"ava_st_left",250,21,"Центральная площадь");
	buildsetPNG(2,"av_registratura",182,85,"Закрыто");
	buildsetPNG(16,"av_skamejka",285,430,"Большая скамейка");
	buildsetPNG(7,"av_str_tower",30,270,"Башня смерти");
	buildsetPNG(5,"av_str_bank",185,500,"Банк");
	buildsetPNG(6,"av_str_flowshop",80,660,"Цветочный магазин");
	buildsetPNG(3200,"ava_st_right",250,720,"Ристалище");
	echo "</td></tr></table>";
}
elseif ($user['room'] == 26)
{
	if((int)date("H") > 5 && (int)date("H") < 22)
			{
			 $fon = 'park_avalon_city_day2';
			 }
			 else
			 {
			 $fon = 'park_avalon_city_night3';
			 }
	echo "<table width=1><tr><td><div style=\"position:relative; \" id=\"ione\"><img src=\"http://i.oldbk.com/i/city/",$fon,".jpg\" alt=\"\" border=\"0\"/>";
	buildsetPNG(5,"ava_znahar",155	,565,"Хижина Знахаря");
	buildsetPNG(8,"ava_park_skameika",217,455,"Маленькая скамейка");
	buildsetPNG(22,"ava_vokzal",160,238,"Вокзал");
	buildsetPNG(11,"ava_loto",240,160,"Лотерея");
	buildsetPNG(1,"ava_gate_2",195,20,"Ворота города");
	buildsetPNG(3,"ava_st_left",250,21,"Замковая площадь");
	buildsetPNG(4,"ava_st_right",225,710,"Центральная площадь");
	buildsetPNG(66,"ava_st_right",270,390,"Торговая улица");
	echo "</td></tr></table>";
}
elseif ($user['room'] == 50)
{
//Замковая улица
			if((int)date("H") > 5 && (int)date("H") < 22)
			{
			 $fon = 'av_zamk_day';
			 }
			 else
			 {
			 $fon = 'av_zamk_night';
			 }

	echo "<table width=1><tr><td><div style=\"position:relative; \" id=\"ione\"><img src=\"http://i.oldbk.com/i/city/",$fon,".jpg\" alt=\"\" border=\"0\"/>";
	buildsetPNG(60,"ava_st_left",258,21,"Арена Богов");
	buildsetPNG(1,"av_zamk_ruins",180,8,"Руины Старого Замка");
	buildsetPNG(45,"av_zamk_lab",130,327,"Вход в Лабиринт Хаоса");
	buildsetPNG(48,"ava_zamk_lavka2",220,527,"Храмовая лавка");
	buildsetPNG(49,"av_zamk_hram",160,600,"Храм Короля Артура");
	buildsetPNG(7,"av_skamejka",280,120,"Средняя скамейка");
	buildsetPNG(4,"ava_st_right",260,720,"Большая парковая улица");
	echo "</td></tr></table>";
}
elseif ($user['room'] == 66)
{
//Торговая улица
			if((int)date("H") > 5 && (int)date("H") < 22)
			{
			 $fon = 'av_torg_day';
			}
			else
			{
			 $fon = 'av_torg_night';
			}

	echo "<table width=1><tr><td><div style=\"position:relative; \" id=\"ione\"><img src=\"http://i.oldbk.com/i/city/",$fon,".jpg\" alt=\"\" border=\"0\"/>";
	buildsetPNG(88,"av_torg_prokat",200,260,"Прокатная лавка");
	buildsetPNG(71,"av_torg_aukc",175,570,"Аукцион");
	buildsetPNG(70,"av_torg_lombard",205,20,"Ломбард");
	buildsetPNG(47,"av_torg_arenda",225,390,"Арендная лавка");
	buildsetPNG(20,"ava_st_right",265,725,"Большая парковая улица");
    	buildsetPNG(67,"av_torg_fontan",260,510,"Фонтан Удачи");
	echo "</td></tr></table>";
}

}

?>
</div>
</div>
</TD>
</TR>
</TABLE>
	<?

	if (strlen($msg)) {
		echo '
			<script>
				var n = noty({
					text: "'.addslashes($msg).'",
				        layout: "topLeft2",
				        theme: "relax2",
					type: "'.($typet == "e" ? "error" : "success").'",
				});
			</script>
		';
	}


	?>

<div align=left><? if(!is_array($_SESSION['vk'])) { ?>
<noindex>
<script><?php if(!is_array($_SESSION['vk'])) echo'top.'; else echo'parent.'; ?>changeroom=<?=$user['room']?></script>
<div><!-- Asynchronous Tracking GA bottom piece counter-->
<script type="text/javascript">
(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(ga);
})();
</script>
</div></noindex></div>
<?php } ?>
<!-- Asynchronous Tracking GA bottom piece end -->
<?

if(isset($_SESSION['uid']) && $_SESSION['uid'] == 546433) {
	try {
		$app->applyHook('user.move', $user);
	} catch (Exception $ex) {

	}
}

include "end_files.php";
?>
</BODY>
</HTML>
<?

/////////////////////////////////////////////////////
    if (isset($miniBB_gzipper_encoding)) {
    $miniBB_gzipper_in = ob_get_contents();
    $miniBB_gzipper_inlenn = strlen($miniBB_gzipper_in);
    $miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
    $miniBB_gzipper_lenn = strlen($miniBB_gzipper_out);
    $miniBB_gzipper_in_strlen = strlen($miniBB_gzipper_in);
    $gzpercent = percent($miniBB_gzipper_in_strlen, $miniBB_gzipper_lenn);
    $percent = round($gzpercent);
    $miniBB_gzipper_in = str_replace('<!- GZipper_Stats ->', 'Original size: '.strlen($miniBB_gzipper_in).' GZipped size: '.$miniBB_gzipper_lenn.' Сompression: '.$percent.'%<hr>', $miniBB_gzipper_in);
    $miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
    ob_clean();
    header('Content-Encoding: '.$miniBB_gzipper_encoding);
    echo $miniBB_gzipper_out;
    }
/////////////////////////////////////////////////////

?>
