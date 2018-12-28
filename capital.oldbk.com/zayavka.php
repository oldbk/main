<?
//////////////////////////
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
	ini_set('display_errors','On');
	if (!($_SESSION['uid'] >0)) { header("Location: index.php"); die(); }

	include "connect.php";
	include "functions.php";
	include "fsystem.php";
	include "functions.zayavka.php";
	require_once('config_ko.php');

	function show_aligns_zay($button)
	{
		global $user;
	$outstr='';


		//view my level
					if ($user['level']>=13)
					{
					$outstr.='Ваша уровневая группа: <B>13-14</B> уровни';
					$get_all=mysql_query('select count(id) as k , align  from zayavka_turn where lvl=14 or lvl=13 group by align ');
					}
					else
					{
					$outstr.='Ваша уровневая группа: <B>'.$user['level'].'</B> уровни';
					$get_all=mysql_query('select count(id) as k, align  from zayavka_turn where lvl='.$user['level'].' group by align ');
					}

						$total_aligns=array();
						$total=0;
					if (mysql_num_rows($get_all)>0)
						{
						while ($rw = mysql_fetch_assoc($get_all))
							{
							$total_aligns[$rw['align']]+=$rw['k'];
							$total+=$rw['k'];
							}
						}

				$outstr.='<BR>Всего в очереди: <b>'.$total.'</b>';

				if (count($total_aligns)>0)
					{
					$txt[3]='Темные';
					$txt[6]='Светлые';
					$txt[2]='Нейтралы';
					foreach ($total_aligns as $a => $kol )
							{
							$outstr.='<br><img src="http://i.oldbk.com/i/align_'.$a.'.gif" alt="'.$txt[$a].'" title="'.$txt[$a].'" > '.$txt[$a].':<b> '.$kol.'</b>';
							}
					}

				$outstr.=$button;
		if (!($_SESSION['view']))
		{
		//view all other
			$outstr.='<hr><div style="text-align:left;">';
			if ($user['level']>=13)
				{
				$allz=mysql_query('select count(id) as k, lvl from zayavka_turn where lvl!=14 and lvl!=13 and lvl>0  GROUP by lvl ORDER by lvl');
				}
				else
				{
				$allz=mysql_query('select count(id) as k, lvl from zayavka_turn where lvl!='.$user['level'].' and lvl>0  GROUP by lvl ORDER by lvl');
				}

			while ($row = mysql_fetch_array($allz))
			{
			$lvldata[$row[lvl]]=$row['k'];
			}

			foreach ($lvldata as $lvl => $k )
				{
					$slvl=($lvl>=13?"13-14":$lvl);
					$slvl=$lvl;
					if ($lvl<=13)
					{
					$kol=($lvl==13?$lvldata[14]+$lvldata[13]:$k);
					//$kol=$k;
					$outstr.='Уровневая группа: <B>'.$slvl.'</B> уровни в очереди:<b>'.(int)($kol).'</b><br>';
					}
				}

			$outstr.= "</div>";
		}

	return $outstr;
	}


	  if (($_POST["get_clans_by_align"] >=1) and ($_POST["get_clans_by_align"] <=4))
	  		{
					switch($_POST["get_clans_by_align"])
					{
					case 1 :
						//<option value=1>Любой
						$kln=mysql_query("SELECT id, short   from oldbk.clans where id not in (78,81,34,417,418,419,420,421,422,423,280,260,279,433) and time_to_del=0 order by short ");
					break;
					case 2 :
						//<option value=2>Нейтралы
						$kln=mysql_query("SELECT id, short  from oldbk.clans where  id not in (78,81,34,417,418,419,420,421,422,423,280,260,279,433) and time_to_del=0 and align=2 order by short ");
					break;
					case 3 :
						//	<option value=3>Тьма
						$kln=mysql_query("SELECT id, short  from oldbk.clans where  id not in (78,81,34,417,418,419,420,421,422,423,280,260,279,433) and time_to_del=0 and align=3 order by short ");
					break;
					case 4 :
						//	<option value=4>Свет
						$kln=mysql_query("SELECT id, short  from oldbk.clans where  id not in (78,81,34,417,418,419,420,421,422,423,280,260,279,433) and time_to_del=0 and align=6 order by short ");
					break;
					}
				$outarray=array();

				while ($clns = mysql_fetch_array($kln))
				{
				$outarray[]=array("id" => $clns['id'] , "name" => $clns['short']);
				}
	  		echo json_encode($outarray);
			    if (isset($miniBB_gzipper_encoding))
			    {
			    $miniBB_gzipper_in = ob_get_contents();
			    $miniBB_gzipper_inlenn = strlen($miniBB_gzipper_in);
			    $miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
			    $miniBB_gzipper_lenn = strlen($miniBB_gzipper_out);
			    $miniBB_gzipper_in_strlen = strlen($miniBB_gzipper_in);
			    $gzpercent = percent($miniBB_gzipper_in_strlen, $miniBB_gzipper_lenn);
			    $percent = round($gzpercent);
			    $miniBB_gzipper_out = gzencode($miniBB_gzipper_in, 2);
			    ob_clean();
			    header('Content-Encoding: '.$miniBB_gzipper_encoding);
			    echo $miniBB_gzipper_out;
			    }
			/////////////////////////////////////////////////////
			die();
	  		}



	$op = CheckOpDay(); // открыт ли день противостояния

		if (((time()>$KO_start_time29) and (time()<$KO_fin_time29)) ) //Победный май
		{
		$op=true;
		}


	if ($user['battle'] != 0 && $_REQUEST['level'] != '') { header('Location: fbattle.php'); die(); }

	if($user['klan'] == 'radminion' )
	{
	$op=true;
	  echo "{$user['klan']} information: ".exec('hostname');
	  echo "Admin-info:<!- GZipper_Stats -> <br>";
	}

	/*
	настройки ботов для физов
	*/
	$time_to_bot=420; // 10 min ТАЙМАУТ показа ботов

	$cbots[2]=array(590=>'590|2|0||Грэхэм#',591=>'591|2|0||Амадис Гальский#',592=>'592|2|0||Эль Сид#',593=>'593|2|0||Любор#',594=>'594|2|0||Метислав#',595=>'595|2|0||Гридя#'); // идшники ботов для 4го уровня
	$cbots[3]=array(586=>'586|3|0||Мангольд#',587=>'587|3|0||Роджер#',588=>'588|3|0||Джон#',589=>'589|3|0||Гарольд#'); // идшники ботов для 3го уровня
	$cbots[4]=array(571=>'571|4|0||Роланд#',572=>'572|4|0||Сид Кампеадор#',573=>'573|4|0||Роберт Дьявол#',574=>'574|4|0||Бертран де Борн#',575=>'575|4|0||Раймунд Тулузский#'); // идшники ботов для 4го уровня
	$cbots[5]=array(576=>'576|5|0||Симон де Монфор#',577=>'577|5|0||Иоганн Люксембургский#',578=>'578|5|0||Бертран Дюгеклен#',579=>'579|5|0||Жан Дюнуа#'); // идшники ботов для  5го уровня
	$cbots[6]=array(580=>'580|6|0||Жиль де Рэ#',581=>'581|6|0||Пьер Баярд#',582=>'582|6|0||Габриэль Монтгомери#',583=>'583|6|0||Галахад#'); // идшники ботов для  6го уровня
//	$cbots[7]=array(584=>'584|7|0||Томас#',585=>'585|7|0||Малькольм#'); // идшники ботов для  7го уровня


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

$myeff = getalleff($user['id']);

if((int)$_GET['haos_del']>0)
{
$_GET['haos_del']=(int)$_GET['haos_del'];
	$zay=mysql_fetch_assoc(mysql_query('SELECT * FROM zayavka WHERE id='.$_GET['haos_del'].';'));

	if ( (($zay['type']==3) or ($zay['type']==5)) and ($zay['coment'] !='<b>Великий Хаотический Бой!</b>') and ($zay['coment'] !='<b>#zlevels</b>' ) and ($zay['coment'] !='<b>#zbuket</b>') and ($zay['coment'] !='<b>#zelka</b>' ) )
	{

		$zay['team1']=substr($zay['team1'],0,-1);
		$tt1=explode(';',$zay['team1']);
		$tt2=explode(';',$zay['team2']);
		if(count($tt1)==1 && $tt2[0]=='' && $tt1[0]==$user['id'])
		{
			if(mysql_query('DELETE FROM zayavka WHERE id='.$_GET['haos_del'].';'))
			{
				if($zay['price']>0)
				{
					$sql=', money=money+'.$zay['price'];

					//new_delo
  		    			$rec['owner']=$user[id];
					$rec['owner_login']=$user[login];
					$rec['owner_balans_do']=$user['money'];
					$user[money]=$user[money]+$zay['price'];
					$rec['owner_balans_posle']=$user['money'];
					$rec['target']=0; $rec['target_login']='';
					$rec['type']=18;
					$rec['sum_kr']=$zay['price'];
					$rec['sum_ekr']=0; $rec['sum_kom']=0; $rec['item_id']=''; $rec['item_name']='';
					$rec['item_count']=0; $rec['item_type']=0; $rec['item_cost']=0; $rec['item_dur']=0;
					$rec['item_maxdur']=0; 	$rec['item_ups']=0; $rec['item_unic']=0;
					$rec['item_incmagic']=''; $rec['item_incmagic_count']=''; $rec['item_arsenal']='';
					add_to_new_delo($rec); //юзеру

					addchp ('<font color=red>Внимание!</font> Вам возвращено '.$zay[price].' кр. ставки. ','{[]}'.$user['login'].'{[]}',$user['room'],$user['id_city']);

				}
				mysql_query('UPDATE users SET `zayavka` = 0, battle_t=0  '.$sql.' WHERE id='.$user['id'].';');

				echo '<font color="red"><b>Заявка удалена.</b></font>';

			}

		}
		else
		{
			echo '<font color="red"><b>Не надо так делать...</b></font>';
		}

	}
	else
	{
		echo '<font color="red"><b>Не надо так делать...</b></font>';
	}
}

//===================================================================================================================
// стираем коммент

	if (($_GET['do'] == "clear") && (($user['align']>1.4 && $user['align']<2) || ($user['align']>2 && $user['align']<3))) {
		if ($user['align']>1.1 && $user['align']<2) {$angel="паладином";}
		if ($user['align']>2 && $user['align']<3) {$angel="Ангелом";}
		$_GET['zid']=(int)$_GET['zid'];
		mysql_query("UPDATE `zayavka` SET `coment`='Удалено $angel <b>".$user['login']."</b>' where `id`='{$_GET['zid']}' LIMIT 1;");
	}
	elseif (($user['klan']=='radminion') and ($_GET['do'] == "del") and ($_GET['zid'] >0) )
	{
		$_GET['zid']=(int)$_GET['zid'];
		mysql_query("UPDATE `zayavka` SET `start`='".time()."' where `id`='{$_GET['zid']}' LIMIT 1;");
	}

	if(isset($_REQUEST['view'])) {
		$_SESSION['view'] = $_REQUEST['view'];
	}

	if($_SESSION['view'] > 0 && $_SESSION['view']!=$user['level']) {$_SESSION['view'] = null;}
//===================================================================================================================
// работа с заявками
	header("Cache-Control: no-cache");
?>
<HTML>
	<HEAD>
		<link rel=stylesheet type="text/css" href="newstyle20.css">
		<link rel="stylesheet" href="/i/btn.css" type="text/css">
		<meta content="text/html; charset=windows-1251" http-equiv=Content-type>
		<META Http-Equiv=Cache-Control Content=no-cache>
		<meta http-equiv=PRAGMA content=NO-CACHE>
		<META Http-Equiv=Expires Content=0>
		<script type="text/javascript" src="/i/globaljs.js"></script>
		<style>
			.m {background: #99CCCC;text-align: center;}
			.s {background: #BBDDDD;text-align: center;}
			.s2 {background: #C0D6D4;text-align: center;}
			BODY{FONT-SIZE:10pt;FONT-FAMILY:Verdana,Arial,Helvetica,Tahoma,sans-serif}TD{FONT-SIZE:10pt;FONT-FAMILY:Verdana,Arial,Helvetica,Tahoma,sans-serif}OL{FONT-SIZE:10pt;FONT-FAMILY:Verdana,Arial,Helvetica,Tahoma,sans-serif}UL{FONT-SIZE:10pt;FONT-FAMILY:Verdana,Arial,Helvetica,Tahoma,sans-serif}LI{FONT-SIZE:10pt;FONT-FAMILY:Verdana,Arial,Helvetica,Tahoma,sans-serif}P{FONT-SIZE:10pt;FONT-FAMILY:Verdana,Arial,Helvetica,Tahoma,sans-serif}
		</style>
		<script>
		<?
			if(!($_POST['open1']) )
			{
		?>
			function refreshPeriodic()
			{
				<?if ($_REQUEST['logs'] == null) {?>location.href='zayavka.php?level=<?=$_REQUEST['level']?>&tklogs=<?=$_REQUEST['tklogs']?>&logs=<?=$_REQUEST['logs']?>';//reload();
				<?}?>
				timerID=setTimeout("refreshPeriodic()",30000);
			}
			timerID=setTimeout("refreshPeriodic()",30000);
			<?
			}
			?>

		function showinfiz(){
		var val = document.getElementById('htypefiz').value ;
		if (val==4)
		{
		document.getElementById('kulakinffiz').style.display = "block";
		}
		else
		{
		document.getElementById('kulakinffiz').style.display = "none";
		}
	}

	</script>

	</HEAD>
<body onload="top.setHP(<?=$user['hp']?>,<?=$user['maxhp']?>);">
<div id="page-wrapper" class="map-wrapper">
<?
	make_quest_div();

		if ($user['level']==0)
			{
			$csp=8;
			}
			else
			{
			$csp=7;
			}

		if (($user['klan']=='radminion') OR ($user['klan']=='testTest'))
		//if (true)
		{
		$csp++;
		}


		echo '<TABLE width=100% cellspacing=1 cellpadding=1 border=0>';
		echo "<FORM METHOD=POST name='zay' id='zay' name=F1>";
		echo '<TR>';
		echo "<TD colspan={$csp}>";
		echo '<table width=100% cellspacing=1 cellpadding=1 border=0>';
		echo '<tr><td width=50%>';
		if ($_REQUEST['level']) { nick($user); } else { echo "&nbsp;"; }
		echo '</td>';
		echo '<td width=50% align=right>';
		$bst=mysql_query("select * from battle where status_flag>0 and win=3 ");
		while ($brow = mysql_fetch_array($bst))
			{
			 if (($brow[teams]!='AFB') and ($brow['coment']!='<b>#zbuket</b>') and ($brow['coment']!='<b>#zelka</b>'))  { echo "<b>Статусная битва</b><img src='http://i.oldbk.com/i/fighttype3.gif'> <a href='logs.php?log=".$brow[id]."' target=_blank>»»</a> "; }
			}
		?>
		<INPUT TYPE=button class="button-mid btn" value="Карта миров" onclick="location.href='main.php?setch=<?=mt_rand(111111,999999); ?>';">
		<INPUT TYPE=button class="button-mid btn" value="Подсказка" style="background-color:#A9AFC0" onclick="window.open('help/combats.html', 'help', 'height=300,width=500,location=no,menubar=no,status=no,toolbar=no,scrollbars=yes')">
		<INPUT TYPE=button class="button-mid btn" value="Вернуться" onclick="location.href='main.php?top=<?=mt_rand(111111,999999); ?>';">
		</td>
		</tr>
		</table>
	</TD>
</TR>
<TR>
<TD class=m width=40>&nbsp;<B>Поединки:</B></TD>
<?
	if ($user['level']==0)
	{
	?>
	<TD class=<?=($_REQUEST['level']=='begin')?"s":"m"?>><A HREF="zayavka.php?level=begin&<?=mt_rand(111111,999999); ?>">Новичков</A></TD>
	<?
	}
?>
<TD class=<?=($_REQUEST['level']=='fiz')?"s":"m"?>><A HREF="zayavka.php?level=fiz&<?=mt_rand(111111,999999); ?>">Физические</A></TD>
<TD class=<?=($_REQUEST['level']=='dgv2')?"s":"m"?>><? if ($op==true) { echo '<IMG align="top" height="16" width="16" SRC="http://i.oldbk.com/i/fighttype40.gif">'; }?><A HREF="zayavka.php?level=dgv2&<?=mt_rand(111111,999999); ?>">Склонности</A></TD>
	<?
	if (($user['klan']=='radminion') OR ($user['klan']=='testTest'))
	//if (true)
	{
	?>
	<TD class=<?=($_REQUEST['level']=='grclass')?"s":"m"?>><A HREF="zayavka.php?level=grclass&<?=mt_rand(111111,999999); ?>">Бои классов</A></TD>
	<?
	}
	?>
<TD class=<?=($_REQUEST['level']=='group')?"s":"m"?>><A HREF="zayavka.php?level=group&<?=mt_rand(111111,999999); ?>">Тренировочные</A></TD>
<TD class=<?=($_REQUEST['level']=='haos')?"s":"m"?>><A HREF="zayavka.php?level=haos&<?=mt_rand(111111,999999); ?>">Хаотичные</A></TD>
<TD class=<?=($_REQUEST['tklogs']>='1')?"s":"m"?>><A HREF="zayavka.php?tklogs=1&<?=mt_rand(111111,999999); ?>">Текущие</A></TD>
<TD class=<?=($_REQUEST['logs']!=null)?"s":"m"?>><A HREF="zayavka.php?logs=<?=date("d.m.y")?>&<?=mt_rand(111111,999999); ?>">Завершенные</A></TD>
</TR>
<?
  if ($_REQUEST['tklogs']>='1')
    {
	echo '<TD class=m width=40>&nbsp;<B>Локации:</B></TD>';

	echo "<TD colspan=".($csp-1).">";
	echo '<TABLE width=100% cellspacing=1 cellpadding=1 border=0>';
	?>
		<TR>
	<TD class=<?=($_REQUEST['tklogs']=='1')?"s2":"s"?>><A HREF="zayavka.php?tklogs=1&<?=mt_rand(111111,999999); ?>">Поединки в клубе</A></TD>
	<TD class=<?=($_REQUEST['tklogs']=='7')?"s2":"s"?>><A HREF="zayavka.php?tklogs=7&<?=mt_rand(111111,999999); ?>">Поединки за Замки</A></TD>
	<TD class=<?=($_REQUEST['tklogs']=='2')?"s2":"s"?>><A HREF="zayavka.php?tklogs=2&<?=mt_rand(111111,999999); ?>">Поединки на Ристалище</A></TD>
	<TD class=<?=($_REQUEST['tklogs']=='3')?"s2":"s"?>><A HREF="zayavka.php?tklogs=3&<?=mt_rand(111111,999999); ?>">Поединки в Руинах</A></TD>
	<TD class=<?=($_REQUEST['tklogs']=='4')?"s2":"s"?>><A HREF="zayavka.php?tklogs=4&<?=mt_rand(111111,999999); ?>">Поединки в Лабиринте</A></TD>
	<TD class=<?=($_REQUEST['tklogs']=='5')?"s2":"s"?>><A HREF="zayavka.php?tklogs=5&<?=mt_rand(111111,999999); ?>">Поединки в Башне смерти</A></TD>
	<TD class=<?=($_REQUEST['tklogs']=='6')?"s2":"s"?>><A HREF="zayavka.php?tklogs=6&<?=mt_rand(111111,999999); ?>">Поединки в Загороде</A></TD>
		</TR>
	</TABLE>
	</TD>
	</TR>
	<?
    }
?>
</TABLE>
<TABLE width=100% cellspacing=0 cellpadding=0><TR><TD valign=top>
<?
if(!$_REQUEST['level'] && !$_REQUEST['tklogs'] && !$_REQUEST['logs'])
	{
	echo "<BR><BR><BR><CENTER><B>Выберите раздел</b></CENTER>";
	}

$allowRoom = array(   36 => true,   54 => true,   55 => true,   56 => true,   57 => true, 75 => true );


if (($_REQUEST['level']=='grclass') and ($user['room']==76) )
	{
	//разрешаем бои  классов из комнаты классов
	}
	elseif (($_REQUEST['level']=='grclass') and ($user['room']!=76) )
	{
	echo "<BR><BR><BR><CENTER><B>Бои доступны только в комнате \"Бои классов\".</b></CENTER>";
	zaydie();
	}
elseif($_REQUEST['level'] != '' && ($user['room'] > 19 && !isset($allowRoom[$user['room']])))
	{
	echo "<BR><BR><BR><CENTER><B>В этой комнате нельзя подавать заявки.</b></CENTER>";
	zaydie();
	}

///////////////////////////////////////////////////////////////////////////
///Новичков
 if ($_REQUEST['level'] == 'begin')
	{
	if ($user[id]==14897) 	print_r($_POST);

		if(($user['level']>0) and ($user[klan]!='radminion'))
			{
			echo "<BR><BR><BR><CENTER><B>Вы уже выросли из ползунков ;)</b></CENTER>";
			zaydie();
			}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	echo "<font color=red><b>";

		if($_POST['open'] AND ($user[zayavka]==0) )
		{
			$kty=(int)$_POST['k'];

			if (($kty!=1) AND  ($kty!=4)) {$kty==1; }

			echo addzayavka(0,$_POST['timeout'], 1, 1, $_POST['k'], $user['level'], 1, $user['level'], 21, '', $user, $myeff , 1, 0);
		}
		else
		if($_POST['back'] AND ($user[zayavka]>0))
		{
		echo delzayavka();
		}
		else
		if($_POST['back2'] AND ($user[zayavka]>0))
		{
			$zay_array=getlist(1,null,$user[zayavka]);
			if ($zay_array)
		 	{
		 	$zid=$user[zayavka];
				$OKBB=false;
				echo delteam (2,$user);
				if ($OKBB)
				{
				$user[zayavka]=0;
				$user[battle_t]=0;
				addchp ('<font color=red>Внимание!</font> '.$user['login'].' отозвал заявку.   ','{[]}'.nick7((int)($zay_array[$zid]['team1'])).'{[]}');
				}
			}
		}
		else
		if($_POST['cansel'] AND ($user[zayavka]>0))
		{

			$zay_array=getlist(1,null,$user[zayavka]);
			if ($zay_array)
			{
			$zid=$user[zayavka];
			$btelo[id]=(int)($zay_array[$zid]['team2']);
			$btelo[zayavka]=$zid;
				$OKBB=false;
				echo delteam (2,$btelo);
				if ($OKBB)
				{
				addchp ('<font color=red>Внимание!</font> '.$user['login'].' отказался от поединка.  ','{[]}'.nick7($btelo[id]).'{[]}');
				}

			}
		}
		else
		if($_POST['confirm2'])
		{
		$zid=(int)($_REQUEST['gocombat']);
		 if ($zid>0)
		  {
			$zay_array=getlist(1,null,$zid);
			 if ($zay_array)
		 	{
		 	$OKADD=false;
			echo addteam (2,$user,$myeff,$zay_array[$zid], $zid);
			if ($OKADD)
				{
				addchp ('<font color=red>Внимание!</font> '.$user['login'].' принял заявку, нужно принять вызов или отказать.  ','{[]}'.nick7((int)($zay_array[$zid]['team1'])).'{[]}');
				}

			}
		  }
		}
		else
		if(($_POST['gofi']) and (ustatus($user)==1) )
		{
			$zay_array=getlist(1,null,$user[zayavka]);
			 if ($zay_array)
		 	{
		 	$zid=$user[zayavka];


			 	if (($zay_array[$zid][team2]!='') AND ($zay_array[$zid][team1]!=''))
		 		{
				battlestart( $user, $zay_array[$zid], 1 );
				}

			}
		}

echo "</b></font>";

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$zay_array=getlist(1,null);

	echo '<table cellspacing=0 cellpadding=0><tr><td>';

	if (( ustatus($user)==0 )  and ($_GET['trainstart']!=1))
	{
	//Таймаут <SELECT NAME=timeout><OPTION value=3>3 мин.<OPTION value=4>4 мин.<OPTION value=5>5 мин.<OPTION value=7>7 мин.<OPTION value=10 selected>10 мин.</SELECT>
		echo '<FIELDSET><LEGEND><B>Подать заявку на бой</B> </LEGEND>Тип боя <SELECT NAME=k><OPTION value=1>с оружием<OPTION value=4>кулачный </SELECT><INPUT TYPE=submit name=open class="button-big btn" value="Подать заявку">&nbsp;';
		echo " или <input type=button class='button-big btn' onclick=\"location.href='zayavka.php?open=1&level=begin&trainstart=1';\" value=\"Начать тренировочный бой\">";
		echo '</FIELDSET>';
	}
	else
	if(( (ustatus($user)==1 ) and (is_array($zay_array[$user[zayavka]]) ) ) OR ($_GET['trainstart']==1) )
		{

		if ($zay_array[$user[zayavka]][team2]!='')
			{
			echo "<B><font color=red>Внимание".$zay_array[$user['zayavka']]['t2hist']." принял заявку на бой, нужно отказать или принять вызов.</font></b> <input class=\"button-mid btn\" type=submit value='Битва!' name=gofi> <input class=\"button-mid btn\" type=submit value='Отказать' name=cansel>";
			}
			else
			{
			echo "Заявка на бой подана, ожидаем противника. <input class=\"button-big btn\" type=submit name=back value='Отозвать заявку'>";

		//	if($timeFigth+300 < time() && $user['level'] == 0)
			if($user['level'] == 0)
			{

				if($_GET['trainstart']==1 && $user['hp'] > $user['maxhp']*0.33)
				{
				//Удаляем свою заявку если она пустая
				mysql_query("DELETE FROM `zayavka` WHERE id='{$user[zayavka]}' and team1='{$user[id]};' and team2='' ");
				//if (mysql_affected_rows()>0)
				{
					//Создаем тренеровочный бой для 0-х уровней
					// подсчет мф тела
					$user_items=load_mass_items_by_id($user);
					$user_items['krit_mf']=(int)($user_items['krit_mf']*0.8);
					$user_items['max_u']=(int)($user_items['max_u']*0.9);
					$user_items['akrit_mf']=(int)($user_items['akrit_mf']*0.8);
					$user_items['uvor_mf']=(int)($user_items['uvor_mf']*0.8);
					$user_items['auvor_mf']=(int)($user_items['auvor_mf']*0.8);
					$user_items['bron1']=(int)($user_items['bron1']*0.8);
					$user_items['bron2']=(int)($user_items['bron2']*0.8);
					$user_items['bron3']=(int)($user_items['bron3']*0.8);
					$user_items['bron4']=(int)($user_items['bron4']*0.8);

					mysql_query("INSERT INTO `users_clons` SET `login`='".$user['login']." (клон 1)',`sex`='{$user['sex']}',
					`level`='{$user['level']}',`align`='{$user['align']}',`klan`='{$user['klan']}',`sila`='{$user['sila']}',
					`lovk`='{$user['lovk']}',`inta`='{$user['inta']}',`vinos`='{$user['vinos']}',
					`intel`='{$user['intel']}',`mudra`='{$user['mudra']}',`duh`='{$user['duh']}',`bojes`='{$user['bojes']}',`noj`='{$user['noj']}',
					`mec`='{$user['mec']}',`topor`='{$user['topor']}',`dubina`='{$user['dubina']}',`maxhp`='{$user['maxhp']}',`hp`='{$user['maxhp']}',
					`maxmana`='{$user['maxmana']}',`mana`='{$user['mana']}',`sergi`='{$user['sergi']}',`kulon`='{$user['kulon']}',`perchi`='{$user['perchi']}',
					`weap`='{$user['weap']}',`bron`='{$user['bron']}',`r1`='{$user['r1']}',`r2`='{$user['r2']}',`r3`='{$user['r3']}',`helm`='{$user['helm']}',
					`shit`='{$user['shit']}',`boots`='{$user['boots']}',`nakidka`='{$user['nakidka']}',`rubashka`='{$user['rubashka']}',`shadow`='{$user['shadow']}',`battle`=1,`bot`=1,
					`id_user`='{$user['id']}',`at_cost`='{$user_items['allsumm']}',`kulak1`=0,`sum_minu`='{$user_items['min_u']}',
					`sum_maxu`='{$user_items['max_u']}',`sum_mfkrit`='{$user_items['krit_mf']}',`sum_mfakrit`='{$user_items['akrit_mf']}',
					`sum_mfuvorot`='{$user_items['uvor_mf']}',`sum_mfauvorot`='{$user_items['auvor_mf']}',`sum_bron1`='{$user_items['bron1']}',
					`sum_bron2`='{$user_items['bron2']}',`sum_bron3`='{$user_items['bron3']}',`sum_bron4`='{$user_items['bron4']}',`ups`='{$user_items['ups']}',
					`injury_possible`=0, `battle_t`=2;");
					$bot = mysql_insert_id();
					//делаем масив для бота шоб не перечитывать из базы
					$bot_data=$user;
					$bot_data[id]=$bot;
					$bot_data[login]=$user['login']." (клон 1)";

					mysql_query("INSERT INTO `battle`
						(
							`id`,`coment`,`teams`,`timeout`,`type`,`status`,`t1`,`t2`,`to1`,`to2`,`t1hist`,`t2hist`
						)
						VALUES
						(
							NULL,'','','3','1','0','".$user['id']."','".$bot."','".time()."','".time()."','".BNewHist($user)."','".BNewHist($bot_data)."'
						)");

					$id = mysql_insert_id();
					mysql_query("INSERT IGNORE INTO battle_dam_exp (battle,owner) VALUES ('{$id}','{$user['id']}')");
					// апдейтим бота
					mysql_query("UPDATE `users_clons` SET `battle` = {$id} WHERE `id` = {$bot} LIMIT 1;");
					$bot_data[battle]=$id;
					// создаем лог
					$rr = "<b>".nick_hist($user)."</b> и <b>".nick_hist($bot_data)."</b>";
//					addlog($id,"Часы показывали <span class=date>".date("Y.m.d H.i")."</span>, когда ".$rr." бросили вызов друг другу. <BR>");
					addlog($id,"!:S:".time().":".BNewHist($user).":".BNewHist($bot_data)."\n");
					//тренеровочный бой чар за сторону 1
					mysql_query("UPDATE users SET `battle` ={$id},`zayavka`=0, `battle_t`=1 WHERE `id`= {$user['id']};");
				echo "<script>location.href='fbattle.php';</script>";
				zaydie();
				}
					///=======================================================================================
				}
				echo " или <input class='button-big btn' type=button onclick=\"location.href='zayavka.php?level=begin&trainstart=1';\" value=\"Начать тренировочный бой\">";

			}

			}
		}
		if( (ustatus($user)==2 ) and (is_array($zay_array[$user[zayavka]]) ) )
		{
			echo "Ожидаем подтверждения боя. <input class='button-big btn' type=submit name=back2 value='Отозвать заявку'>";
		}

	echo '</td></tr></table></TD><TD align=right valign=top rowspan=2><INPUT TYPE=submit class="button-mid btn" name=tmp value="Обновить">';
	echo '<tr><td>';

	if ($zay_array)
	{
	if ($user['zayavka']==0) echo '<INPUT TYPE=hidden name=level value=begin><INPUT class="button-big btn" TYPE=submit value="Принять вызов" NAME=confirm2><BR>';

		$co_fiz=0;
		foreach ( $zay_array as $k => $v )
			{
			echo showfiz($v);
			$co_fiz++;
			}
		if ($co_fiz>=10)
			{
			if ($user['zayavka']==0)	echo '<INPUT class="button-big btn" TYPE=submit value="Принять вызов" NAME=confirm2>';
			}
	}

	echo '</TD></TR></TABLE>';

	zaydie();
}
else if ($_REQUEST['level'] == 'fiz')
{
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//if ($user[id]==14897) 	print_r($_POST);

	if($user['level']==0) {
		echo "<BR><BR><BR><CENTER><B>Физические бои доступны с 1 уровня.</b></CENTER>";
		zaydie();
	}

	if($user['align']==5) {
		echo "<BR><BR><BR><CENTER><B>Физические бои не доступны для Абсолютного хаоса.</b></CENTER>";
		zaydie();
	}

	if (isset($myeff[830])) {
		echo "<BR><BR><BR><CENTER><B>Вы находитесь под медитацией!</b></CENTER>";
		zaydie();
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	echo "<font color=red><b>";
	if($_REQUEST['open'] AND ($user[zayavka]==0) )
	{
		$kty=(int)($_REQUEST['k']);

		if (($kty!=1) AND ($kty!=4) AND $kty!=6) {$kty=1; }
		if (($kty==6) and ($user[level]<5)) {$kty=1;}
		if ($kty==6) { $blood = 1;} else  { $blood = 0; }
		echo  addzayavka (0,$_POST['timeout'], 1, 1, $kty, $user['level'], 1, $user['level'], 21, '', $user, $myeff , 2, 0,$blood);
		$add_fiz=1; //??
	}
	else
	if($_POST['back']  AND ($user[zayavka]>0))
	{
		echo delzayavka();
	}
	else
	if($_POST['back2'] and ($user[zayavka]>0) )
	{
		$zay_array=getlist(2,null,$user[zayavka]);
			if ($zay_array)
		 	{
		 	$zid=$user[zayavka];
				$OKBB=false;
				echo delteam (2,$user);
				if ($OKBB)
				{
				$user[zayavka]=0;
				$user[battle_t]=0;
				addchp ('<font color=red>Внимание!</font> '.$user['login'].' отозвал заявку.   ','{[]}'.nick7((int)($zay_array[$zid]['team1'])).'{[]}');
				}
			}
	}
	else
	if($_POST['cansel'] and ($user[zayavka]>0))
	{

			$zay_array=getlist(2,null,$user[zayavka]);
			if ($zay_array)
			{
			$zid=$user[zayavka];
			$btelo[id]=(int)($zay_array[$zid]['team2']);
			$btelo[zayavka]=$zid;
				$OKBB=false;
				echo delteam (2,$btelo);
				if ($OKBB)
				{
				addchp ('<font color=red>Внимание!</font> '.$user['login'].' отказался от поединка.  ','{[]}'.nick7($btelo[id]).'{[]}');
				}

			}
	}
	else
	if($_POST['confirm2'])
	{

	$zid=(int)($_REQUEST['gocombat']);

	if ($myeff['owntravma']>=1)
		{
		err('С вашей травмой нельзя драться!');

		}
	elseif ($user['hp']<($user['maxhp']/3))
		{
		err('Вы слишком ослаблены для боя!');
		}
	else
	 {
		if ((in_array($zid,$cbots[$user['level']])) and (time()-$_SESSION['bottout'][$zid]>$time_to_bot) )
		{
		//запоминаем время
		$_SESSION['bottout'][$zid]=time();
			//echo "mk_battle $zid ";
			//создаем бой
				$boter=mysql_fetch_array(mysql_query("SELECT * FROM oldbk.`users` WHERE `id` = '{$zid}' LIMIT 1;"));

					$boter_items=load_mass_items_by_id($boter);
					$boter_items['krit_mf']=(int)($boter_items['krit_mf']);
					$boter_items['max_u']=(int)($boter_items['max_u']);
					$boter_items['akrit_mf']=(int)($boter_items['akrit_mf']);
					$boter_items['uvor_mf']=(int)($boter_items['uvor_mf']);
					$boter_items['auvor_mf']=(int)($boter_items['auvor_mf']);
					$boter_items['bron1']=(int)($boter_items['bron1']);
					$boter_items['bron2']=(int)($boter_items['bron2']);
					$boter_items['bron3']=(int)($boter_items['bron3']);
					$boter_items['bron4']=(int)($boter_items['bron4']);

					mysql_query("INSERT INTO `users_clons` SET `login`='".$boter['login']."',`sex`='{$boter['sex']}',
					`level`='{$boter['level']}',`align`='{$boter['align']}',`klan`='{$boter['klan']}',`sila`='{$boter['sila']}',
					`lovk`='{$boter['lovk']}',`inta`='{$boter['inta']}',`vinos`='{$boter['vinos']}',
					`intel`='{$boter['intel']}',`mudra`='{$boter['mudra']}',`duh`='{$boter['duh']}',`bojes`='{$boter['bojes']}',`noj`='{$boter['noj']}',
					`mec`='{$boter['mec']}',`topor`='{$boter['topor']}',`dubina`='{$boter['dubina']}',`maxhp`='{$boter['maxhp']}',`hp`='{$boter['maxhp']}',
					`maxmana`='{$boter['maxmana']}',`mana`='{$boter['mana']}',`sergi`='{$boter['sergi']}',`kulon`='{$boter['kulon']}',`perchi`='{$boter['perchi']}',
					`weap`='{$boter['weap']}',`bron`='{$boter['bron']}',`r1`='{$boter['r1']}',`r2`='{$boter['r2']}',`r3`='{$boter['r3']}',`helm`='{$boter['helm']}',
					`shit`='{$boter['shit']}',`boots`='{$boter['boots']}',`nakidka`='{$boter['nakidka']}',`rubashka`='{$boter['rubashka']}',`shadow`='{$boter['shadow']}',`battle`=1,`bot`=1,
					`id_user`='{$boter['id']}',`at_cost`='{$boter_items['allsumm']}',`kulak1`=0,`sum_minu`='{$boter_items['min_u']}',
					`sum_maxu`='{$boter_items['max_u']}',`sum_mfkrit`='{$boter_items['krit_mf']}',`sum_mfakrit`='{$boter_items['akrit_mf']}',
					`sum_mfuvorot`='{$boter_items['uvor_mf']}',`sum_mfauvorot`='{$boter_items['auvor_mf']}',`sum_bron1`='{$boter_items['bron1']}',
					`sum_bron2`='{$boter_items['bron2']}',`sum_bron3`='{$boter_items['bron3']}',`sum_bron4`='{$boter_items['bron4']}',`ups`='{$boter_items['ups']}',
					`injury_possible`=0, `battle_t`=2;");
					$bot = mysql_insert_id();
					//делаем масив для бота шоб не перечитывать из базы
					$bot_data=$boter;
					$bot_data[id]=$bot;
					$bot_data[login]=$boter['login'].""
					;


					mysql_query("INSERT INTO `battle`
						(
							`id`,`coment`,`teams`,`timeout`,`type`,`status`,`t1`,`t2`,`to1`,`to2`,`t1hist`,`t2hist`
						)
						VALUES
						(
							NULL,'','','3','1','0','".$user['id']."','".$bot."','".time()."','".time()."','".BNewHist($user)."','".BNewHist($boter)."'
						)");

					$id = mysql_insert_id();
					mysql_query("INSERT IGNORE INTO battle_dam_exp (battle,owner) VALUES ('{$id}','{$user['id']}')");
					// апдейтим бота
					mysql_query("UPDATE `users_clons` SET `battle` = {$id} WHERE `id` = {$bot} LIMIT 1;");
					$bot_data[battle]=$id;
					// создаем лог
					$rr = "<b>".nick_hist($user)."</b> и <b>".nick_hist($bot_data)."</b>";
					addlog($id,"!:S:".time().":".BNewHist($user).":".BNewHist($bot_data)."\n");
					//тренеровочный бой чар за сторону 1
					mysql_query("UPDATE users SET `battle` ={$id},`zayavka`=0, `battle_t`=1 WHERE `id`= {$user['id']};");
				echo "<script>location.href='fbattle.php';</script>";
				zaydie();

			//
		}
		else
		 if ($zid>0)
		  {
			$zay_array=getlist(2,null,$zid);
			 if ($zay_array)
		 	{
		 	$OKADD=false;
			echo addteam (2,$user,$myeff,$zay_array[$zid], $zid);
			if ($OKADD)
				{

				if (( $user['hidden'] > 0 ) and ( $user['hiddenlog'] =='' ) )
				{
				$log_nick='<i>Невидимка</i>';
				$ac='';
				}
				elseif (( $user['hidden'] > 0 ) and ( $user['hiddenlog'] !='' ) )
				{
				 $ftelo = load_perevopl($user);
				 $log_nick=$ftelo['login'];
				 if ($ftelo['sex'] == 1) { $ac = ""; } else { $ac="а"; }
				}
				else
				{
				 $log_nick=$user['login'];
				 if ($user['sex'] == 1) { $ac = ""; } else { $ac="а"; }
				}


				addchp ('<font color=red>Внимание!</font> '.$log_nick.' принял'.$ac.' заявку, нужно принять вызов или отказать.  ','{[]}'.nick7((int)($zay_array[$zid]['team1'])).'{[]}');
				//run battle if id hidden by fred
				if (($user['hidden']>0)and ($user['hiddenlog']==''))
					{
					$zay_array=getlist(2,null,$zid);
				 	if (($zay_array[$zid][team2]!='') AND ($zay_array[$zid][team1]!=''))
				 		{
						battlestart($user,$zay_array[$zid], 2 );
						}
					}

				}

			}
		  }
		}
	}
	elseif(($_POST['gofi']) and (ustatus($user)==1) )
		{
			$zay_array=getlist(2,null,$user[zayavka]);
			 if ($zay_array)
		 	{
		 	$zid=$user[zayavka];

		 	if (($zay_array[$zid][team2]!='') AND ($zay_array[$zid][team1]!=''))
		 		{
				battlestart( $user, $zay_array[$zid], 2 );
				}

			}
		}

	echo "</b></font>";
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	$zay_array=getlist(2,$_SESSION['view']);

	echo '<table cellspacing=0 cellpadding=0><tr><td>';

	if (ustatus($user)==0)
	{
	/*
		Таймаут
		<SELECT NAME=timeout>
		<OPTION value=3>3 мин.</OPTION>
		<OPTION value=4>4 мин.</OPTION>
		<OPTION value=5>5 мин.</OPTION>
		<OPTION value=7>7 мин.</OPTION>
		<OPTION value=10 selected>10 мин.</OPTION>
		</SELECT>
	*/
		echo '<FIELDSET><LEGEND><B>Подать заявку на бой</B> </LEGEND>';
		echo "<table border=0>";
		if ($user['level']<=7)
		{
		echo "<tr><td>Начать тренировочный бой: </td>";
		//echo "<input type=button class='button-mid btn' onclick=\"location.href='zayavka.php?open=1&level=fiz&trainstart=2';\" value=\"кулачный\"> ";
		echo "<td><input type=button class='button-mid btn' onclick=\"location.href='zayavka.php?open=1&level=fiz&trainstart=1';\" value=\"с оружием\"> </td></tr>";
		}


		echo "<tr><td>Подать заявку на бой:</td>";
		echo "<td><input type=button class='button-mid btn' onclick=\"location.href='zayavka.php?open=1&level=fiz&k=1';\" value=\"с оружием\"> ";
		echo "<input type=button class='button-mid btn' onclick=\"location.href='zayavka.php?open=1&level=fiz&k=4';\" value=\"кулачный\"> ";
		if ($user[level]>=5)
			{
			echo "<input type=button class='button-mid btn' onclick=\"location.href='zayavka.php?open=1&level=fiz&k=6';\" value=\"кровавый\"> ";
			}
		echo "</td></tr>";
		/*echo ' Тип боя <SELECT NAME=k id=htypefiz onchange="showinfiz()">
		 	<OPTION value=1>с оружием</OPTION>
		 	<OPTION value=4>кулачный</OPTION>';
		if ($user[level]>=5)
			{
		echo '<OPTION value=6>кровавый</OPTION>';
			}
	 	echo '</SELECT><INPUT class="button-big btn" TYPE=submit name=open value="Подать заявку">&nbsp;';
	 	echo '<span id="kulakinffiz" style="display: none;"><small>(не учитывается в квестах и не получает статус)</small></span>'; */
	 	echo "</table>";
	 	echo '<br><span><small>(кулачный бой не учитывается в квестах и не получает статус)</small></span>';

	 	echo '</FIELDSET>';
	}
	elseif(( ustatus($user)==1 ) and (is_array($zay_array[$user[zayavka]]) ) )
	{
		if ($zay_array[$user[zayavka]][team2]!='')
		{
			echo "<B><font color=red>Внимание".$zay_array[$user['zayavka']]['t2hist']." принял заявку на бой, нужно отказать или принять вызов.</font></b> <input class='button-mid btn' type=submit value='Битва!' name=gofi> <input class='button-mid btn' type=submit value='Отказать' name=cansel>";
		}
		else {
			echo "Заявка на бой подана, ожидаем противника. <input type=submit class='button-big btn' name=back value='Отозвать заявку'>";

			if(($user['level'] <= 7 OR $user['id']==14897 ))
			{

				if($_GET['trainstart']==1 && $user['hp'] > $user['maxhp']*0.33)
				{

					//Удаляем свою заявку если она пустая
					mysql_query("DELETE FROM `zayavka` WHERE id='{$user[zayavka]}' and team1='{$user[id]};' and team2='' ");
					if (mysql_affected_rows()>0)
					{
					//Создаем тренеровочный бой для 0-х уровней


					$user_items=load_mass_items_by_id($user);

					$user_items['krit_mf']=(int)($user_items['krit_mf']*0.8);
					$user_items['max_u']=(int)($user_items['max_u']*0.9);
					$user_items['akrit_mf']=(int)($user_items['akrit_mf']*0.8);
					$user_items['uvor_mf']=(int)($user_items['uvor_mf']*0.8);
					$user_items['auvor_mf']=(int)($user_items['auvor_mf']*0.8);
					$user_items['bron1']=(int)($user_items['bron1']*0.8);
					$user_items['bron2']=(int)($user_items['bron2']*0.8);
					$user_items['bron3']=(int)($user_items['bron3']*0.8);
					$user_items['bron4']=(int)($user_items['bron4']*0.8);

					mysql_query("INSERT INTO `users_clons` SET `login`='".$user['login']." (клон 1)',`sex`='{$user['sex']}',
					`level`='{$user['level']}',`align`='{$user['align']}',`klan`='{$user['klan']}',`sila`='{$user['sila']}',
					`lovk`='{$user['lovk']}',`inta`='{$user['inta']}',`vinos`='{$user['vinos']}',
					`intel`='{$user['intel']}',`mudra`='{$user['mudra']}',`duh`='{$user['duh']}',`bojes`='{$user['bojes']}',`noj`='{$user['noj']}',
					`mec`='{$user['mec']}',`topor`='{$user['topor']}',`dubina`='{$user['dubina']}',`maxhp`='{$user['maxhp']}',`hp`='{$user['maxhp']}',
					`maxmana`='{$user['maxmana']}',`mana`='{$user['mana']}',`sergi`='{$user['sergi']}',`kulon`='{$user['kulon']}',`perchi`='{$user['perchi']}',
					`weap`='{$user['weap']}',`bron`='{$user['bron']}',`r1`='{$user['r1']}',`r2`='{$user['r2']}',`r3`='{$user['r3']}',`helm`='{$user['helm']}',
					`shit`='{$user['shit']}',`boots`='{$user['boots']}',`nakidka`='{$user['nakidka']}',`rubashka`='{$user['rubashka']}',`shadow`='{$user['shadow']}',`battle`=1,`bot`=1,
					`id_user`='{$user['id']}',`at_cost`='{$user_items['allsumm']}',`kulak1`=0,`sum_minu`='{$user_items['min_u']}',
					`sum_maxu`='{$user_items['max_u']}',`sum_mfkrit`='{$user_items['krit_mf']}',`sum_mfakrit`='{$user_items['akrit_mf']}',
					`sum_mfuvorot`='{$user_items['uvor_mf']}',`sum_mfauvorot`='{$user_items['auvor_mf']}',`sum_bron1`='{$user_items['bron1']}',
					`sum_bron2`='{$user_items['bron2']}',`sum_bron3`='{$user_items['bron3']}',`sum_bron4`='{$user_items['bron4']}',`ups`='{$user_items['ups']}',
					`injury_possible`=0, `battle_t`=2;");
					$bot = mysql_insert_id();
					//делаем масив для бота шоб не перечитывать из базы
					$bot_data=$user;
					$bot_data[id]=$bot;
					$bot_data[login]=$user['login']." (клон 1)";

					mysql_query("INSERT INTO `battle`
						(
							`id`,`coment`,`teams`,`timeout`,`type`,`status`,`t1`,`t2`,`to1`,`to2`,`t1hist`,`t2hist`
						)
						VALUES
						(
							NULL,'','','3','1','0','".$user['id']."','".$bot."','".time()."','".time()."','".BNewHist($user)."','".BNewHist($bot_data)."'
						)");

					$id = mysql_insert_id();
					// апдейтим бота

					mysql_query("UPDATE `users_clons` SET `battle` = {$id} WHERE `id` = {$bot} LIMIT 1;");
					$bot_data[battle]=$id;

					// создаем лог
					$rr = "<b>".nick_hist($user)."</b> и <b>".nick_hist($bot_data)."</b>";

//					addlog($id,"Часы показывали <span class=date>".date("Y.m.d H.i")."</span>, когда ".$rr." бросили вызов друг другу. <BR>");
					addlog($id,"!:S:".time().":".BNewHist($user).":".BNewHist($bot_data)."\n");
					//тренеровочный бой чар за сторону 1
					mysql_query("UPDATE users SET `battle` ={$id},`zayavka`=0, `battle_t`=1 WHERE `id`= {$user['id']};");
				echo "<script>location.href='fbattle.php';</script>";
				zaydie();
				}
					///=======================================================================================
				}

				echo " или <input class='button-big btn' type=button onclick=\"location.href='zayavka.php?level=fiz&trainstart=1';\" value=\"Начать тренировочный бой\">";

			}
			}
	}
	if( (ustatus($user)==2 ) and (is_array($zay_array[$user[zayavka]]) ) )
	{
		echo "Ожидаем подтверждения боя. <input class='button-big btn' type=submit name=back2 value='Отозвать заявку'>";
	}
	echo '</td></tr></table></TD><TD align=right valign=top rowspan=2><INPUT class="button-mid btn" TYPE=submit name=tmp value="Обновить"><BR><FIELDSET style="width:150px;">
	<LEGEND>Показывать заявки</LEGEND><table cellspacing=0 cellpadding=0 ><tr><td width=1%><input type=radio name=view value="'.$user['level'].'" '.(($_SESSION['view'] == $user['level'])?"checked":"").'></td><td>моего уровня</td></tr><tr><td><input type=radio name=view value="0" '.(($_SESSION['view'] == null || $_SESSION['view'] == 0)?"checked":"").'></td><td>все</td></tr></table></FIELDSET>';
	echo '<tr><td>';

	if (($zay_array) or (is_array($cbots[$user['level']])) )
	{
		if ($user[zayavka]==0) echo '<INPUT TYPE=hidden name=level value=fiz><INPUT TYPE=submit class="button-big btn" value="Принять вызов" NAME=confirm2><BR>';

		$co_fiz=0;
		foreach ( $zay_array as $k => $v )
			{
			echo showfiz($v);
			$co_fiz++;
			}

			if (is_array($cbots[$user['level']]))
			{
			//если есть боты для этого уровня
			echo showsbots($cbots[$user['level']]);
			$co_fiz++;
			}
		if ($co_fiz>=10)
		{
		if ($user[zayavka]==0)	echo '<INPUT class="button-big btn" TYPE=submit value="Принять вызов" NAME=confirm2>';
		}
	}

	echo '</TD></TR></TABLE>';
	zaydie();
}
else
if ($_REQUEST['level']=='dgv2')
{
$all_good=true;
// новые бои склонок
	if($user['level']<9) {
		echo "<BR><BR><BR><CENTER><B>Бои склонностей доступны с 9 уровня.</b></CENTER>";
	$all_good=false;
	}

	if($user['align']==0) {
		echo "<BR><BR><BR><CENTER><B>У Вас нет склонности...</b></CENTER>";
	$all_good=false;
	}
	if($user['align']==4) {
		echo "<BR><BR><BR><CENTER><B>Хаосникам тут делать нечего...</b></CENTER>";
	$all_good=false;
	}

	if($user['align']==5) {
		echo "<BR><BR><BR><CENTER><B>Бои склонностей не доступны для Абсолютного хаоса.</b></CENTER>";
	$all_good=false;
	}


	if (isset($myeff[830])) {
		echo "<BR><BR><BR><CENTER><B>Вы находитесь под медитацией!</b></CENTER>";
	$all_good=false;
	}

$help_message='<FIELDSET style="text-align:justify; width:95%; height:100%; padding:5px;"><LEGEND style="margin: 0 auto;"><B>Подсказка</B> </LEGEND>
				<small>
&nbsp;&nbsp;&nbsp;Чтобы попасть в бой склонностей, вам необходимо встать в очередь. Выйти из очереди можно в любое время до того, как будет сформирована заявка.<br><br>
&nbsp;&nbsp;&nbsp;Раз в минуту из противоборствующих склонностей формируется заявка 5 на 5 случайного типа - с магией или без магии.<br><br>
&nbsp;&nbsp;&nbsp;Если персонажей одной из склонности недостаточно для поединка, то группа может быть доукомплектована другой склонностью, не стоящей напротив.<br><br>
&nbsp;&nbsp;&nbsp;Все бои склонностей закрыты от вмешательства. <br><br>
&nbsp;&nbsp;&nbsp;Шанс получения награды в виде черепов и осколков есть, как у победителей, так и у проигравших (только осколков), но с более низким шансом выпадения.<br><br>
&nbsp;&nbsp;&nbsp;<b>Уровневые группы:</b> 9 уровень, 10 уровень, 11 уровень, 12 уровень, 13-14 уровень</small></FIELDSET>';

	echo "<table border=0 width=100%><tr><td width=15%>&nbsp;</td><td width=70%>";
	echo "<CENTER><H3>Бой склонностей</H3></CENTER>";
	if (($op == true and $all_good==true) /*or ($user['klan']=='radminion')*/)
		{

			if (($_POST['exitdgv2']) and ($user['zayavka']>100000000) and ($user['battle']==0) )
			{
					$turn=mysql_fetch_assoc(mysql_query('SELECT * FROM zayavka_turn WHERE id='.$user['zayavka']));
					if (($turn['id']>0) and ($turn['zayid']==0) and (time()-strtotime($turn['mktime'])<300))
					{
						echo "<BR><center><font color=red><b>Не так быстро! Выйти из очереди можно не раньше чем через 5 минут.</b></font></center>";
					}
					else
					if (($turn['id']>0) and ($turn['zayid']==0) )
						{
						//можно выйти
						mysql_query("DELETE FROM zayavka_turn WHERE id=".$user['zayavka']);
						mysql_query('UPDATE users SET `zayavka` = 0, battle_t=0  WHERE id='.$user['id']);
						$user['zayavka']=0;
						echo "<BR><center><font color=red><B>Вы покинули очередь на бой склонностей!</b></font></center>";
						}
			}


			if (($user['hp'] < $user['maxhp']*0.33) and ($_POST['open']))
			{
				echo "<font color=red> Вы слишком ослаблены для боя, восстановитесь.</font>";
			}
			else
			if (($_POST['open']) and ($user['zayavka']==0) and ($user['battle']==0))
			{
//				print_r($_POST);
				$align=(int)($user['align']);
				if ($user['klan']=='pal')
					{
					$align=6;
					}

				mysql_query('INSERT INTO `oldbk`.`zayavka_turn` SET `owner`="'.$user['id'].'",`lvl`="'.$user['level'].'",`align`="'.$align.'" on DUPLICATE KEY UPDATE lvl="'.$user['level'].'",`align`="'.$align.'" , zayid=0 , mktime=NOW(); ');
				if (mysql_affected_rows()>0)
					{
					$skl_zay_id=mysql_insert_id();
					mysql_query('UPDATE users SET `zayavka` = "'.$skl_zay_id.'", battle_t=0  WHERE id="'.$user['id'].'" and zayavka=0 and battle=0  ');
						if (mysql_affected_rows()>0)
							{
							$user['zayavka']=$skl_zay_id;
							echo "<BR><center><font color=red><B>Вы вошли в очередь на бой склонностей!</b></font></center>";
							$look=true;
							}
					}
			}

				if ($user['zayavka']>100000000) // человек стоит в очереди на бой
				{
				//в очереди
					$turn=mysql_fetch_assoc(mysql_query('SELECT * FROM zayavka_turn WHERE id='.$user['zayavka']));
					if (($turn['id']>0) and ($turn['zayid']>0))
						{
						//уже готовая заявка ждет старта боя
						 //ищем и показываем заявку на бой
						 $get_list=mysql_fetch_assoc(mysql_query('SELECT * FROM zayavka WHERE id='.$turn['zayid']));
								 if ($get_list['id']>0)
								 {
								 $sek=$get_list['start']-time();
								 if ($sek<0) { $sek=0; }

								 echo '
								 <BR><CENTER><b>Ожидаем начала боя склонностей. Бой начнется через:'.$sek.' сек. </b>';

								 echo '<table border=0>
								 	<tr>
								 		<td colspan=3>';
								 if ($get_list['nomagic']>0)
								 	{
								 	echo "Бой <img src='http://capitalcity.oldbk.com/i/fighttype3.gif'><u>групповой, без магии</u>";
								 	}
								 	else
								 	{
								 	echo "Бой <img src='http://capitalcity.oldbk.com/i/fighttype3.gif'><u>групповой</u>";
								 	}

 								 if ($get_list['autoblow']>0)
 								 	{
 								 	echo ", <img src='http://capitalcity.oldbk.com/i/achaos.gif'><u>c автоударом</u>";
 								 	}
 								 if ($get_list['blood']>0)
 								 	{
 								 	echo ", <img src='http://i.oldbk.com/i/fighttype6.gif'><u>кровавый</u>";
 								 	}

								 echo '<br></td>
								 	</tr>
								 	<tr>
								 		<td><b>['.$get_list['t1hist'].']</b></td>
								 		<td> <i>против</i></td>
								 		<td><b>['.$get_list['t2hist'].']</b></td>
								 	</tr>
								 </table>
								 </CENTER>';
								}
						}
						else
						{
						//просто в очереди
						if ($look!=true) { echo "<CENTER><B>Вы находитесь в очереди на бой склонностей, который будет сформирован автоматически, как только наберется необходимое количество человек!</b></CENTER><BR>"; }


						echo '<CENTER><FIELDSET style="text-align:justify; width:300px; height:100%;"><LEGEND><B>Очередь</B> </LEGEND>';

							if (time()-strtotime($turn['mktime'])<300)
							{
							$out_mess='Вы сможете выйти из заявки через '. prettyTime(null,(strtotime($turn['mktime'])+300));
							}
							else
							{
							$out_mess='<INPUT class="button-big btn" TYPE=submit value="Покинуть очередь" class="button-mid btn" name=exitdgv2>';
							}

						echo show_aligns_zay('<div align=center><br>'.$out_mess.'<INPUT TYPE=hidden name=level value=dgv2></div>');

						echo '</FIELDSET></CENTER>';

						}
				}
				else
				{
					echo '<CENTER>
					<table border=0>
					<tr valign=top>
					<td height=100%>
					<FIELDSET style="text-align:justify; width:310px; height:100%;"><LEGEND style="margin: 0 auto;"><B>Заявка</B> </LEGEND>';
					echo '<div align=center><br>';
						if ($user['zayavka']==0)
							{
							echo show_aligns_zay('<BR><BR><INPUT class="button-big btn" TYPE=submit value="Войти в очередь на бой" name=open><INPUT TYPE=hidden name=level value=dgv2><br><br>');
							}
							else
							{
							echo show_aligns_zay('');
							}
					echo '</div>';
					echo '</FIELDSET></td><td>';

					echo $help_message;

					echo '</td></tr></table></CENTER>';
				}

		}
	else if ( ($op == false) and ($user['zayavka']>100000000)) // человек стоит в очереди на бой  но ДП уже закончилось
		{
			$turn=mysql_fetch_assoc(mysql_query('SELECT * FROM zayavka_turn WHERE id='.$user['zayavka']));
					if (($turn['id']>0) and ($turn['zayid']==0))
						{
						//можно выйти
						mysql_query("DELETE FROM zayavka_turn WHERE id=".$user['zayavka']);
						mysql_query('UPDATE users SET `zayavka` = 0, battle_t=0  WHERE id='.$user['id']);
						$user['zayavka']=0;
						echo "<BR><center><font color=red><B>Вы покинули очередь на бой склонностей, время противостояния закончилось!</b></font></center>";
						}
		}
	else
		{
				echo '<CENTER>
				<table border=0>
				<tr valign=top>
				<td height=100%>
				<FIELDSET style="text-align:justify; width:310px; height:100%;"><LEGEND style="margin: 0 auto;"><B>Заявка</B> </LEGEND>';
				echo '<div align=center><br>';
				$tchk = CheckOpNextDay();
				if ( ($tchk === true) or ((time()>$KO_start_time29) and (time()<$KO_fin_time29)) ) //Победный май
				{
					echo "Сегодня день Противостояния!";
				} else {
					echo "Следующий день противостояния склонностей начнется ".$tchk;
				}
				echo '</div>';
				echo '</FIELDSET></td><td>';

				echo $help_message;

				echo '</td></tr></table></CENTER>';
		}
			 echo '</td><td  width=15% valign=top> ';
			echo '<div align=right><INPUT class="button-mid btn" TYPE=submit name=tmp value="Обновить"><br>';
			echo '<FIELDSET style="width:150px;"><LEGEND>Показывать заявки</LEGEND>
			<table cellspacing=0 cellpadding=0 ><tr><td width=1%><input type=radio name=view value="'.$user['level'].'" '.(($_SESSION['view'] == $user['level'])?"checked":"").'></td><td>моего уровня</td></tr><tr><td><input type=radio name=view value="0" '.(($_SESSION['view'] == null || $_SESSION['view'] == 0)?"checked":"").'></td><td>все</td></tr></table></FIELDSET>';
			 echo "</td></tr></table>";
echo '</FORM>';
zaydie();
}/*
else
if ($_REQUEST['level']=='dgv')
{
if ($user[id]==14897) 	print_r($_POST);
$zay_array=getlist(3,null);
	if($user['level']<2) {
		echo "<BR><BR><BR><CENTER><B>Групповые бои доступны с 2 уровня.</b></CENTER>";
		zaydie();
	}

	if($user['align']==0) {
		echo "<BR><BR><BR><CENTER><B>У Вас нет склонности...</b></CENTER>";
		zaydie();
	}
	if($user['align']==4) {
		echo "<BR><BR><BR><CENTER><B>Хаосникам тут делать нечего...</b></CENTER>";
		zaydie();
	}

	if($user['align']==5) {
		echo "<BR><BR><BR><CENTER><B>Бои склонностей не доступны для Абсолютного хаоса.</b></CENTER>";
		zaydie();
	}


	if (isset($myeff[830])) {
		echo "<BR><BR><BR><CENTER><B>Вы находитесь под медитацией!</b></CENTER>";
		zaydie();
	}


////////////Форма - подачи заявки
	if($_POST['open1'] && !$user['zayavka'])
	{
		echo '
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top">
			<TABLE border=0><TR><TD>
			<H3>Подать заявку на групповой бой склонностей</H3>
			Начало боя через <SELECT NAME=startime>
			<option value=600>10 минут
			<option value=900 selected>15 минут
			<option value=1800>30 минут
			<option value=2700>45 минут
			<option value=3600>1 час
			</SELECT>
			&nbsp;&nbsp;&nbsp;&nbsp;Таймаут <SELECT NAME=timeout><OPTION value=3>3 мин.<OPTION value=4>4 мин.<OPTION value=5 selected>5 мин.<OPTION value=7>7 мин.<OPTION value=10>10 мин.</SELECT>
			<BR>
			Уровни бойцов &nbsp;&nbsp;<SELECT NAME=levellogin1>
			<option value=0>любой
			<option value=1>только моего и ниже
			<option value=2>только ниже моего уровня
			<option value=3>только моего уровня
			<option value=4>не старше меня более чем на уровень
			<option value=5>не младше меня более чем на уровень
			<option value=6>мой уровень +/- 1
			<option value=99>клан
			</SELECT><BR>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr align=center>
			<td align=center>
				За меня <SELECT name=am1 disabled="disabled">
				<option value=2 '; if (((int)($user[align]))==2) {echo "selected";} echo '  >Нейтралы</option>
				<option value=3 '; if (((int)($user[align]))==3) {echo "selected";} echo '  >Темные</option>
				<option value=6 '; if ((((int)($user[align]))==6) OR (((int)($user[align]))==1))  { echo "selected"; } echo '>Светлые</option>
				</SELECT>
			</td>
		       <td align=center>
				Против меня <SELECT name=ae1 >
				<option value=0>-</option>
				<option value=2>Нейтралы</option>
				<option value=3>Темные</option>
				<option value=6>Светлые</option>
				</SELECT>
			</td>
			</tr>
			<tr>
			   <td align=center>
			   За меня <SELECT name=am2 >
				<option value=0>-</option>
				<option value=2>Нейтралы</option>
				<option value=3>Темные</option>
				<option value=6>Светлые</option>
				</SELECT>
			   </td>
			<td align=center>
				Против меня <SELECT name=ae2 >
				<option value=0>-</option>
				<option value=2>Нейтралы</option>
				<option value=3>Темные</option>
				<option value=6>Светлые</option>
				</SELECT>
			</td>
			  </tr>
			</table>


			<BR>

			<BR>

			';
			if ($user[level]>=5) { echo '<INPUT TYPE=checkbox NAME=travma> Бой без правил (<font class=dsc>проигравшая сторона получает инвалидность</font>)<BR>'; }

			echo 'Комментарий к бою <INPUT TYPE=text NAME=cmt maxlength=40 size=40>
			</TD></TR>
			<TR><TD align=center>
			<INPUT TYPE=submit value="Начнем месилово! :)" name=open>
			</TD></TR>
			</TABLE>

			</td>
			<td valign="top"><INPUT TYPE=hidden name=level value=dgv>
			</FORM>
			';
			zaydie();
	}

////форма - захода в заявку - выбор сторон
	if($_POST['goconfirm'] && !$user['zayavka'] && !$_POST['tmp'])
	{
	$zid=(int)($_POST['gocombat']);
		if (is_array($zay_array[$zid]))
		{

		echo '<TABLE width=100%><TR><TD>';

		$alish1="";$alish2="";
				if ($z[$_POST['gocombat']][am1]>0) {$alish1.="<img src='http://i.oldbk.com/i/align_{$z[$_POST['gocombat']][am1]}.gif'>";}
				if ($z[$_POST['gocombat']][am2]>0) {$alish1.="<img src='http://i.oldbk.com/i/align_{$z[$_POST['gocombat']][am2]}.gif'>";}
				if ($z[$_POST['gocombat']][ae1]>0) {$alish2.="<img src='http://i.oldbk.com/i/align_{$z[$_POST['gocombat']][ae1]}.gif'>";}
				if ($z[$_POST['gocombat']][ae2]>0) {$alish2.="<img src='http://i.oldbk.com/i/align_{$z[$_POST['gocombat']][ae2]}.gif'>";}

		echo "<B>Ожидаем начала группового боя...</B><BR>Бой начнется через: ".round((($zay_array[$zid]['start']+10)-time())/60,1)." мин.";
		echo '</TD><TD align=right><INPUT TYPE=submit value="Вернуться"></TD></TR></TABLE><H3>На чьей стороне будете сражаться?</H3>
				<TABLE align=center cellspacing=4 cellpadding=1><TR><TD bgcolor=99CCCC><B>Группа 1:'.$alish1.'</B><BR>
				Максимальное кол-во: '.$zay_array[$zid]['t1c'].'<BR>
				Ограничения по уровню: '.($zay_array[$zid]['t1min']==99?'клан':$zay_array[$zid]['t1min']." - ".$zay_array[$zid]['t1max']).'
				</TD><TD bgcolor=99CCCC><B>Группа 2:'.$alish2.'</B><BR>
				Максимальное кол-во: '.$zay_array[$zid]['t2c'].'<BR>
				Ограничения по уровню: '.($zay_array[$zid]['t2min']==99?'клан':$zay_array[$zid]['t2min']." - ".$zay_array[$zid]['t2max']).'
				</TD></TR><TR>
				<TD align=center>';
		echo BNewRender($zay_array[$zid][t1hist]);

		echo '</TD><TD align=center>';
		echo substr($zay_array[$zid][t2hist],1);
		echo '</TD></TR><TR><TD align=center><INPUT TYPE=submit name=confirm1 value="Я за этих!"></TD><TD align=center><INPUT TYPE=submit name=confirm2 value="Я за этих!"></TD></TR></TABLE>
			<INPUT TYPE=hidden name=gocombat value="'.$zid.'"><INPUT TYPE=hidden name=level value=dgv>';
		zaydie();
		}
	}
	echo "<font color=red><b>";

/// вход в заявку непосрадственно за сторону 1
	if(($_POST['confirm1']) && $_POST['gocombat'] && !$user['zayavka'] && !$_POST['tmp'])
	{
	$zid=(int)($_POST['gocombat']);
		if (is_array($zay_array[$zid]))
		{
		echo "<font color=red><b>";
		$OKADD=false;
		echo addteam (1,$user,$myeff,$zay_array[$zid], $zid);
		if ($OKADD)
			{
			$zay_array=getlist(3,null); //перечитываем
			}
		echo "</b></font>";
		}
	}
	else
/// вход в заявку непосрадственно за сторону 2
	if(($_POST['confirm2']) && $_POST['gocombat'] && !$user['zayavka'] && !$_POST['tmp'])
	{
	$zid=(int)($_POST['gocombat']);
		if (is_array($zay_array[$zid]))
		{
		echo "<font color=red><b>";
		$OKADD=false;
		echo addteam (2,$user,$myeff,$zay_array[$zid], $zid);
		if ($OKADD)
			{
			$zay_array=getlist(3,null); //перечитываем
			}
		echo "</b></font>";
		}
	}

	/////////////////////////////////
	/////// Подача склонок заявки ///////
	/////////////////////////////////
	if($_POST['open'] && !$user['zayavka']) {
		//print_r($_REQUEST);
		//уровни все 2-21- уже нет :)
			switch($_POST['levellogin1']) {
			case 0 :
				$min1 = 2;
				$max1 = 21;
			break;
			case 1 :
				$min1 = 2;
				$max1 = $user['level'];
			break;
			case 2 :
				$min1 = 2;
				$max1 = $user['level']-1;
			break;
			case 3 :
				$min1 = $user['level'];
				$max1 = $user['level'];
			break;
			case 4 :
				$min1 = $user['level'];
				$max1 = $user['level']+1;
			break;
			case 5 :
				$min1 = $user['level']-1;
				$max1 = $user['level'];
			break;
			case 6 :
				$min1 = (int)$user['level']-1;
				$max1 = (int)$user['level']+1;
			break;
			// KLANNNNNNNNNNNNNN
			case 99 :
				$min1 = 99;
				$max1 = 99;
			break;
		}
		$min2=$min1;
		$max2 = $max1;
		$zaykt = 2; // тип групповой не кулачный
		if($_POST['travma']) { $blood = 1; } else { $blood = 0;	}
		$nlogin1 =99;//кол. за тиму 1
		$nlogin2 =99;//кол. за тиму 2

		if (((int)($user[align]))==2) // нейтралы
			{
			$am1=2;
			}
			elseif (((int)($user[align]))==3) // темные
			{
			$am1=3;
			}
			elseif ((((int)($user[align]))==6) OR (((int)($user[align]))==1))  // свет и палы
			{
			$am1=6;
			}
		//print_r($_POST);
		$am2=(int)($_POST[am2]); if (($am2!=2) AND ($am2!=3) AND ($am2!=6)) {$am2=0; }
		$ae1=(int)($_POST[ae1]); if (($ae1!=2) AND ($ae1!=3) AND ($ae1!=6)) {$ae1=0; }
		$ae2=(int)($_POST[ae2]); if (($ae2!=2) AND ($ae2!=3) AND ($ae2!=6)) {$ae2=0; }

		//если склонки дублируются вторую в 0
		if ($am1==$am2) { $am2=0; }
		if ($ae1==$ae2) { $ae2=0; }


		if ( ($am1==6 OR $am2==6) AND ($ae1==6 OR $ae2==6 ) )
	 	   {
	 	    echo "Свет не воюет против Света...";
	 	   }
		else
		if ( ($am1==3 OR $am2==3) AND ($ae1==3 OR $ae2==3 ) )
	 	   {
	 	    echo "Тьма не воюет против Тьмы...";
	 	   }
		else
		// проверка на то что б за одну сторону не были свет и тьма
		if (
			( ($ae1==3) AND ($ae2==6) ) OR
			( ($ae1==6) AND ($ae2==3) ) OR
			( ($am1==3) AND ($am2==6) ) OR
			( ($am1==6) AND ($am2==3) )  )
	 	   {
	 	    echo "За одну сторону не могут быть Свет и Тьма...";
	 	   }
		else
		if (($ae1==0)AND($ae2==0))
			 {
			 echo "Вы не выбрали склонность противнику...";
			}
			else {
				echo addzayavka ($_POST['startime']/60,$_POST['timeout'], $nlogin1, $nlogin2, $zaykt, $min1, $min2, $max1, $max2, $_POST['cmt'], $user, $myeff , 3, 0, $blood, 0,0,0,$am1 , $am2, $ae1, $ae2);
				$zay_array=getlist(3,null); //перечитываем
				}
	}


	echo "</font></b><INPUT TYPE=hidden name=level value=dgv>";
	echo '<table cellspacing=0 cellpadding=0><tr><td>';


	if( ustatus($user)==0 )
	{
		echo '<INPUT TYPE=submit value="Подать новую заявку" name=open1>';
	}
	else if( (ustatus($user)>0) AND (is_array($zay_array[$user[zayavka]])) )
	{

			echo "<B>Ожидаем начала группового боя склонностей...</B><BR>Бой начнется через: ".round((($zay_array[$user['zayavka']]['start']+10)-time())/60,1)." мин.";
	}
	echo '</td></tr></table></TD><TD align=right valign=top rowspan=2>';
	echo '<INPUT TYPE=submit name=tmp value="Обновить"><BR>';
//	echo '<FIELDSET style="width:150px;"><LEGEND>Показывать заявки</LEGEND><table cellspacing=0 cellpadding=0 ><tr><td width=1%><input type=radio name=view value="'.$user['level'].'" '.(($_SESSION['view'] == $user[level])?"checked":"").'></td><td>моего уровня</td></tr><tr><td><input type=radio name=view value="0" '.(($_SESSION['view'] == null || $_SESSION['view'] == 0)?"checked":"").'></td><td>все</td></tr></table></FIELDSET>';
	echo '<tr><td width=85%>';


	if ($zay_array)
	{
	if ($user[zayavka]==0)  echo '<BR><INPUT TYPE=submit value="Принять участие в мясорубке" NAME=goconfirm><BR>';

		foreach ( $zay_array as $k => $v )
			{
			echo showgroup($v);
			}

	if ($user[zayavka]==0) 	echo '<INPUT TYPE=submit value="Принять участие в мясорубке" NAME=goconfirm>';
	}

	echo '</td></tr></table>';
}*/
else
/////////////////////////////////////////////////////////
if ($_REQUEST['level'] == 'group')
{
?>
<script type="text/javascript">
  function getXmlHttp()
  {
    var xmlhttp;
    try {
      xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
      try {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (E) {
        xmlhttp = false;
      }
    }
    if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
      xmlhttp = new XMLHttpRequest();
    }
    return xmlhttp;
  }
  function changeAlign(id)
  {
    var xmlhttp = getXmlHttp();
    xmlhttp.open('POST', 'zayavka.php', true);
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xmlhttp.send("get_clans_by_align=" + encodeURIComponent(id));
    xmlhttp.onreadystatechange = function() {
      if (xmlhttp.readyState == 4) {
        if(xmlhttp.status == 200) {
          var clandata = JSON.parse(xmlhttp.responseText);
          var text = "<option value='0'>Любой</option>";
          for (var i in clandata) {
            text += "<option value='" + clandata[i]['id'] + "'>" + clandata[i]['name'] + "</option>";
          }

          document.getElementById('enklannonly').innerHTML = text;
        }
      }
    };
  }

  function myClan()
  {
	if (document.getElementById('klanonly').checked)
	{
	document.getElementById("klanrecrut").disabled = false;
	}
	else
	{
	document.getElementById("klanrecrut").disabled = true;
	}
  }

</script>


<?

	/*if ($user['klan']!='radminion')
	{
		echo "<BR><BR><BR><CENTER><B>Групповые бои временно не работают.</b></CENTER>";
		zaydie();
	}*/

$zay_array=getlist(4,$_SESSION['view']);

	/*if($user['level']<40) {
		echo "<BR><BR><BR><CENTER><B>Групповые бои временно не работают.</b></CENTER>";
		die();
	}*/
	if($user['level']<2) {
		echo "<BR><BR><BR><CENTER><B>Тренировочные бои доступны с 2 уровня.</b></CENTER>";
		zaydie();
	}


	if (isset($myeff[830])) {
		echo "<BR><BR><BR><CENTER><B>Вы находитесь под медитацией!</b></CENTER>";
		zaydie();
	}


	if($_POST['open1'] && !$user['zayavka']) {
	//Таймаут <SELECT NAME=timeout><OPTION value=3>3 мин.<OPTION value=4>4 мин.<OPTION value=5 selected>5 мин.<OPTION value=7>7 мин.<OPTION value=10>10 мин.</SELECT>
		echo '
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top">
			<TABLE border=0><TR><TD>
			В тренировочных боях не начисляется опыт и не выдается репутация.
			<H3>Подать заявку на тренировочный бой</H3>
			Начало боя через <SELECT NAME=startime>
			<option value=300 selected>5 минут
			<option value=600>10 минут
			<option value=900 >15 минут

			</SELECT>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<BR><BR>
			Ваша команда <INPUT TYPE=text NAME=nlogin1 size=3 maxlength=2> бойцов<BR>';
				if ($user['klan']!='')
				{
				echo '<INPUT TYPE=checkbox NAME=klanonly id=klanonly onClick="myClan();"> Только мой клан<BR>'; //<подхватываем клан подающего заявку> (неактивная опция для персонажей без клана)
				echo '<INPUT TYPE=checkbox NAME=klanrecrut id=klanrecrut disabled = true> Разрешить заходить бойцам из основного и рекрут кланов<BR>'; //(неактивная опция для персонажа без клана)
				}

			echo 'Уровни союзников &nbsp;&nbsp;<SELECT NAME=levellogin1>
			<option value=0>любой
			<option value=1>только моего и ниже
			<option value=2>только ниже моего уровня
			<option value=3>только моего уровня
			<option value=4>не старше меня более чем на уровень
			<option value=5>не младше меня более чем на уровень
			<option value=6>мой уровень +/- 1
			</SELECT>
			<BR>
			<BR>
			Противники &nbsp;&nbsp;<INPUT TYPE=text NAME=nlogin2 size=3 maxlength=2> бойцов<BR>
			Только со склонностью:
			<SELECT NAME=alignonly  onchange="changeAlign(this.value);">
			<option value=1>Любой
			<option value=2>Нейтралы
			<option value=3>Тьма
			<option value=4>Свет
			</SELECT> <br>
			Только из клана: <SELECT NAME=enklannonly id=enklannonly >';

				$kln=mysql_query("SELECT id, short   from oldbk.clans where id not in (78,81,34,417,418,419,420,421,422,423,280,260,279,433) and time_to_del=0 order by short ");
				$outarray=array();
				echo '<option value=0>Любой';
				while ($clns = mysql_fetch_array($kln))
				{
				echo '<option value='.$clns['id'].'>'.$clns['short'];
				}

			 echo '</SELECT><br>

			<INPUT TYPE=checkbox NAME=enklanrecrut> Разрешить заходить бойцам из основного и рекрут кланов<br>

			Уровни противников <SELECT NAME=levellogin2>
			<option value=0>любой
			<option value=1>только моего и ниже
			<option value=2>только ниже моего уровня
			<option value=3>только моего уровня
			<option value=4>не старше меня более чем на уровень
			<option value=5>не младше меня более чем на уровень
			<option value=6>мой уровень +/- 1
			</SELECT><BR>';

			//echo '<INPUT TYPE=checkbox NAME=k> Кулачный бой (<font class=dsc>не учитывается в квестах и не получает статус</font>)<BR>';
			//echo '<INPUT TYPE=checkbox NAME=travma> Бой без правил (<font class=dsc>проигравшая сторона получает инвалидность</font>)<BR>';

			echo 'Комментарий к бою <INPUT TYPE=text NAME=cmt maxlength=40 size=40>
			</TD></TR>
			<TR><TD align=center>
			<INPUT class="button-big btn" TYPE=submit value="Создать заявку" name=open>
			</TD></TR>
			</TABLE>

			</td>
			<td valign="top"><INPUT TYPE=hidden name=level value=group>
			</FORM>';

			/*
			echo '<FORM METHOD=POST name="zay" id="zay" ACTION=zayavka.php name=F2>
			<TABLE border=0><TR><TD>
			<H3>Подать заявку на "футбольный" бой</H3>
			Начало боя через <SELECT NAME=startime>
			<option value=600>10 минут
			<option value=900 selected>15 минут
			<option value=1800>30 минут
			<option value=2700>45 минут
			<option value=3600>1 час
			</SELECT>
			&nbsp;&nbsp;&nbsp;&nbsp;Таймаут <SELECT NAME=timeout><OPTION value=3>3 мин.<OPTION value=4>4 мин.<OPTION value=5 selected>5 мин.<OPTION value=7>7 мин.<OPTION value=10>10 мин.</SELECT>
			<BR><BR>
			Тип матча <SELECT NAME=nlogin1>
			<option value=11>11 на 11
			<option value=5>5 на 5
			</SELECT>
			<BR>
			<BR><BR>
			Уровни бойцов &nbsp;&nbsp;<SELECT NAME=levellogin1>
			<option value=0>любой
			<option value=1>только моего и ниже
			<option value=2>только ниже моего уровня
			<option value=3>только моего уровня
			<option value=4>не старше меня более чем на уровень
			<option value=5>не младше меня более чем на уровень
			<option value=6>мой уровень +/- 1
			<option value=99>клан
			</SELECT><BR><BR>
			(<font class=dsc>все поединки без магии и без боевых абилити</font>)
			Бой на деньги: <SELECT NAME=price><OPTION value=0>нет<OPTION value=5>5 кр.<OPTION value=10>10 кр.<OPTION value=25>25 кр.<OPTION value=50>50 кр.</SELECT><br>
			Комментарий к бою <INPUT TYPE=text NAME=cmt maxlength=40 size=40>
			</TD></TR>
			<TR><TD align=center>
			<INPUT TYPE=submit value=\'Начнем "футбол" :)\' name=openfifa>
			</TD></TR>
			</TABLE>
				</td>
				</tr>
				</table>

			</TD>
			<TD align=right valign=top>
			<INPUT TYPE=submit value="Вернуться">
			</TD></TR></TABLE><INPUT TYPE=hidden name=level value=group>
			</FORM>
			';*/

			zaydie();
	}
	else
	if($_POST['goconfirm'] && !$user['zayavka'] && !$_POST['tmp'])
	{
	$zid=(int)($_POST['gocombat']);
		if (is_array($zay_array[$zid]))
		{
				$klns_data=array();
				$infklan_data=array();
				if ($zay_array[$zid]['klan1']>0) $klns_data[]=$zay_array[$zid]['klan1'];
				if ($zay_array[$zid]['klan2']>0) $klns_data[]=$zay_array[$zid]['klan2'];
				if ($zay_array[$zid]['reklan1']>0) $klns_data[]=$zay_array[$zid]['reklan1'];
				if ($zay_array[$zid]['reklan2']>0) $klns_data[]=$zay_array[$zid]['reklan2'];

				if (count($klns_data)>0)
					{
						$get_all=mysql_query("select *  from oldbk.clans where id in (".implode(",",$klns_data).")");
						if (mysql_num_rows($get_all)>0)
						{
							while ($rw = mysql_fetch_assoc($get_all))
								{
								$infklan_data[$rw['id']]='<img src="http://i.oldbk.com/i/align_'.$rw['align'].'.gif"  width=12 height=15><A HREF="http://oldbk.com/encicl/klani/clans.php?clan='.$rw['short'].'" target=_blank><img src="http://i.oldbk.com/i/klan/'.$rw['short'].'.gif" title="'.$rw['name'].'" >'.$rw['name'].'</A>';
								}
						}
					}




		echo '<TABLE width=100%><TR><TD>';
		echo "<B>Ожидаем начала тренировочного боя...</B><BR>Бой начнется через: ".round((($zay_array[$zid]['start'])-time())/60,1)." мин.";
		echo '</TD><TD align=right><INPUT class="button-mid btn" TYPE=submit value="Вернуться"></TD></TR></TABLE><H3>На чьей стороне будете сражаться?</H3><center>
				<TABLE align=center cellspacing=4 cellpadding=1><TR><TD bgcolor=99CCCC><B>Группа 1:</B><BR>
				Максимальное кол-во: '.$zay_array[$zid]['t1c'].'<BR>
				Ограничения по уровню: '.($zay_array[$zid]['t1min']==99?'клан':$zay_array[$zid]['t1min']." - ".$zay_array[$zid]['t1max']);
				if ($zay_array[$zid]['klan1']>0)
							{
							echo '<br>Клан:'.$infklan_data[$zay_array[$zid]['klan1']];
							}
				if ($zay_array[$zid]['reklan1']>0)
							{
							echo '<br>Клан:'.$infklan_data[$zay_array[$zid]['reklan1']];
							}
				echo '</TD><TD bgcolor=99CCCC><B>Группа 2:</B><BR>
				Максимальное кол-во: '.$zay_array[$zid]['t2c'].'<BR>
				Ограничения по уровню: '.($zay_array[$zid]['t2min']==99?'клан':$zay_array[$zid]['t2min']." - ".$zay_array[$zid]['t2max']);

				if ($zay_array[$zid]['alig2']>1)
					{
					//		<option value=1>Любой
					$ov[2]=' <img src="http://i.oldbk.com/i/align_2.gif" title="Нейтралы" width=12 height=15> Нейтралы ';
					$ov[3]=' <img src="http://i.oldbk.com/i/align_3.gif" title="Тьма" width=12 height=15> Тьма ';
					$ov[4]=' <img src="http://i.oldbk.com/i/align_6.gif" title="Свет" width=12 height=15> Свет';
					echo '<br>Только склонность:'.$ov[$zay_array[$zid]['alig2']];
					}

				if ($zay_array[$zid]['klan2']>0)
							{
							echo '<br>Клан:'.$infklan_data[$zay_array[$zid]['klan2']];
							}
				if ($zay_array[$zid]['reklan2']>0)
							{
							echo '<br>Клан:'.$infklan_data[$zay_array[$zid]['reklan2']];
							}

				echo '</TD></TR><TR>
				<TD align=center>';
		echo BNewRender($zay_array[$zid][t1hist]);
		echo '</TD><TD align=center>';
		echo BNewRender($zay_array[$zid][t2hist]);
		echo '</TD></TR><TR><TD align=center><INPUT class="button-mid btn" TYPE=submit name=confirm1 value="Я за этих!"></TD>
		<TD align=center><INPUT class="button-mid btn" TYPE=submit name=confirm2 value="Я за этих!"></TD></TR></TABLE></center>
		<INPUT TYPE=hidden name=gocombat value="'.$zid.'"><INPUT TYPE=hidden name=level value=group>';
		zaydie();
		}
	}
	else
	// in da battle
	if(($_POST['confirm1']) && $_POST['gocombat'] && !$user['zayavka'] && !$_POST['tmp'])
	{
	$zid=(int)($_POST['gocombat']);
		if (is_array($zay_array[$zid]))
		{
		echo "<font color=red><b>";
		$OKADD=false;
		echo addteam (1,$user,$myeff,$zay_array[$zid], $zid);
		if ($OKADD)
			{
			$zay_array=getlist(4,$_SESSION['view']); //перечитываем
			}
		echo "</b></font>";
		}
	}
	else
	if(($_POST['confirm2']) && $_POST['gocombat'] && !$user['zayavka'] && !$_POST['tmp'])
	{
	$zid=(int)($_POST['gocombat']);
		if (is_array($zay_array[$zid]))
		{
		echo "OK";
		echo "<font color=red><b>";
		$OKADD=false;
		echo addteam (2,$user,$myeff,$zay_array[$zid], $zid);
		if ($OKADD)
			{
			$zay_array=getlist(4,$_SESSION['view']); //перечитываем
			}
		echo "</b></font>";
		}
	}
	else
	/////////////////////////////////
	/////// Подача груп заявки ///////
	/////////////////////////////////
	if($_POST['open'] && !$user['zayavka'])
	{
		echo "<font color=red><b>";

		if (!(($_POST['levellogin1']>=0) and ($_POST['levellogin1']<=6) )) $_POST['levellogin1']=0;
		if (!(($_POST['levellogin2']>=0) and ($_POST['levellogin2']<=6) )) $_POST['levellogin2']=0;


		switch($_POST['levellogin1']) {
			case 0 :
				$min1 = 2;
				$max1 = 21;
			break;
			case 1 :
				$min1 = 2;
				$max1 = $user['level'];
			break;
			case 2 :
				$min1 = 2;
				$max1 = $user['level']-1;
			break;
			case 3 :
				$min1 = $user['level'];
				$max1 = $user['level'];
			break;
			case 4 :
				$min1 = $user['level'];
				$max1 = $user['level']+1;
			break;
			case 5 :
				$min1 = $user['level']-1;
				$max1 = $user['level'];
			break;
			case 6 :
				$min1 = (int)$user['level']-1;
				$max1 = (int)$user['level']+1;
			break;
		}
		switch($_POST['levellogin2']) {
			case 0 :
				$min2 = 2;
				$max2 = 21;
			break;
			case 1 :
				$min2 = 2;
				$max2 = $user['level'];
			break;
			case 2 :
				$min2 = 2;
				$max2 = $user['level']-1;
			break;
			case 3 :
				$min2 = $user['level'];
				$max2 = $user['level'];
			break;
			case 4 :
				$min2 = $user['level'];
				$max2 = $user['level']+1;
			break;
			case 5 :
				$min2 = $user['level']-1;
				$max2 = $user['level'];
			break;
			case 6 :
				$min2 = (int)$user['level']-1;
				$max2 = (int)$user['level']+1;
			break;
		}

		/*
		if($_POST['k'])
		{
			$_POST['k'] = 4;
		} else {
			$_POST['k'] = 2;
		}
		*/
		$_POST['k'] = 2;
		/*
		if($_POST['travma'])
		{
			$blood = 1;
		} else {
			$blood = 0;
		}
		*/
		$blood = 0;
		$klan1=0;
		$reklan1=0;
		$klan2=0;
		$reklan2=0;

		$alig1=0;
		$alig2=0;

		if (($_POST['klanonly']||$_POST['klanrecrut']) and ($user['klan']!='') )
			{
			$myklan=mysql_fetch_array(mysql_query("select * from oldbk.clans where short='{$user['klan']}'"));
			}

		if (($_POST['klanonly']) and ($user['klan']!='') )
			{

			if ($myklan['id']>0)
				{
				$klan1=$myklan['id'];
				}
			}

		if (($_POST['klanrecrut']) and ($user['klan']!='') )
			{

			if ($myklan['id']>0)
				{
					if ($myklan['rekrut_klan']>0)
						{
						$reklan1=$myklan['rekrut_klan'];
						}
					elseif ($myklan['base_klan']>0)
						{
						$reklan1=$myklan['base_klan'];
						}
				}
			}

		if (($_POST['alignonly']>=1) and ($_POST['alignonly']<=4))
			{
			$alig2=(int)$_POST['alignonly'];
			}

		if ($_POST['enklannonly'])
			{
			$enklanid=(int)($_POST['enklannonly']);
			$enklan=mysql_fetch_array(mysql_query("select * from oldbk.clans where id='{$enklanid}'"));
				if ($enklan['id']>0)
					{
					$klan2=$enklan['id'];
					if ($_POST['enklanrecrut'])
						{
							if ($enklan['rekrut_klan']>0)
								{
								$reklan2=$enklan['rekrut_klan'];
								}
							elseif ($enklan['base_klan']>0)
								{
								$reklan2=$enklan['base_klan'];
								}
						}
					}
			}


		if ($_POST['nlogin1'] == 0 OR $_POST['nlogin2'] == 0)
		{
		echo "Недопустимое количество участников!";
		}
		else
		{
		echo addzayavka ((int)($_POST['startime'])/60,(int)($_POST['timeout']), $_POST['nlogin1'], $_POST['nlogin2'], (int)($_POST['k']), $min1, $min2, $max1, $max2, $_POST['cmt'], $user, $myeff , 4, 0, $blood,0,0,0,0,0,0,0,0,0,$klan1,$reklan1,$klan2,$reklan2,$alig1,$alig2);
		$zay_array=getlist(4,$_SESSION['view']);
		}
	echo "</b></font>";
	}

	else
	/////////////////////////////////

	/////////////////////////////////
	/////// Подача груп заявки Футбол ///////
	/////////////////////////////////
	if($_POST['openfifa'] && !$user['zayavka'])
	{
		echo "<font color=red><b>";
		switch($_POST['levellogin1']) {
			case 0 :
				$min1 = 2;
				$max1 = 21;
				$min2 = 2;
				$max2 = 21;
			break;
			case 1 :
				$min1 = 2;
				$max1 = $user['level'];
				$min2 = 2;
				$max2 = $user['level'];

			break;
			case 2 :
				$min1 = 2;
				$max1 = $user['level']-1;
				$min2 = 2;
				$max2 = $user['level']-1;
			break;
			case 3 :
				$min1 = $user['level'];
				$max1 = $user['level'];
				$min2 = $user['level'];
				$max2 = $user['level'];
			break;
			case 4 :
				$min1 = $user['level'];
				$max1 = $user['level']+1;
				$min2 = $user['level'];
				$max2 = $user['level']+1;
			break;
			case 5 :
				$min1 = $user['level']-1;
				$max1 = $user['level'];
				$min2 = $user['level']-1;
				$max2 = $user['level'];
			break;
			case 6 :
				$min1 = (int)$user['level']-1;
				$max1 = (int)$user['level']+1;
				$min2 = (int)$user['level']-1;
				$max2 = (int)$user['level']+1;
			break;
			// KLANNNNNNNNNNNNNN
			case 99 :
				$min1 = 99;
				$max1 = 99;
				$min2 = 99;
				$max2 = 99;
			break;
		}

			$_POST['k'] = 20; // тип fifa
			$blood = 0; // все не кровавые

		if ((int)$_POST['nlogin1']==5)
		{
			$_POST['nlogin1'] = 5;
			$_POST['nlogin2'] = 5;
		}
		else
		{
			$_POST['nlogin1'] = 11;
			$_POST['nlogin2'] = 11;
		}
/*
		$_POST['price'] = round($_POST['price']);
		if($_POST['price']<0) {$_POST['price'] = 0;}
		if($_POST['price']<5 && $_POST['price']>0) {$_POST['price'] = 5;}
		if($_POST['price']>50) {$_POST['price'] = 50;}
		$price=$_POST['price'];
*/		$price=0;

		echo addzayavka ($_POST['startime']/60,$_POST['timeout'], $_POST['nlogin1'], $_POST['nlogin2'], $_POST['k'], $min1, $min2, $max1, $max2, $_POST['cmt'], $user, $myeff , 4, 0, $blood ,$price,1);
		$zay_array=getlist(4,$_SESSION['view']);
	}
	/////////////////////////////////



	echo "</font></b><INPUT TYPE=hidden name=level value=group>";
	echo '<table cellspacing=0 cellpadding=0><tr><td>';


	if( ustatus($user)==0 )
	{
		echo 'В тренировочных боях не начисляется опыт и не выдается репутация.<br>';
		echo '<INPUT TYPE=hidden name=level value=group><INPUT class="button-big btn" TYPE=submit value="Подать новую заявку" name=open1>';
	}
	else if ( (ustatus($user)>0) and 	(is_array($zay_array[$user[zayavka]])) )
	{
			echo "<B>Ожидаем начала тренировочного боя...</B><BR>Бой начнется через: ".round((($zay_array[$user['zayavka']]['start'])-time())/60,1)." мин.";
	}
	echo '</td></tr></table></TD><TD align=right valign=top rowspan=2><INPUT class="button-mid btn" TYPE=submit name=tmp value="Обновить"><BR><FIELDSET style="width:150px;"><LEGEND>Показывать заявки</LEGEND><table cellspacing=0 cellpadding=0 ><tr><td width=1%><input type=radio name=view value="'.$user['level'].'" '.(($_SESSION['view'] == $user[level])?"checked":"").'></td><td>моего уровня</td></tr><tr><td><input type=radio name=view value="0" '.(($_SESSION['view'] == null || $_SESSION['view'] == 0)?"checked":"").'></td><td>все</td></tr></table></FIELDSET>';
	echo '<tr><td width=85%>';


	if ($zay_array)
	{
	if ($user[zayavka]==0)	echo '<BR><INPUT class="button-big btn" TYPE=submit value="Принять участие в мясорубке" NAME=goconfirm><BR>';

		foreach ( $zay_array as $k => $v )
			{
			echo showgroup($v);
			}

	if ($user[zayavka]==0)	echo '<INPUT class="button-big btn" TYPE=submit value="Принять участие в мясорубке" NAME=goconfirm>';
	}
	echo '</td></tr></table>';
}
else
if ($_REQUEST['level'] == 'grclass')  ////////////////////////////////////////////////////////// БОИ КЛАССОВ
{

?>
<script type="text/javascript">
  function getXmlHttp()
  {
    var xmlhttp;
    try {
      xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
      try {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (E) {
        xmlhttp = false;
      }
    }
    if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
      xmlhttp = new XMLHttpRequest();
    }
    return xmlhttp;
  }
  function changeAlign(id)
  {
    var xmlhttp = getXmlHttp();
    xmlhttp.open('POST', 'zayavka.php', true);
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xmlhttp.send("get_clans_by_align=" + encodeURIComponent(id));
    xmlhttp.onreadystatechange = function() {
      if (xmlhttp.readyState == 4) {
        if(xmlhttp.status == 200) {
          var clandata = JSON.parse(xmlhttp.responseText);
          var text = "<option value='0'>Любой</option>";
          for (var i in clandata) {
            text += "<option value='" + clandata[i]['id'] + "'>" + clandata[i]['name'] + "</option>";
          }

          document.getElementById('enklannonly').innerHTML = text;
        }
      }
    };
  }

  function myClan()
  {
	if (document.getElementById('klanonly').checked)
	{
	document.getElementById("klanrecrut").disabled = false;
	}
	else
	{
	document.getElementById("klanrecrut").disabled = true;
	}
  }

</script>


<?

	/*
	if (($user['klan']!='radminion') AND ($user['klan']!='testTest') )
	{
		echo "<BR><BR><BR><CENTER><B>временно не работают</b></CENTER>";
		zaydie();
	}
	*/

$zay_array=getlist(7,$_SESSION['view']);


	if($user['level']<8) {
		echo "<BR><BR><BR><CENTER><B>Бои классов доступны с 8 уровня.</b></CENTER>";
		zaydie();
	}

	if($user['uclass']==0) {
		echo "<BR><BR><BR><CENTER><B>Бои классов доступны персонажам с установленным классом.</b></CENTER>";
		zaydie();
	}

	if (isset($myeff[830])) {
		echo "<BR><BR><BR><CENTER><B>Вы находитесь под медитацией!</b></CENTER>";
		zaydie();
	}


	if($_POST['open1'] && !$user['zayavka']) {
	//Таймаут <SELECT NAME=timeout><OPTION value=3>3 мин.<OPTION value=4>4 мин.<OPTION value=5 selected>5 мин.<OPTION value=7>7 мин.<OPTION value=10>10 мин.</SELECT>
		echo '
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top">
			<TABLE border=0><TR><TD>


			<H3>Подать заявку на бой классов</H3>
			Начало боя через <SELECT NAME=startime>

			<option value=300 selected>5 минут
			<option value=600>10 минут
			<option value=900 >15 минут

			</SELECT>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<BR><BR>
			Ваша команда <INPUT TYPE=text NAME=nlogin1 size=3 maxlength=2 value=3> бойцов <small>(мин.  в команде 3)</small><BR>';
				if ($user['klan']!='')
				{
				echo '<INPUT TYPE=checkbox NAME=klanonly id=klanonly onClick="myClan();"> Только мой клан<BR>'; //<подхватываем клан подающего заявку> (неактивная опция для персонажей без клана)
				echo '<INPUT TYPE=checkbox NAME=klanrecrut id=klanrecrut disabled = true> Разрешить заходить бойцам из основного и рекрут кланов<BR>'; //(неактивная опция для персонажа без клана)
				}

			echo 'Уровни союзников &nbsp;&nbsp;<SELECT NAME=levellogin1>
			<option value=0>только моего уровня
			<option value=1>мой уровень +1
			</SELECT>
			<BR>
			<BR>
			Противники &nbsp;&nbsp;<INPUT TYPE=text NAME=nlogin2 size=3 maxlength=2 value=3> бойцов <small>(мин.  в команде 3)</small><BR>
			Только со склонностью:
			<SELECT NAME=alignonly  onchange="changeAlign(this.value);">
			<option value=1>Любой
			<option value=2>Нейтралы
			<option value=3>Тьма
			<option value=4>Свет
			</SELECT> <br>
			Только из клана: <SELECT NAME=enklannonly id=enklannonly >';

				$kln=mysql_query("SELECT id, short   from oldbk.clans where id not in (78,81,34,417,418,419,420,421,422,423,280,260,279,433) and time_to_del=0 order by short ");
				$outarray=array();
				echo '<option value=0>Любой';
				while ($clns = mysql_fetch_array($kln))
				{
				echo '<option value='.$clns['id'].'>'.$clns['short'];
				}

			 echo '</SELECT><br>

			<INPUT TYPE=checkbox NAME=enklanrecrut> Разрешить заходить бойцам из основного и рекрут кланов<br>

			Уровни противников <SELECT NAME=levellogin2>
			<option value=0>только моего уровня
			<option value=1>мой уровень +1
			</SELECT><BR>';

			//echo '<INPUT TYPE=checkbox NAME=k> Кулачный бой (<font class=dsc>не учитывается в квестах и не получает статус</font>)<BR>';
			//echo '<INPUT TYPE=checkbox NAME=travma> Бой без правил (<font class=dsc>проигравшая сторона получает инвалидность</font>)<BR>';

			echo 'Комментарий к бою <INPUT TYPE=text NAME=cmt maxlength=40 size=40>
			</TD></TR>
			<TR><TD align=center>
			<INPUT class="button-big btn" TYPE=submit value="Создать заявку" name=open>
			</TD></TR>
			</TABLE>

			</td>
			<td valign="top"><INPUT TYPE=hidden name=level value=grclass>
			</FORM>';

			zaydie();
	}
	else
	if($_POST['goconfirm'] && !$user['zayavka'] && !$_POST['tmp'])
	{
	$zid=(int)($_POST['gocombat']);
		if (is_array($zay_array[$zid]))
		{
				$klns_data=array();
				$infklan_data=array();
				if ($zay_array[$zid]['klan1']>0) $klns_data[]=$zay_array[$zid]['klan1'];
				if ($zay_array[$zid]['klan2']>0) $klns_data[]=$zay_array[$zid]['klan2'];
				if ($zay_array[$zid]['reklan1']>0) $klns_data[]=$zay_array[$zid]['reklan1'];
				if ($zay_array[$zid]['reklan2']>0) $klns_data[]=$zay_array[$zid]['reklan2'];

				if (count($klns_data)>0)
					{
						$get_all=mysql_query("select *  from oldbk.clans where id in (".implode(",",$klns_data).")");
						if (mysql_num_rows($get_all)>0)
						{
							while ($rw = mysql_fetch_assoc($get_all))
								{
								$infklan_data[$rw['id']]='<img src="http://i.oldbk.com/i/align_'.$rw['align'].'.gif"  width=12 height=15><A HREF="http://oldbk.com/encicl/klani/clans.php?clan='.$rw['short'].'" target=_blank><img src="http://i.oldbk.com/i/klan/'.$rw['short'].'.gif" title="'.$rw['name'].'" >'.$rw['name'].'</A>';
								}
						}
					}




		echo '<TABLE width=100%><TR><TD>';
		echo "<B>Ожидаем начала боя классов...</B><BR>Бой начнется через: ".round((($zay_array[$zid]['start'])-time())/60,1)." мин.";
		echo '</TD><TD align=right><INPUT class="button-mid btn" TYPE=submit value="Вернуться"></TD></TR></TABLE><H3>На чьей стороне будете сражаться?</H3><center>
				<TABLE align=center cellspacing=4 cellpadding=1><TR><TD bgcolor=99CCCC><B>Группа 1:</B><BR>
				Максимальное кол-во: '.$zay_array[$zid]['t1c'].'<BR>
				Ограничения по уровню: '.($zay_array[$zid]['t1min']==99?'клан':$zay_array[$zid]['t1min']." - ".$zay_array[$zid]['t1max']);
				if ($zay_array[$zid]['klan1']>0)
							{
							echo '<br>Клан:'.$infklan_data[$zay_array[$zid]['klan1']];
							}
				if ($zay_array[$zid]['reklan1']>0)
							{
							echo '<br>Клан:'.$infklan_data[$zay_array[$zid]['reklan1']];
							}
				echo '</TD><TD bgcolor=99CCCC><B>Группа 2:</B><BR>
				Максимальное кол-во: '.$zay_array[$zid]['t2c'].'<BR>
				Ограничения по уровню: '.($zay_array[$zid]['t2min']==99?'клан':$zay_array[$zid]['t2min']." - ".$zay_array[$zid]['t2max']);

				if ($zay_array[$zid]['alig2']>1)
					{
					//		<option value=1>Любой
					$ov[2]=' <img src="http://i.oldbk.com/i/align_2.gif" title="Нейтралы" width=12 height=15> Нейтралы ';
					$ov[3]=' <img src="http://i.oldbk.com/i/align_3.gif" title="Тьма" width=12 height=15> Тьма ';
					$ov[4]=' <img src="http://i.oldbk.com/i/align_6.gif" title="Свет" width=12 height=15> Свет';
					echo '<br>Только склонность:'.$ov[$zay_array[$zid]['alig2']];
					}

				if ($zay_array[$zid]['klan2']>0)
							{
							echo '<br>Клан:'.$infklan_data[$zay_array[$zid]['klan2']];
							}
				if ($zay_array[$zid]['reklan2']>0)
							{
							echo '<br>Клан:'.$infklan_data[$zay_array[$zid]['reklan2']];
							}

				echo '</TD></TR><TR>
				<TD align=center>';
		echo BNewRender($zay_array[$zid][t1hist]);
		echo '</TD><TD align=center>';
		echo BNewRender($zay_array[$zid][t2hist]);
		echo '</TD></TR><TR><TD align=center><INPUT class="button-mid btn" TYPE=submit name=confirm1 value="Я за этих!"></TD>
		<TD align=center><INPUT class="button-mid btn" TYPE=submit name=confirm2 value="Я за этих!"></TD></TR></TABLE></center>
		<INPUT TYPE=hidden name=gocombat value="'.$zid.'"><INPUT TYPE=hidden name=level value=grclass>';
		zaydie();
		}
	}
	else
	// in da battle
	if(($_POST['confirm1']) && $_POST['gocombat'] && !$user['zayavka'] && !$_POST['tmp'])
	{
	$zid=(int)($_POST['gocombat']);
		if (is_array($zay_array[$zid]))
		{
		echo "<font color=red><b>";
		$OKADD=false;
                if (chck_items_prem_classes(true)==true) // проверка на удержание
                	{
			echo addteam (1,$user,$myeff,$zay_array[$zid], $zid);
			if ($OKADD)
				{
				$zay_array=getlist(7,$_SESSION['view']); //перечитываем
				}
			}
		echo "</b></font>";
		}
	}
	else
	if(($_POST['confirm2']) && $_POST['gocombat'] && !$user['zayavka'] && !$_POST['tmp'])
	{
	$zid=(int)($_POST['gocombat']);
		if (is_array($zay_array[$zid]))
		{
		echo "OK";
		echo "<font color=red><b>";
		$OKADD=false;

	                if (chck_items_prem_classes(true)==true) // проверка на удержание
                     	{
			echo addteam (2,$user,$myeff,$zay_array[$zid], $zid);
			if ($OKADD)
				{
				$zay_array=getlist(7,$_SESSION['view']); //перечитываем
				}
			}
		echo "</b></font>";
		}
	}
	else
	/////////////////////////////////
	/////// Подача 	заявки ///////
	if($_POST['open'] && !$user['zayavka'])
	{
		echo "<font color=red><b>";

		if (!(($_POST['levellogin1']>=0) and ($_POST['levellogin1']<=1) )) $_POST['levellogin1']=0;
		if (!(($_POST['levellogin2']>=0) and ($_POST['levellogin2']<=1) )) $_POST['levellogin2']=0;


		switch($_POST['levellogin1']) {
			case 0 :
				$min1 = $user['level'];
				$max1 = $user['level'];
			break;
			case 1 :
				$min1 = $user['level'];
				$max1 = $user['level']+1;
			break;
		}
		switch($_POST['levellogin2']) {
			case 0 :
				$min2 = $user['level'];
				$max2 = $user['level'];
			break;
			case 1 :
				$min2 = $user['level'];
				$max2 = $user['level']+1;
			break;
		}


		$_POST['k'] =3;

		$blood = 0;
		$klan1=0;
		$reklan1=0;
		$klan2=0;
		$reklan2=0;

		$alig1=0;
		$alig2=0;

		if (($_POST['klanonly']||$_POST['klanrecrut']) and ($user['klan']!='') )
			{
			$myklan=mysql_fetch_array(mysql_query("select * from oldbk.clans where short='{$user['klan']}'"));
			}

		if (($_POST['klanonly']) and ($user['klan']!='') )
			{

			if ($myklan['id']>0)
				{
				$klan1=$myklan['id'];
				}
			}

		if (($_POST['klanrecrut']) and ($user['klan']!='') )
			{

			if ($myklan['id']>0)
				{
					if ($myklan['rekrut_klan']>0)
						{
						$reklan1=$myklan['rekrut_klan'];
						}
					elseif ($myklan['base_klan']>0)
						{
						$reklan1=$myklan['base_klan'];
						}
				}
			}

		if (($_POST['alignonly']>=1) and ($_POST['alignonly']<=4))
			{
			$alig2=(int)$_POST['alignonly'];
			}

		if ($_POST['enklannonly'])
			{
			$enklanid=(int)($_POST['enklannonly']);
			$enklan=mysql_fetch_array(mysql_query("select * from oldbk.clans where id='{$enklanid}'"));
				if ($enklan['id']>0)
					{
					$klan2=$enklan['id'];
					if ($_POST['enklanrecrut'])
						{
							if ($enklan['rekrut_klan']>0)
								{
								$reklan2=$enklan['rekrut_klan'];
								}
							elseif ($enklan['base_klan']>0)
								{
								$reklan2=$enklan['base_klan'];
								}
						}
					}
			}


		if ((int)$_POST['nlogin1'] < 3 OR (int)$_POST['nlogin2'] < 3)
		{
		echo "Недопустимое количество участников!";
		}
		elseif (chck_items_prem_classes(true)==true) // проверка на удержание
		{

		echo addzayavka ((int)($_POST['startime'])/60,(int)($_POST['timeout']), (int)$_POST['nlogin1'], (int)$_POST['nlogin2'], (int)($_POST['k']), $min1, $min2, $max1, $max2, $_POST['cmt'], $user, $myeff , 7, 0, $blood,0,0,0,0,0,0,0,0,0,$klan1,$reklan1,$klan2,$reklan2,$alig1,$alig2);
		$zay_array=getlist(7,$_SESSION['view']);
		}
	echo "</b></font>";
	}

	echo "</font></b><INPUT TYPE=hidden name=grclass value=group>";
	echo '<table cellspacing=0 cellpadding=0><tr><td>';


	if( ustatus($user)==0 )
	{
		echo '<br>Участвовать в боях классов могут только персонажи с установленным классом и в обмундировании, имеющем требование класса (кроме футболок, плащей и рун). Если на начало боя на персонаже есть предметы без требований класса, все предметы экипировки с персонажа автоматически снимаются.<br><br>';
		echo '<INPUT TYPE=hidden name=grclass value=group><INPUT class="button-big btn" TYPE=submit value="Подать новую заявку" name=open1>&nbsp;&nbsp;&nbsp;';
		echo "<INPUT TYPE=button class=\"button-big btn\" value=\"Примерочная\" onclick=\"location.href='class_armory.php?tmp=".mt_rand(111111,999999)."';\">";
	}
	else if ( (ustatus($user)>0) and 	(is_array($zay_array[$user[zayavka]])) )
	{
			echo "<B>Ожидаем начала боя...</B>";
			if (isset($_GET['zerror'])) 			echo "<FONT color=red><B>Вы находитесь в заявке на бой, переодевание недоступно...</B></font>";

			echo "<BR>Бой начнется через: ".round((($zay_array[$user['zayavka']]['start'])-time())/60,1)." мин.";
	}
	echo '</td></tr></table></TD><TD align=right valign=top rowspan=2><INPUT class="button-mid btn" TYPE=submit name=tmp value="Обновить"><BR><FIELDSET style="width:150px;"><LEGEND>Показывать заявки</LEGEND><table cellspacing=0 cellpadding=0 ><tr><td width=1%><input type=radio name=view value="'.$user['level'].'" '.(($_SESSION['view'] == $user[level])?"checked":"").'></td><td>моего уровня</td></tr><tr><td><input type=radio name=view value="0" '.(($_SESSION['view'] == null || $_SESSION['view'] == 0)?"checked":"").'></td><td>все</td></tr></table></FIELDSET>';
	echo '<tr><td width=85%>';


	if ($zay_array)
	{
	if ($user[zayavka]==0)	echo '<BR><INPUT class="button-big btn" TYPE=submit value="Принять участие в мясорубке" NAME=goconfirm><BR>';

		foreach ( $zay_array as $k => $v )
			{
			echo showgroup($v);
			}

	if ($user[zayavka]==0)	echo '<INPUT class="button-big btn" TYPE=submit value="Принять участие в мясорубке" NAME=goconfirm>';
	}
	echo '</td></tr></table>';
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
else
if ($_REQUEST['level'] == 'haos')
{
if ($user[id]==14897) 	print_r($_POST);
$zay_array=getlist(5,$_SESSION['view']);
	////////////////////////////
	if($user['level']<2)
	{
		echo "<BR><BR><BR><CENTER><B>Хаотичные бои доступны с 2 уровня.</b></CENTER>";
		zaydie();
	}
	else
	if($user['align']==5)
	{
		echo "<BR><BR><BR><CENTER><B>Хаотичные бои не доступны для Абсолютного хаоса.</b></CENTER>";
		zaydie();
	}
	else
	if (isset($myeff[830]))
	{
		echo "<BR><BR><BR><CENTER><B>Вы находитесь под медитацией!</b></CENTER>";
		zaydie();
	}
	/////////////////////////
	$nomagic = 0;
	$in_att=0;
	$ch_levl=5;

	 if (!((time()>$KO_start_time19) and (time()<$KO_fin_time19)))
	{

		if ($_POST['levellogin1']==10)
		{
		$_POST['levellogin1']=0;
		}
	}
	else	if (!(((time()>$KO_start_time22) and (time()<$KO_fin_time22)) OR ((time()>mktime(0,0,0,3,7,2018) ) and (time()<mktime(23,59,59,3,16,2018) )) ) )
	{
		if ($_POST['levellogin1']==11)
		{
		$_POST['levellogin1']=0;
		}
	}


	/*
		$dayof=date("w");
		$h=date("G");
		//заявки на велики
	 if ( ( (($dayof==0) or ($dayof==6))  and ($h>=14) and ($h<=18) )  OR       // в выходные дни 14:00-18:00
	      ( (($dayof>=1) and ($dayof<=5)) and ($h>=19) and ($h<=22) )  )  //     в будние 19:00-22:00
	      	{
		      	if ($_POST['levellogin1']==0)
		      		{
		      		$_POST['levellogin1']=6;
		      		}
	      	}
	*/


	if($_POST['open'] && !$user['zayavka'])
	{
		switch($_POST['levellogin1']) {
			case 0 :
				$min1 = 1;
				$max1 = 21;
				$all_pip=150;
				$in_att=1;
			break;
			case 3 :
				$min1 = $user['level'];
				$max1 = $user['level'];
				$all_pip=50;
			break;
			case 6 :
				$min1 = (int)$user['level']-1;
				$max1 = (int)$user['level']+1;
				$all_pip=50;
			break;
			case 10 :
				$min1 = 1;
				$max1 = 21;
				$all_pip=100;
				$in_att=1;
				$ch_levl=10;
			break;
			case 11 :
				$min1 = 1;
				$max1 = 21;
				$all_pip=100;
				$in_att=1;
				$ch_levl=11;
			break;
		}

		if($_POST['travma'])
		{
			$blood = 1;
		} else {
			$blood = 0;
		}

/*
		$_POST['price'] = round($_POST['price']);
		if($_POST['price']<0) {$_POST['price'] = 0;}
		if($_POST['price']<5 && $_POST['price']>0) {$_POST['price'] = 5;}
		if($_POST['price']>50) {$_POST['price'] = 50;}
*/		$_POST['price'] = 0;

		if ( $_POST['autoblow']=='on')
			{
			$autob=1;
			} else
			{
			$autob=0;
			}


//		if($_POST['nomagic'])
//		{
//			if($_POST['price'] > 0) {$nomagic = 1;} else{$nomagic = 0;}
//		} else {
//			$nomagic = 0;
//		}

		$hrandom=0;
		if ($_POST['hrandom'])
			{
			$hrandom=1;
			}

		if ($_POST['classbattle'])
			{
			$ch_levl=12;
			}


		echo "<font color=red><b>";
		$coment=($_POST['cmt']);
		$coment = str_ireplace("автозаявка", "",$coment);
		$coment = str_ireplace("дух", "",$coment);

		echo addzayavka ((int)($_POST['startime2'])/60,(int)($_POST['timeout']), $all_pip, $all_pip, (int)($_POST['k']), $min1, $min1, $max1, $max1,$coment, $user, $myeff , $ch_levl, 0, $blood, $_POST['price'], $nomagic,$autob,0,0,0,0,$in_att,$hrandom);
		echo "</b></font>";
		$zay_array=getlist(5,$_SESSION['view']);
	}
	else
	if($_POST['confirm2'] && $_POST['gocombat'] && !$user['zayavka'] && !$_POST['tmp'])
	{
	$zid=(int)($_POST['gocombat']);
		if (is_array($zay_array[$zid]))
		{
		echo "<font color=red><b>";
		$OKADD=false;
		echo addteam (1,$user,$myeff,$zay_array[$zid], $zid);
		if ($OKADD)
			{
			$zay_array=getlist(5,$_SESSION['view']);
			}
		echo "</b></font>";
		}
	}


	?>
	<script>
	function confirm_fight() {
		var fight_id = get_radio_value();
		var elPrice = document.getElementById('price'+fight_id);
		if(!elPrice)
		{
			document.getElementById('zay').submit();
		}

		var fight_price = elPrice.value;
		if(fight_id <= 0) {alert('Не выбран поединок!');}
		if(fight_price > 0) {
			if(confirm('Уверены что хотите заплатить '+fight_price+' кр. за участие в поединке?')) {
				document.getElementById('zay').submit();
			}
		} else {
			document.getElementById('zay').submit();
		}
	}
	function get_radio_value()
	{
	for (var i=0; i < document.zay.gocombat.length; i++)
	   {
	   if (document.zay.gocombat[i].checked)
		  {
			return document.zay.gocombat[i].value;
		  }
	   }
	   return 0;
	}

	function showinf(){
	var val = document.getElementById('htype').value ;
	if (val==5)
		{
		document.getElementById('kulakinf').style.display = "block";
		}
		else
		{
		document.getElementById('kulakinf').style.display = "none";
		}
	}

	</script>

	<?
	$captcha_time=true;

	$load_captime=mysql_fetch_array(mysql_query("select UNIX_TIMESTAMP(captime) as captime from users_capcha_time where owner='{$user['id']}' "));
	if (time()-$load_captime['captime'] < 3600) //6 ч.
			{
			//непоказываем капчу
			$captcha_time=false;
			}

	if ($user[prem]==3)
		{
		$captcha='';
		}
		else
		{
			if ($captcha_time)
				{
				$captcha = '<img src="sec1.php?battle=1&nc1='.mt_rand(0,mt_getrandmax()).'&nc2='.mt_rand(0,mt_getrandmax()).'" border="1"> Код подтверждения: <input type="text" name="securityCode" value="" maxlength=3 style="FONT-FAMILY: verdana; width:40px">';
				}
				else
				{
				$captcha='';
				}
		}

	echo '<table cellspacing=0 cellpadding=0><tr><td>';


	if( ustatus($user)==0 )
	{

//		$money = 'Бой на деньги: <SELECT NAME=price><OPTION value=0>нет<OPTION value=5>5 кр.<OPTION value=10>10 кр.<OPTION value=25>25 кр.<OPTION value=50>50 кр.</SELECT> / <INPUT TYPE=checkbox NAME=nomagic checked> если отмечено - без магии и без боевых абилити(только бои на деньги)<br>';
//Таймаут <SELECT NAME=timeout><OPTION value=3 SELECTED>3 мин.<OPTION value=5>5 мин.<OPTION value=10>10 мин.</SELECT>
		echo 'Хаотичный бой - разновидность группового, где группы формируются автоматически. Бой не начнется, если собралось меньше 4-х человек.
		<DIV id="dv2" style="display:"><A href="#" onclick="dv1.style.display=\'\'; dv2.style.display=\'none\'; return false">Подать заявку на хаотичный бой</A></DIV>
		<DIV id="dv1" style="display: none">
		<FIELDSET><LEGEND><B>Подать заявку на хаотичный бой</B> </LEGEND>
		Начало боя через <SELECT NAME=startime2><option value=300  selected>5 минут<option value=600 >10 минут<option value=900>15 минут</SELECT>&nbsp;&nbsp;&nbsp;&nbsp;
		<BR>
		Уровни бойцов &nbsp;&nbsp;
		<SELECT NAME=levellogin1>';

		if ((time()>$KO_start_time19) and (time()<$KO_fin_time19))
		{
		echo '<option value=10>Бой на елках, любой уровень (до 100 игроков в заявке, без вмешательства)';
		}
		if (((time()>$KO_start_time22) and (time()<$KO_fin_time22)) OR ((time()>mktime(0,0,0,3,7,2018) ) and (time()<mktime(23,59,59,3,16,2018) )) )
		{
		echo '<option value=11>Бой на букетах, любой уровень (до 100 игроков в заявке, без вмешательства)';
		}

	/*
	$dayof=date("w");
	$h=date("G");
	//заявки на велики
	 if ( ( (($dayof==0) or ($dayof==6))  and ($h>=14) and ($h<=18) )  OR       // в выходные дни 14:00-18:00
	      ( (($dayof>=1) and ($dayof<=5)) and ($h>=19) and ($h<=22) )  )  //     в будние 19:00-22:00
	      	{
	      	}
	 */
	echo ' <option value=0>любой уровень (до 150 игроков в заявке, без вмешательства)';
	echo '
		<option value=3>только моего уровня (до 50 игроков в заявке)
		<option value=6 selected>мой уровень +/- 1 (до 50 игроков в заявке)</SELECT><BR><BR>
		<table border=0>
			<tr>
			<td>Тип боя <SELECT id=htype NAME=k onchange="showinf()" ><OPTION value=3>с оружием<OPTION value=5>кулачный </SELECT></td>
			<td><span id="kulakinf" style="display: none;"><small>(не учитывается в квестах и не получает статус)</small></span></td>
			</tr>
		</table>
			<INPUT TYPE=checkbox NAME=autoblow> Бой c автоударом <BR>';
		if ($user[level]>=5) { echo '<INPUT TYPE=checkbox NAME=travma> Бой без правил (<font class=dsc>проигравшая сторона получает инвалидность</font>)<BR>'; }

		//if ($user['klan']=='radminion')
		{
		echo '<INPUT TYPE=checkbox NAME=hrandom checked> Cлучайное распределение (<font class=dsc>параметры персонажа не учитываются при формировании групп</font>)<BR>';
		}

		//if (($user['klan']=='radminion') OR  ($user['klan']=='testTest') )
		{
		echo '<INPUT TYPE=checkbox NAME=classbattle > Классовый бой (<font class=dsc>Могут принимать участие персонажи с классом</font>)<BR>';
		}

		echo 'Комментарий к бою <INPUT TYPE=text NAME=cmt maxlength=40 size=40><BR>';

		echo $money.''.$captcha.'
		<INPUT class="button-big btn" TYPE=submit name=open value="Подать заявку">&nbsp;<BR></FIELDSET>
		<BR></DIV>';
	}
	elseif(( ustatus($user)>0) and (is_array($zay_array[$user[zayavka]])) )
	{

		echo "<B>Ожидаем начала группового боя...</B><BR>Бой начнется через: ".round((($zay_array[$user['zayavka']]['start']+10)-time())/60,1)." мин.";

	}
	echo '</td></tr></table></TD><TD align=right valign=top rowspan=2><INPUT class="button-mid btn" TYPE=submit name=tmp value="Обновить"><BR><FIELDSET style="width:150px;"><LEGEND>Показывать заявки</LEGEND><table cellspacing=0 cellpadding=0 ><tr><td width=1%><input type=radio name=view value="'.$user['level'].'" '.(($_SESSION['view'] == $user[level])?"checked":"").'></td><td>моего уровня</td></tr><tr><td><input type=radio name=view value="0" '.(($_SESSION['view'] == null || $_SESSION['view'] == 0)?"checked":"").'></td><td>все</td></tr></table></FIELDSET>';
	echo '<tr><td width=85%><INPUT TYPE=hidden name=level value=haos><input type="hidden" name="confirm2" value="1">';


	if($user[zayavka]==0)
	{
		if ($user[prem]!=3)
			{
			if ($captcha_time)
				{
				echo "<img src='sec1.php?battle=1&nc1=".mt_rand(0,mt_getrandmax())."&nc2=".mt_rand(0,mt_getrandmax())."' border='1'> Код подтверждения: <input type='text' name='securityCode1' value='' maxlength=3 style='FONT-FAMILY: verdana; width:40px'/>&nbsp;";
				}
			}
		echo '<INPUT style="margin-bottom:7px;" TYPE=button class="button-big btn" value="Принять участие в мясорубке" NAME=confirm2 onclick="confirm_fight();"><BR>';
	}


	if ($zay_array) foreach ( $zay_array as $k => $v )
			{

				if (($k==3045156) and ($user['klan']!='radminion'))
				{

				}
				else
				{
				echo showhaos($v);
				}
			}

	if($user[zayavka]==0)
	{
		if ($user[prem]!=3)
			{
			if ($captcha_time)
				{
				echo "<img src='sec1.php?battle=1&nc1=".mt_rand(0,mt_getrandmax())."&nc2=".mt_rand(0,mt_getrandmax())."' border='1'> Код подтверждения: <input type='text' name='securityCode2' value='' maxlength=3 style='FONT-FAMILY: verdana; width:40px'/>&nbsp;";
				}
			}
		echo '<INPUT TYPE=button class="button-big btn" value="Принять участие в мясорубке" NAME=confirm2 onclick="confirm_fight();">';
	}

	echo '</TD></TR></TABLE>';
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
else
if ($_REQUEST['tklogs'] != null)
	{
	$tttype=(int)$_REQUEST['tklogs'];
 	 if ($tttype==2)
 	 		{
 	 		//ристалка
			$data = mysql_query("SELECT * FROM `battle` WHERE `win`='3' AND ((type>=211 AND type<=282) OR (type=304) OR (type=308)   ) ORDER by `id` ASC;");
 	 		}
 	else 	 if ($tttype==3)
 	 		{
			//руины
			$data = mysql_query("SELECT * FROM `battle` WHERE `win`='3' AND (type=11 OR type=12) ORDER by `id` ASC;");
 	 		}
	else 	 if ($tttype==4)
 	 		{
 	 		//ЛАБА
			$data = mysql_query("SELECT * FROM `battle` WHERE `win`='3' AND type=30 ORDER by `id` ASC;");
 	 		}
	else 	 if ($tttype==5)
 	 		{
 	 		//БС
			$data = mysql_query("SELECT * FROM `battle` WHERE `win`='3' AND (type=10 or type = 1010) ORDER by `id` ASC;");
 	 		}
	else 	 if ($tttype==6)
 	 		{
 	 		//Загород
			$data = mysql_query("SELECT * FROM `battle` WHERE `win`='3' AND (type=13 OR type=14 OR type=15) ORDER by `id` ASC;");
 	 		}
	else 	 if ($tttype==7)
 	 		{
 	 		//замки
				$data = mysql_query("SELECT * FROM oldbk.`battle` WHERE `win`='3' AND (type=170 OR type=171 or type=172) ORDER by `id` ASC;");
 	 		}
	 else
	 {
	  $data = mysql_query("SELECT * FROM `battle` WHERE `win`='3' AND (type!=10 AND type!=11 AND type!=12 AND type!=13 AND type!=14 AND type!=15 AND type!=30 AND type< 211 and type != 170 and type != 171 and type != 172) ORDER by `id` ASC;");
	 }


	while($row = @mysql_fetch_array($data))
	 {
	if (($row[teams]!='AFB') AND ($row[teams]!='AF'))  // не показываем в текущих тестовые бои
		 {
        	// fix date
	        $k=$row['date'];   $kk=explode(" ",$k);  $d=explode("-",$kk[0]); $t=explode(":",$kk[1]);
		$mmk=mktime($t[0], $t[1], $t[2], $d[1], $d[2], $d[0]);
		//$mmk=$mmk-3600; //-1 час
		$row['date']=date("d-m-Y H:i:s", $mmk);

 		echo "<span class=date>{$row['date']}</span> ";
		if ($row['t1hist'] != "") {
			echo BNewRender($row['t1hist']);
		}

		 if (($row[type]>=247)and($row[type]<=259))
		 {
		 //ристалка
		 }
		 else
		 {

			if ($row['t1hist'] != "" && ($row['t2hist'] != "" || $row['t3hist'] != "")) {
				echo " против ";
			}
			if ($row['t2hist'] != "") {
				echo BNewRender($row['t2hist']);
			}

		if (($row[type]==61 || $row[type]==40 || $row[type]==41) and ($row[t3hist]!='')) {
			echo " против ";
			echo BNewRender($row['t3hist']);
		}
		}
		if ($row['CHAOS']==2 || $row['CHAOS']==-1)
					{
					echo "<IMG SRC=\"i/achaos.gif\" WIDTH=20 HEIGHT=20 ALT=\"бой с автоударом\">";
					}

		if ($row[blood]>0) { echo "<img src='http://i.oldbk.com/i/fighttype6.gif'>"; }

		if ($row['type']==8) { echo "<img src='http://i.oldbk.com/i/fight_flowers.png'>"; }
		elseif (($row[type]!=6)AND($row[type]!=10)AND($row[type]!=100)AND($row[coment]!="Бой склонностей")) { echo "<img src='http://i.oldbk.com/i/fighttype{$row['type']}.gif'>"; }
		elseif ($row[coment]=='Бой склонностей') {
			echo "<img src='http://i.oldbk.com/i/fighttype40.gif'>";
		}
		else { echo "<img src='http://i.oldbk.com/i/fighttype1.gif'>"; }
		if (CITY_ID == 1 && ($row['type'] == 170 || $row['type'] == 171 || $row['type'] == 172))
			{
				echo "<a href='http://capitalcity.oldbk.com/logs.php?log={$row['id']}' target=_blank>»»</a><BR>";
			} else
			{
				echo "<a href='logs.php?log={$row['id']}' target=_blank>»»</a><BR>";
			}
		 }
	echo "<hr style='margin:5px;margin-bottom:0px;'>";
	}
}
// zavershonki
else
if ($_REQUEST['logs'] != null)
	{
	echo '<TABLE width=100% cellspacing=0 cellpadding=0><TR>
			<TD valign=top>&nbsp;<A HREF="zayavka.php?logs='.
			date("d.m.y",mktime(0, 0, 0, substr($_REQUEST['logs'],3,2), substr($_REQUEST['logs'],0,2)-1, "20".substr($_REQUEST['logs'],6,2)))
			.'&filter='.(($_REQUEST['filter'])?$_REQUEST['filter']:$user['login']).'">« Предыдущий день</A></TD>
			<TD valign=top align=center><H3>Записи о завершенных боях за '.(($_REQUEST['logs']!=1)?"{$_REQUEST['logs']}":"".date("d.m.y")).'</H3></TD>
			<TD  valign=top align=right><A HREF="zayavka.php?logs='.
			date("d.m.y",mktime(0, 0, 0, substr($_REQUEST['logs'],3,2), substr($_REQUEST['logs'],0,2)+1, "20".substr($_REQUEST['logs'],6,2)))
			.'&filter='.(($_REQUEST['filter'])?$_REQUEST['filter']:$user['login']).'">Следующий день »</A>&nbsp;</TD>
			</TR><TR><TD colspan=3 align=center>Показать только бои персонажа: <INPUT TYPE=text NAME=filter value="'.(($_REQUEST['filter'])?$_REQUEST['filter']:$user['login']).'"> за <INPUT TYPE=text NAME=logs size=12 value="'.(($_REQUEST['logs']!=1)?"{$_REQUEST['logs']}":"".date("d.m.y")).'"> <INPUT class="button-mid btn" TYPE=submit value="фильтр!"></TD>
			</TR></TABLE>';

	$u = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `login` = '".(($_REQUEST['filter'])?"{$_REQUEST['filter']}":"{$user['login']}")."' LIMIT 1;"));
	//переменная для проверки границы
	$DHIST1=mktime('00','00','00',substr($_REQUEST['logs'],3,2),substr($_REQUEST['logs'],0,2),substr($_REQUEST['logs'],6,2));
	$DHIST2=mktime('23','59','59',substr($_REQUEST['logs'],3,2),substr($_REQUEST['logs'],0,2),substr($_REQUEST['logs'],6,2));
	if (ADMIN || $user['align'] == "1.99") {
		$data = mysql_query("
			(select hist.owner,hist.bat_date, b.*  from battle_hist hist LEFT JOIN battle b ON b.id=hist.battle_id  where owner='{$u[id]}' and b.win!=3 and bat_date >='{$DHIST1}'  and bat_date <='{$DHIST2}')
			UNION
			(select hist.owner,hist.bat_date, b.*  from battle_hist_hidden hist LEFT JOIN battle b ON b.id=hist.battle_id  where owner='{$u[id]}' and b.win!=3 and bat_date >='{$DHIST1}'  and bat_date <='{$DHIST2}')
			ORDER BY bat_date
		");
	} else {
		$data = mysql_query("select hist.owner,hist.bat_date, b.*  from battle_hist hist LEFT JOIN battle b ON b.id=hist.battle_id  where owner='{$u[id]}' and b.win!=3 and bat_date >='{$DHIST1}'  and bat_date <='{$DHIST2}' ");
	}



	while($row = @mysql_fetch_array($data))
	{
		if (($row[teams]=='AFB') and ($user['klan']!='radminion')) continue; // непоказываем не админам тестовые бои

		if ($row['type'] == 601 || $row['type'] == 602 || $row['type'] == 603 || $row['type'] == 604) $row['win'] = 2;

		// fix date
          	$k=$row['date'];   $kk=explode(" ",$k);  $d=explode("-",$kk[0]); $t=explode(":",$kk[1]);
	  	$mmk=mktime($t[0], $t[1], $t[2], $d[1], $d[2], $d[0]);
		// $mmk=$mmk-3600; //-1 час
	  	$row['date']=date("Y-m-d H:i:s", $mmk);

		echo "<span class=date>{$row['date']}</span> ";

		if ($row["t1hist"] != "") {
        $upos = strrpos($row["t1hist"], BNewHist($u));
      if ($upos===false) //ищем тело в первой тиме
        {

                if (substr_count($row["t1hist"], '#')>=10)
                {
                  echo BNewRender($row["t1hist"],10);
                }
                else
                {
                echo BNewRender($row['t1hist']);
                }
        }
        else
        {
              if (substr_count($row["t1hist"], '#')>=2) //если больше 10 тел в бою то сокращаем
              {
                $Tshort=substr($row["t1hist"], $upos);
                echo BNewRender($Tshort,5);
              }
              else
              {
              echo BNewRender($row['t1hist']);
              }
        }

			if ($row['win'] == 1) { echo '<img src=http://i.oldbk.com/i/flag.gif>'; }
			if ($row['t2hist'] != "" || $row['t3hist'] != "") {
				echo " против ";
			}
		}

		if ($row["t2hist"] != "") {

    $upos = strrpos($row["t2hist"], BNewHist($u));
    if ($upos===false) //ищем тело в первой тиме
    {
              if (substr_count($row["t2hist"], '#')>=10)
              {
                echo BNewRender($row["t2hist"],10);
              }
              else
              {
              echo BNewRender($row['t2hist']);
              }
    }
    else
    {
          if (substr_count($row["t2hist"], '#')>=2) //если больше 10 тел в бою то сокращаем
          {
                $Tshort=substr($row["t2hist"], $upos);
                echo BNewRender($Tshort,5);
          }
          else
          {
          echo BNewRender($row['t2hist']);
          }
    }

			if ($row['win'] == 2) { echo '<img src=http://i.oldbk.com/i/flag.gif>'; }
			if ($row['t3hist'] != "") {
				echo " против ";
			}
		}

		if ($row["t3hist"] != "") {
			echo BNewRender($row['t3hist']);
			if ($row['win'] == 4) { echo '<img src=http://i.oldbk.com/i/flag.gif>'; }
		}

		if ($row['CHAOS']==2 || $row['CHAOS']==-1)
					{
					echo "<IMG SRC=\"i/achaos.gif\" WIDTH=20 HEIGHT=20 ALT=\"бой с автоударом\">";
					}

		if ($row['type'] == 20) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype20.gif\" WIDTH=20 HEIGHT=20 ALT=\"Футбольный поединок\" title=\"Футбольный поединок\"> ";
			}elseif ($row['type'] == 10) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype6.gif\" WIDTH=20 HEIGHT=20 ALT=\"Кровавый поединок\"> ";
			}elseif ($row['type'] == 30) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype30.gif\" WIDTH=20 HEIGHT=20 ALT=\"Бой в лабиринте Хаоса\"> ";
			}
			elseif ($row['blood'] && ($row['type'] == 5 OR $row['type'] == 4)) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype5.gif\" WIDTH=20 HEIGHT=20 ALT=\"кулачный бой\"><IMG SRC=\"http://i.oldbk.com/i/fighttype6.gif\" WIDTH=20 HEIGHT=20 ALT=\"Кровавый поединок\"> ";
			}
			elseif ($row['blood'] && ($row['type'] == 2 OR $row['type'] == 3 OR $row['type'] == 6)) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype6.gif\" WIDTH=20 HEIGHT=20 ALT=\"Кровавый поединок\"> ";
			}
			elseif ($row['type'] == 100) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype6.gif\" WIDTH=20 HEIGHT=20 ALT=\"Кровавый поединок (клановая битва)\"> ";
			}
			elseif ($row['type'] == 5 OR $row['type'] == 4) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype4.gif\" WIDTH=20 HEIGHT=20 ALT=\"кулачный бой\"> ";
			}
			elseif (($row['type'] == 3 && $row['coment'] != "Бой склонностей")  OR $row['type'] == 2) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype3.gif\" WIDTH=20 HEIGHT=20 ALT=\"групповой бой\"> ";
			}
			elseif ($row['type'] == 1) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype1.gif\" WIDTH=20 HEIGHT=20 ALT=\"физический бой\"> ";
			}
			elseif ($row['type'] == 7) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype7.gif\" WIDTH=20 HEIGHT=20 ALT=\"бой на елках\"> ";
			}
			elseif ($row['type'] == 40 || $row['coment'] == "Бой склонностей") {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype40.gif\" WIDTH=20 HEIGHT=20 ALT=\"противостояние\"> ";
			}
			elseif ($row['type'] == 41) {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype41.gif\" WIDTH=20 HEIGHT=20 ALT=\"кровавое противостояние\"> ";
			} else {
				$rr = "<IMG SRC=\"http://i.oldbk.com/i/fighttype1.gif\" WIDTH=20 HEIGHT=20 ALT=\"физический бой\"> ";
			}

			echo $rr;
		echo " <a href='logs.php?log={$row['id']}' target=_blank>»»</a><BR>";
		$i++;
	}
	if($i==0) {
		echo '<CENTER><BR><BR><B>В этот день не было боев, или же, летописец опять потерял свитки...</B><BR><BR><BR></CENTER>';
	}
	echo '<HR><BR>';
}

function zaydie()
{
global $miniBB_gzipper_encoding;
/////////////////////////////////////////////////////
echo "</FORM>";
echo "</div></body>";
echo "</html>";


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
die();
}

zaydie();
?>
