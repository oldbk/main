<?
//zyavki in lab
		include ("connect.php");
		session_start();
		//ini_set('display_errors','On');
		if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }
		include "functions.php";
		header("Cache-Control: no-cache");

	    unset($_SESSION['quest']); //очистка
     	    unset($_SESSION['questdata']);//очистка
     	    unset($_SESSION['questid']);//очистка


	   if ($_SERVER["SERVER_NAME"]=='capitalcity.oldbk.com') { $cityname='capitalcity' ;  }
	    else if ($_SERVER["SERVER_NAME"]=='avaloncity.oldbk.com') { $cityname='avaloncity' ; $quest_filt=' and ( id > 30 )';  }
		else {$cityname='noname' ; } 


		//$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['uid']}' LIMIT 1;"));
		$labc=mysql_fetch_array(mysql_query("select * from oldbk.`labirint_var` where  `var`= 'labstarttime' and `owner`='".$user[id]."';"));
		//echo "$labc[val]";

		//3600- 1час
		//$regulyator=32400;//9 часов
		//$regulyator=79200;//22 часа
		$regulyator=72000;//20 часов
		$gen_map=true;
		
		$need_bat=10;
		if (($user['level']>=4) and ($user['level']<=9) ) { 		$need_bat=0; }
		
		$bonuseffect = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$user['id']}'  and `type` = '9104' LIMIT 1;")); 
		if ($bonuseffect['id']>0)
			{
			$regulyator-=(int)($regulyator*$bonuseffect['add_info']);
//			$regulyator=50400;//-30%
			$need_bat=(int)($need_bat-($need_bat*$bonuseffect['add_info']));
			}

	if (!(($user['klan']=='radminion')||($user['id']>=690426 and $user['id']<=690429 )))

{
	 $testrist=mysql_fetch_array(mysql_query("select * from oldbk.ristalka where owner={$user['id']}"));
	 if (($testrist['chaos']<$need_bat) and ($testrist['labp']==0) )
	 {
	 $labcmsg="<font color=red>Вы можете посетить \"Лабиринт Хаоса.\"  через ".($need_bat-$testrist['chaos'])." Хаотических боев!</font><br>";
	 }

}
		if ((($labc[val]+$regulyator) > time()  ) and ($labc[val]>0))
		{
		$H=floor(($regulyator-(time()-$labc[val]))/60/60);
		$M=round( (($regulyator-(time()-$labc[val]))/60) - ($H*60) );
		$labcmsg.='До следующего посещения лабиринта:'.$H."ч.".$M." мин.";
		}
		
		/*if ($user['id']==636748)
		{
		$labcmsg="<font color=red>Лабиринт переполнен, приходите позже...<br></font>";
		}*/


		if ($user['lab'] == 1) { header("Location: lab.php"); die();}
		if ($user['lab'] == 2) { header("Location: lab2.php"); die();}
		if ($user['lab'] == 3) { header("Location: lab3.php"); die();}
		if ($user['lab'] == 4) { header("Location: lab4.php"); die();}
		if ($user['room']!=45) { header("Location: main.php"); die();}
		if ($user['battle']!=0) { header("Location: fbattle.php");die();}


if($_GET[clear])
{
$_GET['clear']=(int)$_GET['clear'];

if (($_GET['clear'] > 0) && (($user['align']>1.4 && $user['align']<2) || ($user['align']>2 && $user['align']<3))) {
		if ($user['align']>1.1 && $user['align']<2) {$angel="паладином";}
		if ($user['align']>2 && $user['align']<3) {$angel="Ангелом";}
		mysql_query("UPDATE `labirint_zayav` SET `koment`='Удалено $angel <b>".$user['login']."</b>' WHERE `Id`='{$_GET['clear']}' LIMIT 1;");
	}


}



?>

<html>
<head>
	<link rel=stylesheet type="text/css" href="i/main.css">
    <link rel="stylesheet" href="/i/btn.css" type="text/css">
	<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
	<script type="text/javascript" src="/i/globaljs.js"></script>	
	<style>
		body {
			background-image: url('i/labbg.jpg');
			background-repeat: no-repeat;
			background-position: top right;
		}
		.INPUT {
			width:50px; height:50px;
			BORDER-RIGHT: #b0b0b0 1pt solid; BORDER-TOP: #b0b0b0 1pt solid; MARGIN-TOP: 1px; FONT-SIZE: 10px; MARGIN-BOTTOM: 2px; BORDER-LEFT: #b0b0b0 1pt solid; COLOR: #191970; BORDER-BOTTOM: #b0b0b0 1pt solid; FONT-FAMILY: MS Sans Serif
		}
	</style>
	<script>
		<?
		  if (!($_POST['quest']))
		{
		?>
		
			function refreshPeriodic()
			{
				location.href='startlab.php?';//reload();
								timerID=setTimeout("refreshPeriodic()",30000);
			}
			timerID=setTimeout("refreshPeriodic()",30000);
		<?
		}
		?>

		function returned2(s){
			//if (top.oldlocation != '') { top.frames['main'].navigate(top.oldlocation+'?'+s+'tmp='+Math.random()); top.oldlocation=''; }
			//else {
			//top.frames['main'].location='city.php?'+s+'tmp='+Math.random()
			//}
			location.href='city.php?'+s+'tmp='+Math.random();
		}
	</script>


</head>
<body leftmargin=5 topmargin=0 marginwidth=0 marginheight=0 bgcolor=#e2e0e0 onload="top.setHP(<?=$user['hp']?>,<?=$user['maxhp']?>)">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="1%">&nbsp;</td>
    <td width="65%">

<?
     if(!$_SESSION['beginer_quest'][none])
     {
     	  $last_q=check_last_quest(5);
	      if($last_q)
	      {
	          quest_check_type_5($last_q);
	          //местонахождения
	      }
     }
	 make_quest_div();

		nick($user);
		echo $labcmsg;

		$need_med=true;
		
		$med =str_replace("|",";",$user['medals']); //берем все и открытые и закрытые значки
		$medals = explode(";",$med);
		foreach($medals as $k=>$v)
		{
				if ($v=="011")
				{
				$need_med=false;
				break;
				}
		}
		
		
		if (($need_med) and ($user[rep]>=20000) )
		   {
		   mysql_query("UPDATE `users` SET `medals` = CONCAT('011;',`medals`) WHERE  `id` = '{$user[id]}' ");
		   echo '<br>Вы получили: <img src="http://i.oldbk.com/i/medal_hram_011.gif" onMouseOut="HideOpisShmot()" onMouseOver="OpisShmot(event,\'Рыцарь Лабиринта\')"> ';
		   addchp ('<font color=red>Внимание!</font> Вы получили звание: Рыцарь Лабиринта!','{[]}'.$user['login'].'{[]}');		   
		   }		
		
		
if ((int)($_GET[err])==1)
		{
		echo "<font color=red>Вы в заявке...</font>";
		}


		echo "<div align=right>
		<form method=POST >
		    <div class='btn-control'>
		        <INPUT class='button-dark-mid btn' TYPE=\"button\" value=\"Подсказка\" style=\"background-color:#A9AFC0\" onclick=\"window.open('help/laba.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')\">
		        <input class='button-mid btn' type=submit name=ref value='Обновить'> 
		        <INPUT class='button-mid btn' TYPE=button value=\"Вернуться\" onClick=\"returned2('zp=1&');\">
            </div> 
		    &nbsp;&nbsp;&nbsp;
		    </form>
		    </div>";


		if ($_POST['quest'])
		   {
   		    echo "<div align=left>&nbsp;&nbsp;&nbsp;<form method=POST > 
                <div class='btn-control'>
                    <input class='button-big btn' type=submit name=ref value='Вернуться к заявкам' style=\"background-color:#A9AFC0\">
                </div> 
                
                &nbsp;&nbsp;&nbsp;&nbsp;<br></form></div>";
		   }
		   else {
		    echo "<div align=left>&nbsp;&nbsp;&nbsp;<form method=POST > 
                <div class='btn-control'>
                    <input class='button-big btn' type=submit name=quest value='Взять квестовое задание' style=\"background-color:#A9AFC0\"  >
                </div> 
                &nbsp;&nbsp;&nbsp;&nbsp;<br></form></div>";
		      }


		$online = mysql_query("select count(id) as kol , lab from users where ldate >=".(time()-60)." and lab>0 group by lab;");
		while($lrow=mysql_fetch_array($online))
		 {
		  $in_lab[$lrow[lab]]=$lrow[kol];
		 }
		
		$total=$in_lab[1]+$in_lab[2];
		
		echo "<div align=right>(сейчас в «Лабиринте новичков» <u>online</u>: <b>".(int)($in_lab[3])."</b> чел.) <br></div>";
		echo "<div align=right>(сейчас в «Обычном Лабиринте» <u>online</u>: <b>".(int)($in_lab[1])."</b> чел.) <br></div>";
		echo "<div align=right>(сейчас в «Героическом Лабиринте» <u>online</u>: <b>".(int)($in_lab[2])."</b> чел.) <br></div>";				
		echo "<div align=right>(сейчас в «Легендарном Лабиринте» <u>online</u>: <b>".(int)($in_lab[4])."</b> чел.) <br></div>";						


if ($total>700)
{
	echo "<font color=red>Лабиринт переполнен, приходите позже...<br></font>";
}
else
///////////////////
if ($user[align]==4)
	{
	echo "<font color=red>Хаос не ходит в лабиринт...<br></font>";
	}
else
	if ($_POST['quest'])
	{
		// echo " Получение квестов / завершение квестов / статус квеста: ";


		$qu=mysql_fetch_array(mysql_query("select * from users_quest where owner='{$user[id]}' and status=0 and city='{$cityname}' ;"));

		if   ($qu[id] >0)
		{

			// есть незавершенный квест в этом городе !
			$get_q=mysql_fetch_array(mysql_query("select * from quests where id='{$qu[quest_id]}' ".$quest_filt." ;"));
			if ($get_q[id] >0)
			{
				echo "<b>У Вас есть незавершенное задание:<i>".$get_q[qname]."</i></b><hr>";
				echo "  <b>$get_q[qstart] </b><hr>";
				//подключаем квест файл

				include('./quests/quest'.$get_q[id].'.php');
				// запрос условий завершения
				echo "<b>Выполнено:</b>";
				if (make_qfin($get_q,$user)==true )
				{
					if ($_POST['fin'])
					{
//			echo "Запрос на завершение квеста!!!";
						if (
						mysql_query("UPDATE users_quest SET status=1 where owner={$user[id]} and id='{$qu[id]}' and status=0;")
						)
						{
							$repa=$get_q[rep];

							$ar=0;
							if ($user['prem']>0) $ar += 0.1;

							//дополнительный бонус
							/*
							$eff = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$user['id']}'  and `type` = '9101' ;"));
							if ($eff['id']>0)
								{
								 $ar +=$eff['add_info'];
								}
							*/

							if ($ar>0)
							{
								$repa+=(int)($repa*$ar);
							}



							mysql_query("UPDATE `users` SET `rep`=`rep`+'".$repa."', `repmoney` = `repmoney` + '".$repa."' WHERE `id`='".$user[id]."' LIMIT 1; ");
							echo "<b><i>".$get_q[qfin]."</i></b><br>";
							echo "<font color=red><b>Вы получили:$repa репутации<b></font>";
							mysql_query("INSERT `labirint_var` (`owner`,`var`,`val`) values('".$user[id]."', 'qlab_count_bot', '0' ) ON DUPLICATE KEY UPDATE `val` =0;"); // обнуление счетчика для некоторых квестов надо
							make_qfin_del($get_q,$user);
							$_SESSION['quest']='';
							$_SESSION['questid']='';
							unset($_SESSION['questdata']);
							//пишем в дело удачно окончено
							//+ данные по квесту


							$rec['owner']=$user[id];
							$rec['owner_login']=$user[login];
							$rec['owner_balans_do']=$user[money];
							$rec['owner_rep_do']=$user[repmoney];

							//$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = '{$user[id]}' LIMIT 1;"));

							$rec['owner_balans_posle']=$user[money]+$repa;
							$rec['owner_rep_posle']=$user[repmoney]+$repa;
							$rec['target']=0;
							$rec['target_login']='Вход в лабиринт';
							$rec['type']=382;
							$rec['sum_kr']=($user[money]-$rec['owner_balans_do']);
							$rec['sum_ekr']=0;
							$rec['sum_kom']=0;
							$rec['sum_rep']=$repa;
							$rec['item_id']='';
							$rec['item_name']="";
							$rec['item_count']=0;
							$rec['item_type']=0;
							$rec['item_cost']=0;
							$rec['item_dur']=0;
							$rec['item_maxdur']=0;
							$rec['item_ups']=0;
							$rec['item_unic']=0;
							$rec['item_incmagic']='';
							$rec['item_incmagic_count']='';
							$rec['item_arsenal']='';
							$rec['add_info']='Окончен квест:№'.$get_q[id].'('.$get_q[qname].')';
							add_to_new_delo($rec);


							//удаляем его файлы если есть
							$delfiles='/www/capitalcity.oldbk.com/labmapsq/*-'.$user[id].'.qst';
							foreach (glob($delfiles) as $dfilename)
							{
								unlink($dfilename);
							}

							try {
								$User = new \components\models\User($user);
								$Quest = $app->quest
									->setUser($User)
									->get();
								$Checker = new \components\Component\Quests\check\CheckerEvent();
								$Checker->event_type = \components\Component\Quests\pocket\questTask\EventTask::EVENT_LAB_QUEST_ANY_FINISH;
								if(($Item = $Quest->isNeed($Checker)) !== false) {
									$Quest->taskUp($Item);
								}
								unset($Checker);

							} catch (Exception $ex) {
								$app->logger->addEmergency((string)$ex);
							}

						}


					}
					else
					{
						echo "<form method=POST > <input type=hidden name=fin value='true'><input type=submit name=quest value='Завершить задание'> &nbsp;&nbsp;&nbsp;&nbsp;<br></form>";
					}
				}
				else
				{
					if ($_POST['fin'])
					{
						if (
						mysql_query("UPDATE users_quest SET status=1, count=count-1 where owner={$user[id]} and id='{$qu[id]}' and status=0;")
						)
						{
							echo "<font color=red>Вы отказались от задания....очень жаль...</font>";
							$_SESSION['quest']='';
							$_SESSION['questid']='';
							unset($_SESSION['questdata']);
							mysql_query("INSERT `labirint_var` (`owner`,`var`,`val`) values('".$user[id]."', 'qlab_count_bot', '0' ) ON DUPLICATE KEY UPDATE `val` =0;");
							//удаляем его файлы если есть
							//отказ пишем в дело
							//+ данные по квесту

							$rec['owner']=$user[id];
							$rec['owner_login']=$user[login];
							$rec['owner_balans_do']=$user[money];
							$rec['owner_rep_do']=$user[repmoney];
							$rec['owner_balans_posle']=$user[money];
							$rec['owner_rep_posle']=$user[repmoney];
							$rec['target']=0;
							$rec['target_login']='Вход в лабиринт';
							$rec['type']=383;
							$rec['sum_kr']=0;
							$rec['sum_ekr']=0;
							$rec['sum_kom']=0;
							$rec['sum_rep']=0;
							$rec['item_id']='';
							$rec['item_name']="";
							$rec['item_count']=0;
							$rec['item_type']=0;
							$rec['item_cost']=0;
							$rec['item_dur']=0;
							$rec['item_maxdur']=0;
							$rec['item_ups']=0;
							$rec['item_unic']=0;
							$rec['item_incmagic']='';
							$rec['item_incmagic_count']='';
							$rec['item_arsenal']='';
							$rec['add_info']='Отменен квест:№'.$get_q[id].'('.$get_q[qname].')';
							add_to_new_delo($rec);


							$delfiles='/www/capitalcity.oldbk.com/labmapsq/*-'.$user[id].'.qst';
							foreach (glob($delfiles) as $dfilename)
							{
								unlink($dfilename);
							}

						}
					}
					else
					{
						echo "<div class=\"btn-control\"><form method=POST > <input type=hidden name=fin value='true'><input class='button-big btn' type=submit name=quest value='Отменить задание'> &nbsp;&nbsp;&nbsp;&nbsp;<br></form></div>";
					}
				} // проверка на возможность завершения квеста

			}
		}
		else
		{
			if ($_POST['new'])
			{
				$new=(int)($_POST['new']);
				// есть квест для получения
				if ($user[level]<=7) { $qty=" and qtype=2 ";  } else { $qty=""; }
				$qsql="select * from quests as q where (id='{$new}') ".$qty." and  id not in (select quest_id from users_quest where owner='{$user[id]}' and count>=q.repeat) ".$quest_filt." LIMIT 1;";
				// echo $qsql;
				$get_nq=mysql_fetch_array(mysql_query($qsql));
				if ($get_nq[id]>0)
				{
					if (
					mysql_query("INSERT INTO `users_quest` SET `owner`='{$user[id]}',`quest_id`='{$get_nq[id]}',`status`=0,`city`='{$cityname}',`count`=1,`get_date`=".time()." ON DUPLICATE KEY UPDATE `status`=0 , `count`=`count`+1, `get_date`=".time()."  ;")
					)
					{
						mysql_query("INSERT `labirint_var` (`owner`,`var`,`val`) values('".$user[id]."', 'qlab_count_bot', '0' ) ON DUPLICATE KEY UPDATE `val` =0;");
						echo "<b>У Вас появилось новое задание:<i>".$get_nq[qname]."</i></b><hr>";
						//пишем в дело взяли квест
						//new delo
						$rec['owner']=$user[id];
						$rec['owner_login']=$user[login];
						$rec['owner_balans_do']=$user[money];
						$rec['owner_balans_posle']=$user[money];
						$rec['owner_rep_do']=$user[repmoney];
						$rec['owner_rep_posle']=$user[repmoney];
						$rec['target']=0;
						$rec['target_login']='Вход в лабиринт';
						$rec['type']=381;
						$rec['sum_kr']=0;
						$rec['sum_ekr']=0;
						$rec['sum_kom']=0;
						$rec['sum_rep']=0;
						$rec['item_id']='';
						$rec['item_name']="";
						$rec['item_count']=0;
						$rec['item_type']=0;
						$rec['item_cost']=0;
						$rec['item_dur']=0;
						$rec['item_maxdur']=0;
						$rec['item_ups']=0;
						$rec['item_unic']=0;
						$rec['item_incmagic']='';
						$rec['item_incmagic_count']='';
						$rec['item_arsenal']='';
						$rec['add_info']='Взят квест:№'.$get_nq[id].'('.$get_nq[qname].')';
						add_to_new_delo($rec);
						echo "  <b>$get_nq[qstart] </b><hr>";
					}
				}
				else
				{
					echo "Что-то не в порядке?";
				}
			}
			else
			{
				//echo "<form method=POST > <input type=hidden name=new value='true'><input type=submit name=quest value='Получить задание'> &nbsp;&nbsp;&nbsp;&nbsp;<br></form>";
				if ($user[level]<=7) { $qty="  qtype=2 and ";  } else { $qty=""; }

				$get_nq=mysql_query("select * from quests as q where  ".$qty."  id not in (select quest_id from users_quest where owner='{$user[id]}' and count>=q.repeat) ".$quest_filt." ORDER BY qtype;");

				if (mysql_num_rows($get_nq) > 0)
				{
					echo ' <table width="80%" border="0" cellspacing="0" cellpadding="0">';

					while($grow=mysql_fetch_array($get_nq))
					{
						echo  '  <tr>';
						echo '   <td><form method=POST> <b>'.$grow[qname].'</b> <br> '.$grow[qstart].'  '.'<BR>';
						echo '   <input type=hidden name="new" value="'.$grow[id].'"><div class="btn-control"><input class="button-mid btn" type=submit name=quest value="Взять квест"></div></form><hr></td>';
						echo '   <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
						echo '  </tr>';
					}

					echo '</table>';
				}
				else
				{
					echo "<font color=red> Поздравляем вы выполнили все квесты, теперь у вас есть шанс их повторить....</font>";
					mysql_query("delete from users_quest where owner='{$user[id]}' ; ");

					//проверка колоды №1 и собраной
					$get_test_items=mysql_fetch_array(mysql_query("select count(id) as kol from inventory where owner='{$user['id']}' and prototype in (111000,111010) "));
					if (!($get_test_items['kol']>0))
					{
						// делаем дроп футляра
						//собралась
						$dress = mysql_fetch_array(mysql_query("SELECT * FROM oldbk.shop WHERE `id` = '111010' LIMIT 1;"));
						if ($dress['id']>0)
						{
							//кидаем собраную колоду
							$dress['goden']=0;
							$dress['dategoden']='';

							if (mysql_query("INSERT INTO oldbk.`inventory`
							(`prototype`,`owner`,`sowner`,`name`,`type`,`massa`,`cost`,`img`,`maxdur`,`isrep`,
								`gsila`,`glovk`,`ginta`,`gintel`,`ghp`,`gnoj`,`gtopor`,`gdubina`,`gmech`,`gfire`,`gwater`,`gair`,`gearth`,`glight`,`ggray`,`gdark`,`needident`,`nsila`,`nlovk`,`ninta`,`nintel`,`nmudra`,`nvinos`,`nnoj`,`ntopor`,`ndubina`,`nmech`,`nfire`,`nwater`,`nair`,`nearth`,`nlight`,`ngray`,`ndark`,
								`mfkrit`,`mfakrit`,`mfuvorot`,`mfauvorot`,`bron1`,`bron2`,`bron3`,`bron4`,`maxu`,`minu`,`magic`,`nlevel`,`nalign`,`dategoden`,`goden`,`nsex`,`otdel`,`present`,`labonly`,`labflag`,`group`,`idcity`,`letter`
							)
							VALUES
							('{$dress['id']}','{$user['id']}','{$user['id']}','{$dress['name']}','{$dress['type']}',{$dress['massa']},{$dress['cost']},'{$dress['img']}',{$dress['maxdur']},{$dress['isrep']},'{$dress['gsila']}','{$dress['glovk']}','{$dress['ginta']}','{$dress['gintel']}','{$dress['ghp']}','{$dress['gnoj']}','{$dress['gtopor']}','{$dress['gdubina']}','{$dress['gmech']}','{$dress['gfire']}','{$dress['gwater']}','{$dress['gair']}','{$dress['gearth']}','{$dress['glight']}','{$dress['ggray']}','{$dress['gdark']}','{$dress['needident']}','{$dress['nsila']}','{$dress['nlovk']}','{$dress['ninta']}','{$dress['nintel']}','{$dress['nmudra']}','{$dress['nvinos']}','{$dress['nnoj']}','{$dress['ntopor']}','{$dress['ndubina']}','{$dress['nmech']}','{$dress['nfire']}','{$dress['nwater']}','{$dress['nair']}','{$dress['nearth']}','{$dress['nlight']}','{$dress['ngray']}','{$dress['ndark']}',
							'{$dress['mfkrit']}','{$dress['mfakrit']}','{$dress['mfuvorot']}','{$dress['mfauvorot']}','{$dress['bron1']}','{$dress['bron2']}','{$dress['bron3']}','{$dress['bron4']}','{$dress['maxu']}','{$dress['minu']}','{$dress['magic']}','{$dress['nlevel']}','{$dress['nalign']}','".$dress['dategoden']."','{$dress['goden']}','{$dress['nsex']}','{$dress['razdel']}','Лабиринт Хаоса','0','0','{$dress['group']}','{$user['id_city']}','{$dress['letter']}'
							) ;") )
							{
								$dress['id']=mysql_insert_id();


								// пишем в дело
								$rec=array();
								$rec['owner']=$user['id'];
								$rec['owner_login']=$user['login'];
								$rec['owner_balans_do']=$user['money'];
								$rec['owner_balans_posle']=$user['money'];
								$rec['target'] = 0;
								$rec['target_login'] = 'Коллекции';
								$rec['type']=1112;
								$rec['sum_kr']=0;
								$rec['sum_ekr']=0;
								$rec['sum_kom']=0;
								$rec['item_count']=0;
								$rec['item_id']=get_item_fid($dress);
								$rec['item_name']=$dress['name'];
								$rec['item_count']=1;
								$rec['item_type']=$dress['type'];
								$rec['item_cost']=$dress['cost'];
								$rec['item_dur']=$dress['duration'];
								$rec['item_maxdur']=$dress['maxdur'];
								add_to_new_delo($rec); //юзеру
								$rec=array();
								//пишем в эффекты
								echo "<br><font color=red> Поздравляем вы получили....".$dress['name']."</font>";
							}
						}


					}


				}


			}
		}
	}
else
if (($_POST['dozay']) and ($_POST['zaylab']) and ((int)($user[labzay])==0) and ((int)$user[lab]==0) and ($labcmsg=='') )
   {
$_POST['zaylab']=(int)($_POST['zaylab']);

//$zzay=mysql_fetch_array(mysql_query("select * from labirint_zayav where `pass`='".$pass."' and `Id`='".$_POST['zaylab']."'  and `minlevel` <= '".$user[level]."' and `maxlevel` >= '".$user[level]."'  ;"));
$zzay=mysql_fetch_array(mysql_query("select * from labirint_zayav where `Id`='".$_POST['zaylab']."' ;"));

	if ($_POST['zaylab']==$zzay[Id])
		{
		if  (( ($zzay[lab]==2) AND  ( strpos($user[medals],"011;" ) !== FALSE ) ) OR ($zzay[lab]==1) OR ($zzay[lab]==3) OR ($zzay[lab]==4) )
		  {
			if ($zzay['pass']==mysql_real_escape_string($_POST['zaypass']))
			{
			if ( ($user[level]>=$zzay['minlevel']) and ($user[level] <= $zzay['maxlevel']))
				{
				$newteam=$zzay[team].$user[id].";";
				mysql_query("UPDATE `labirint_zayav` SET `team`='".$newteam."' WHERE `Id`='".$zzay[Id]."' ; ");
				mysql_query("UPDATE `users` SET `labzay`='".$zzay[Id]."' WHERE `id`='".$user[id]."' ; ");
				$user[labzay]=$zzay[Id];
				}
				else
				{
				echo "<font color=red>У Вас неподходящий уровень!<br></font>";
				}
			}
			else
			{
			echo "<font color=red>Неправильный пароль!<br></font>";
			}
		    }
		    else
		    {
		    	echo "<font color=red>Вы не рыцарь лабиринта....<br></font>";
		    }

		}
		else
		{
		echo "<font color=red>Нет такой заявки<br></font>";
		}

	}
else
 if (($_POST['open']) and ((int)($user[labzay])==0) and ((int)$user[lab]==0) and ($labcmsg=='') )
{
//проверить на чит созданали она
switch((int)$_POST['levellogin1']) {
			case 0 :
				if (($user[level]<7) OR  (((int)($_POST[labs])==3) AND ($user[level]==7)) )
					{
					$min1 = 4;
					$max1 = 7;
					}
					else
					{
					$min1 = 7;
					$max1 = 21;
					}

			break;
			case 3 :
				$min1 = $user['level'];
				$max1 = $user['level'];
			break;
			case 6 :
				//if ($user[level]<=6)
				if (((int)($_POST[labs])==3) AND ($user[level]==6))
				{
				$min1 = 5;
				$max1 = 6;
				}
				else
				if ( (((int)($_POST[labs])==0) AND ($user[level]==6)) OR (((int)($_POST[labs])==2) AND ($user[level]==6)) )
				{
				$min1 = 6;
				$max1 = 7;
				}
				else
				if ($user[level]<6)
				{
				$min1 = (int)$user['level']-1;
				$max1 = (int)$user['level']+1;
				  if ($max1>6) {$max1=6;}
				}
				else
				{
				$min1 = (int)$user['level']-1;
				$max1 = (int)$user['level']+1;
			        if ($min1<6) {$max1=6;}
				}
			break;
		}
if (((int)$min1==0) or ((int)$max1==0))
		{
		$min1 = $user[level];
		$max1 = $user[level];
		}
if ($min1<4) {$min1=4;}

$koment=$_POST[koment];
$pass=($_POST['passcr']);

	if (((int)($_POST[labs])==2) AND (strpos($user[medals],"011;" ) !== FALSE ) )
		{
		$labs=2;
		}
		else if (($user[level]<6) OR (($user[level]==6) AND ($_POST[labs])==3) )
		{
		$labs=3;
		}
		else if (($user[level]>=8) AND ($_POST[labs]==4) )
		{
		$labs=4;
		}
		else
		{
		$labs=1;
		}

	$makezay=mysql_query("INSERT INTO `labirint_zayav` SET  `lab`='".$labs."' , `team`='".$user[id].";',`minlevel`='".$min1."',`maxlevel`='".$max1."',`kol`=5,`koment`='".$koment."',`stamp`='".time()."' , `pass`='".mysql_real_escape_string($pass)."' ;");
	$newzayid=mysql_insert_id();
	mysql_query("UPDATE `users` SET `labzay`='".$newzayid."' WHERE `id`='".$user[id]."' ; ");
	$user[labzay]=$newzayid;

}
else
 if (($_POST['cancelzay'])  and ((int)($user[labzay])!=0) and ((int)$user[lab]==0) and ($labcmsg=='') )
{

$zzay=mysql_fetch_array(mysql_query("select `team` from `labirint_zayav` where `Id`='".$user[labzay]."' LIMIT 1;"));

$team=explode(";",$zzay[team]);

foreach ($team as $k => $v) if (($v!=$user[id]) and ($v!='')) $newteam.=$v.";";
mysql_query("UPDATE `users` SET `labzay`='0' WHERE `id`='".$user[id]."' ; ");

if ($newteam!='')
	{
	mysql_query("UPDATE `labirint_zayav` SET `team`='".$newteam."' WHERE `Id`='".$user[labzay]."' ; ");
	}
	else
	{
	mysql_query("DELETE FROM `labirint_zayav` WHERE `Id`='".$user[labzay]."' ; ");
	}

$user[labzay]=0;



//echo "покинуть  заявку ".$newteam."<br>";
}
else
 if (($_POST['startzay']) and ((int)($user[labzay])!=0) and ((int)$user[lab]==0) and ($labcmsg=='') )

{
// проверка только если я был подающий начать!

$zzay=mysql_fetch_array(mysql_query("select `team` , `lab` from `labirint_zayav` where `Id`='".$user[labzay]."' LIMIT 1;"));
$team=explode(";",$zzay[team]);

if ($team[0]==$user[id])
	{
// первый в списке тот может начать
	//перекидываем всех в лабу
mysql_query("DELETE FROM `labirint_zayav` WHERE `Id`='".$user[labzay]."' ; ");
	foreach ($team as $k => $v)
		{
		if ((int)($v)>0)
				{
					if (($zzay[lab]==4)  ) ///and ($user['id']==14897 OR $user['id']==690426 )
						{
						//для 3д лабы ставим первый этаж
						mysql_query("INSERT INTO `labirint_users` (`map`, `owner`, `start`, `flr` ) VALUES ('".$user[labzay]."', '".$v."', NOW(), '1' );");
						}
						else
						{
						mysql_query("INSERT INTO `labirint_users` (`map`, `owner`, `start`) VALUES ('".$user[labzay]."', '".$v."', NOW() );");
						}
					$makeroom=1000+$user[labzay];
					mysql_query("UPDATE `users` SET `labzay`='0', `lab`='".$zzay[lab]."' , `room`='".$makeroom."' WHERE `id`='".$v."' ; ");
			
				if ($user['klan']!='radminion')
					{
					mysql_query("INSERT oldbk.`labirint_var` (`owner`,`var`,`val`) values('".$v."', 'labstarttime', '".time()."' ) ON DUPLICATE KEY UPDATE `val` ='".time()."';");
					}
					
					mysql_query("INSERT `labirint_var` (`owner`,`var`,`val`) values('".$v."', 'labstartcount', '1' ) ON DUPLICATE KEY UPDATE `val` =`val`+1;");
					mysql_query("INSERT INTO oldbk.users_progress set owner='".$v."', alabcount=1 ON DUPLICATE KEY UPDATE alabcount=alabcount+1");
					mysql_query("INSERT `labirint_var` (`owner`,`var`,`val`) values('".$v."', 'keylab', '0' ) ON DUPLICATE KEY UPDATE `val` =0;");
			
					$nbat=10;		
				        $telo=mysql_fetch_array(mysql_query("select * from users where id='{$v}' "));
					if (($telo['level']>=4) and ($telo['level']<=9) ) 
							{
							$nbat=0; 
							}
			
					$bonuseffect = mysql_fetch_array(mysql_query("SELECT * FROM `effects` WHERE `owner` = '{$v}'  and `type` = '9104' LIMIT 1;")); //смотрим бонус чару		
					if ($bonuseffect['id']>0)
						{
						$nbat=(int)($nbat-($nbat*$bonuseffect['add_info']));
						if ($nbat<0) $nbat=0;
						}

						mysql_query("UPDATE `oldbk`.`ristalka` SET labp=0  WHERE `owner`='{$v}' "); //снимаем  флаг если был заюзан ключь
						if (!(mysql_affected_rows()>0))
							{	
							//апдей не прошел значит снимаем хаоты
							mysql_query("UPDATE `oldbk`.`ristalka` SET `chaos`=`chaos`-'{$nbat}'   WHERE `owner`='{$v}' "); //снимаем нужное количество
							}

						 if ($zzay[lab]==3)
								{
								mysql_query("INSERT `labirint_var` (`owner`,`var`,`val`) values('".$v."', 'charka_lab_count_ger', '1' ) ON DUPLICATE KEY UPDATE `val` =`val`+1;");
								}
								else
								{
								mysql_query("INSERT `labirint_var` (`owner`,`var`,`val`) values('".$v."', 'charka_lab_count', '1' ) ON DUPLICATE KEY UPDATE `val` =`val`+1;");
								}
						/////////////////////////////////////////////////////////////////////
				}
		}
		// genlab
		//////////
		$mapid=$user[labzay];
		echo "$mapid <br>";
		if ($zzay[lab]==2)
		{
	        include ('labogenerator2.php');
		$LL='lab2.php';
		}
		else if ($zzay[lab]==3)
		{
		include ('labogenerator3.php');
		$LL='lab3.php';
		}
		else if ($zzay[lab]==4)
		{
		//if ($user['id']==14897 OR $user['id']==690426)
		if (true)
			{
			//ставим первый этаж
			
			include ('labogenerator4_1.php');			
			}
			else
			{
			include ('labogenerator4.php');
			}
		$LL='lab4.php';
		}		
		else
		{
			include ('labogenerator.php');
			$LL='lab.php';
			}
		$user[labzay]=0;
		$user[lab]=$zzay[lab];
		$user[room]=$makeroom;



		echo "
			<script>
				function cityg(){
					location.href='".$LL."';
				}
				setTimeout('cityg()', 1000);
			</script>
			";

	}

//	echo "начать  заявку";
}
else if (($_POST['dozay'] or $_POST['open'] ) and ($labcmsg!='') )
{
 echo "<font color=red>Еще не время!</font><br>";
}





if (!$_POST['quest'])
{
////////////////////



if (($user['labzay'] > 0) and ((int)$user[lab]==0) )
	{
//уже в заявке в лабу

//$listzay=mysql_query("select * from labirint_zayav where `minlevel` <= '".$user[level]."' and `maxlevel` >= '".$user[level]."' ORDER BY id DESC ;");
$listzay=mysql_query("select * from labirint_zayav where lab!=10  ORDER BY id DESC ;");

echo "<table border=0 width=100%>\n";
while ($zay = mysql_fetch_array($listzay))
	{

	echo "
	<tr>
		<td><form method=POST >&nbsp;&nbsp;" ;
	if ($zay[lab]==2) { echo "<img src=http://i.oldbk.com/i/medal_hram_011.gif alt='Героический Лабиринт' title='Героический Лабиринт'>"; }
	$arrzay=explode(";",$zay[team]);
	foreach($arrzay as $k=>$v )
		{
		if ($k>0) {echo ', ';}
		echo nick33($v);
		}
	if ($zay[kol]==$k) { echo " <i>группа набрана</i>"; }
	 else {
		 echo "(".$zay[minlevel]."-".$zay[maxlevel]." уровни) ";
		 if ($zay[koment]!='') {  echo "<i>(".$zay[koment].")</i>";  }
		 if ($zay[lab]==3) {  echo "<i>(<u>для новичков</u>)</i>";  }
		}


		if ($zay[Id]==$user[labzay]) {
	        echo "<div class=\"btn-control\">";
                if ($arrzay[0]==$user[id])
                {
                    echo "<input type=hidden name=myzay value='".$user[labzay]."'> <input class='button-mid btn' type=submit name=startzay value='Начать'>";
                }
                echo "<input type=hidden name=myzay value='".$user[labzay]."'> <input class='button-mid btn' type=submit name=cancelzay value='Покинуть группу'>";
			echo "</div>";
		}


	echo " </form> </td>
	</tr>
		";

	}


echo "</table>";

	}
	else
 if ((int)$user[lab]==0)
	{
//не в зайвке может подать

	if ($labcmsg=='')
		{
		echo " При походе в Лабиринт Хаоса - карта всегда разная. Удачи! <br><br>";

?>
		<DIV id="dv2" style="display:">&nbsp;&nbsp;<A href="#" onclick="dv1.style.display=''; dv2.style.display='none'; return false">Создать группу</A></DIV>
		<DIV id="dv1" style="display: none">

		<FIELDSET><LEGEND><B>Создать группу</B> </LEGEND>
		<form method=POST >
		Уровни бойцов &nbsp;&nbsp;<SELECT NAME=levellogin1><option value=0>любой<option value=3>только моего уровня<option value=6 selected>мой уровень +/- 1</SELECT><BR>
		<?

		if (($user[level]==6) OR ($user[level]==7))
		{
		echo "Тип Лабиринта &nbsp;&nbsp;<SELECT NAME=labs><option value=3>Лабиринт новичков<option value=0>Обычный Лабиринт";
		}
		else if ($user[level]<6)
		{
		echo "Тип Лабиринта &nbsp;&nbsp;<SELECT NAME=labs><option value=3>Лабиринт новичков";
		}
		else
		{
		echo "Тип Лабиринта &nbsp;&nbsp;<SELECT NAME=labs><option value=1>Обычный Лабиринт";
		}

		if (( strpos($user[medals],"011;" ) !== FALSE ) )
		{
		echo "<option value=2>Героический Лабиринт";
		}

		if ($user[level]>=8) 
		{
		echo "<option value=4>Легендарный Лабиринт";
		}

		echo "</SELECT><BR><BR>";
		?>

		Комментарий <INPUT TYPE=text NAME='koment' maxlength=40 size=40><BR>
		Пароль <INPUT TYPE=password NAME='passcr' maxlength=20 size=20><BR>
		<div class="btn-control">
            <INPUT class="button-mid btn" TYPE=submit name=open value="Подать заявку">&nbsp;
        </div>
            <BR></form></FIELDSET>
		<BR></DIV>
<?
		}
	echo "<table border=0 width=100%>\n";

//	$listzay=mysql_query("select * from labirint_zayav where `minlevel` <= '".$user[level]."' and `maxlevel` >= '".$user[level]."' ORDER BY id DESC ;");
	$listzay=mysql_query("select * from labirint_zayav where lab!=10  ORDER BY id DESC ;");


while ($zay = mysql_fetch_array($listzay))
	{

	echo "<form method=POST >
	<tr valign=center>
		<td ><INPUT TYPE=radio  NAME=zaylab id=zaylab value='".$zay[Id]."'> " ;
	if ($zay[lab]==2) { echo "<img src=http://i.oldbk.com/i/medal_hram_011.gif alt='Героический Лабиринт' title='Героический Лабиринт'>&nbsp;"; }
	$arrzay=explode(";",$zay[team]);
	foreach($arrzay as $k=>$v )
		{
		if ($k>0) {echo ', ';}
		echo nick33($v);
		}
	if ($zay[kol]==$k) { echo " <i>группа набрана</i>"; }
	 else {
 		 echo "(".$zay[minlevel]."-".$zay[maxlevel]." уровни) ";
		 if ($zay[koment]!='') {  echo "<i>(".$zay[koment].")</i>";  }
		 if ($zay[lab]==3) {  echo "<i>(<u>для новичков</u>)</i>";  }
 		 if (($user['align']>1.4 && $user['align']<2) || ($user['align']>2 && $user['align']<3)) {
				echo "<a href='?clear=$zay[Id]'><img src='i/clear.gif' title='Удалить комент' alt='Удалить комент'></a>";
			}



		if ($zay[pass]!="") { echo " пароль:<input type=password name='zaypass'>"; }
		echo " <input type=submit name=dozay value='Войти в группу'>";
		}

	echo "  </td>
	</tr>
	</form>	";

	}
	echo "</table>";

	}
 else
	{
	$_SESSION['looklab']=270;
	 echo " Перемещаемся в лабиринт... ";
	}
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
?>
&nbsp;</td>
  </tr>
</table>

</body>
</html>
